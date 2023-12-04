;(function () {
  // defaults //

  const EVENT_READY = 'dmf-ready'
  const EVENT_REQUEST_READY = 'dmf-request-ready'

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

  function getAjaxUrl(type, plugin) {
    if (DMF.urls[type][plugin]) {
      return DMF.urls[type][plugin]
    }
    return ''
  }

  function fetchData(type, plugin) {
    const url = getAjaxUrl(type, plugin)
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

  function getPluginSettings(type, plugin) {
    DMF.pluginSettings[type] = DMF.pluginSettings[type] || {}
    DMF.pluginSettings[type][plugin] = DMF.pluginSettings[type][plugin] || {}
    const pluginSettings = DMF.pluginSettings[type][plugin] || {}

    DMF.urls[type] = DMF.urls[type] || {}
    const urlSettings = DMF.urls[type][plugin]
      ? {
          url: DMF.urls[type][plugin],
        }
      : {}

    return deepExtend({}, DMF.settings, pluginSettings, urlSettings)
  }

  function setupPlugin(type) {
    if (typeof DMF[type] !== 'undefined') {
      return
    }
    DMF[type] = function (plugin) {
      const instance = {
        settings: getPluginSettings(type, plugin),
        fetchData: async () => await fetchData(type, plugin),
        flushCache: () => DMF.flushCache(type, plugin),
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

    for (let type in DMF.urls) {
      setupPlugin(type)
    }

    for (let type in DMF.pluginSettings) {
      setupPlugin(type)
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
      element.classList.add('loading')
    })
  }

  DMF.markAsLoaded = function (elements) {
    elements.forEach((element) => {
      element.classList.remove('loading')
    })
  }

  DMF.fetchElements = function (type, plugin) {
    const dataType = camelCaseToDashed(type)
    return DMF.container.querySelectorAll(
      '[data-' + DMF.settings.prefix + '-' + dataType + '="' + plugin + '"]'
    )
  }

  DMF.flushCache = function (type, plugin) {
    if (typeof type !== 'undefined' && typeof plugin !== 'undefined') {
      const url = getAjaxUrl(type, plugin)
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
