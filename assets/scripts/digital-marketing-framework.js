;(function () {
  // defaults //

  const EVENT_READY = 'dmf-ready'
  const EVENT_REQUEST_READY = 'dmf-request-ready'

  const CLASS_LOADING = 'loading'

  const DEFAULTS = {
    settings: {
      prefix: 'dmf',
    },
    urls: {},
    pluginSettings: {},
  }

  // state //

  const refreshCallbacks = []
  let fetchCache = {}
  let DMF = null

  // helpers //

  function ucfirst(keyword) {
    return keyword === '' ? '' : keyword[0].toUpperCase() + keyword.substring(1)
  }

  function deepExtend(out) {
    out = out || {}
    for (let i = 1; i < arguments.length; i++) {
      const obj = arguments[i]
      if (!obj) {
        continue
      }
      for (let key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
          if (typeof obj[key] === 'object' && obj[key] !== null) {
            if (obj[key] instanceof Array) out[key] = obj[key].slice(0)
            else out[key] = deepExtend(out[key], obj[key])
          } else out[key] = obj[key]
        }
      }
    }
    return out
  }

  function camelCaseToDashed(keyword) {
    return keyword.replace(/[A-Z]+/, (match) => '-' + match.toLowerCase())
  }

  function dashedToCamelCase(keyword) {
    return keyword.replace(/-([a-z0-9])/, (match, firstCharacter) => {
      return firstCharacter.toUpperCase()
    })
  }

  function fetchSettings() {
    const settingsScript = document.querySelector(
      '[data-dmf-selector="dmf-settings-json"]'
    )
    if (settingsScript) {
      try {
        return JSON.parse(settingsScript.innerHTML.trim())
      } catch (e) {
        console.error(e.message)
        return null
      }
    }
  }

  function getAjaxUrl(module, plugin) {
    if (DMF.urls[module][plugin]) {
      return DMF.urls[module][plugin]
    }
    return ''
  }

  function fetchData(module, plugin) {
    const url = getAjaxUrl(module, plugin)
    return new Promise(async function (resolve) {
      if (typeof fetchCache[url] !== 'undefined') {
        if (typeof fetchCache[url].response !== 'undefined') {
          resolve(fetchCache[url].response)
        } else {
          fetchCache[url].callbacks.push(() => {
            resolve(fetchCache[url].response)
          })
        }
      } else {
        fetchCache[url] = {
          callbacks: [
            () => {
              resolve(fetchCache[url].response)
            },
          ],
        }
        const response = await fetch(url)
        const dataString = await response.text()
        const data = JSON.parse(dataString)
        fetchCache[url].response = data
        fetchCache[url].callbacks.forEach((callback) => {
          callback()
        })
      }
    })
  }

  function getPluginSettings(module, plugin) {
    DMF.pluginSettings[module] = DMF.pluginSettings[module] || {}
    DMF.pluginSettings[module][plugin] =
      DMF.pluginSettings[module][plugin] || {}
    const pluginSettings = DMF.pluginSettings[module][plugin] || {}

    DMF.urls[module] = DMF.urls[module] || {}
    const urlSettings = DMF.urls[module][plugin]
      ? {
          url: DMF.urls[module][plugin],
        }
      : {}

    return deepExtend({}, DMF.settings, pluginSettings, urlSettings)
  }

  function setupPlugin(module) {
    if (typeof DMF[module] !== 'undefined') {
      return
    }
    DMF[module] = function (plugin, name) {
      if (typeof name !== 'undefined') {
        plugin = plugin + '-' + name
      }
      const instance = {
        settings: getPluginSettings(module, plugin),
        fetchData: async () => await fetchData(module, plugin),
        flushCache: () => DMF.flushCache(module, plugin),
        onRefresh: (callback) => {
          refreshCallbacks.push(() => {
            callback.apply(plugin, [])
          })
        },
      }
      return instance
    }
  }

  function initDMF() {
    const base = fetchSettings()

    if (base === null) {
      return
    }

    DMF = deepExtend({}, DEFAULTS, base)

    if (typeof DMF.container === 'undefined') {
      DMF.container = document
    }

    for (let module in DMF.urls) {
      setupPlugin(module)
    }

    for (let module in DMF.pluginSettings) {
      setupPlugin(module)
    }
  }

  function callReady() {
    document.dispatchEvent(
      new CustomEvent(EVENT_READY, { detail: { DMF: DMF } })
    )
  }

  // init //

  initDMF()

  if (DMF === null) {
    console.error('No (valid) DMF settings found')
    return
  }

  DMF.refresh = function () {
    this.flushCache()
    refreshCallbacks.forEach((callback) => {
      callback()
    })
  }

  DMF.markAsLoading = function (elements) {
    elements.forEach((element) => {
      element.classList.add(CLASS_LOADING)
    })
  }

  DMF.markAsLoaded = function (elements) {
    elements.forEach((element) => {
      element.classList.remove(CLASS_LOADING)
    })
  }

  DMF.fetchElements = function (module, plugin) {
    const dataModule = camelCaseToDashed(module)
    return DMF.container.querySelectorAll(
      '[data-' + DMF.settings.prefix + '-' + dataModule + '="' + plugin + '"]'
    )
  }

  DMF.getPluginType = function (module, element) {
    return element.dataset[DMF.settings.prefix + ucfirst(module)]
  }

  DMF.getPluginName = function (module, element) {
    return element.dataset[DMF.settings.prefix + ucfirst(module) + 'Name'] || ''
  }

  DMF.getInstance = function (module, plugin, name) {
    return DMF[module](plugin, name)
  }

  DMF.getInstanceFromElement = function (module, element) {
    const plugin = DMF.getPluginType(module, element)
    const name = DMF.getPluginName(module, element)
    return DMF.getInstance(module, plugin, name)
  }

  DMF.getAllPluginInstancesWithElements = function (module, plugin) {
    const result = []
    DMF.fetchElements(module, plugin).forEach((element) => {
      const instance = DMF.getInstanceFromElement(module, element)
      result.push({
        element: element,
        plugin: instance,
      })
    })
    return result
  }

  DMF.flushCache = function (module, plugin) {
    if (typeof module !== 'undefined' && typeof plugin !== 'undefined') {
      const url = getAjaxUrl(module, plugin)
      delete fetchCache[url]
    } else {
      fetchCache = {}
    }
  }

  // ready //

  window.DMF = DMF

  document.addEventListener(EVENT_REQUEST_READY, callReady)
  callReady()
})()
