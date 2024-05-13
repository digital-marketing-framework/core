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

  function getAjaxUrl(pluginId) {
    return DMF.urls[pluginId] || ''
  }

  async function fetchData(pluginId) {
    const url = getAjaxUrl(pluginId)
    if (url === '') {
      console.error('No URL found for plugin', pluginId)
      return false
    }
    return await new Promise(async function (resolve) {
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
        const responseData = await response.json()
        fetchCache[url].response = responseData.status.code === 200 ? responseData.response : false
        fetchCache[url].callbacks.forEach((callback) => {
          callback()
        })
      }
    })
  }

  async function sendData(pluginId, payload, context) {
    const url = getAjaxUrl(pluginId)
    const bodyData = { payload }
    if (typeof context !== 'undefined') {
      bodyData.context = context
    }
    const response = await fetch(url, {
      method: 'POST',
      body: JSON.stringify(bodyData)
    })
    return await response.json()
  }

  function getPluginSettings(pluginId) {
    const pluginSettings = DMF.pluginSettings[pluginId] || {}
    const urlSettings = DMF.urls[pluginId]
      ? { url: DMF.urls[pluginId] }
      : {}

    return deepExtend({}, DMF.settings, pluginSettings, urlSettings)
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

  DMF.pull = async function (pluginId) {
    return await fetchData(pluginId)
  }

  DMF.push = async function(pluginId, payload, context) {
    return await sendData(pluginId, payload, context)
  }

  DMF.refresh = function () {
    this.flushCache()
    refreshCallbacks.forEach((callback) => {
      callback()
    })
  }

  DMF.onRefresh = function(callback) {
    refreshCallbacks.push(callback);
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

  DMF.fetchElements = function (pluginIdPattern, container) {
    container = container || DMF.container
    return container.querySelectorAll(['[data-' + DMF.settings.prefix + '-plugin^="' + pluginIdPattern + '"]'])
  }

  function createPlugin(pluginId) {
    return {
      settings: getPluginSettings(pluginId),
      flushCache: () => DMF.flushCache(pluginId),
      onRefresh: (callback) => {
        DMF.onRefresh(callback)
      }
    }
  }

  function createPullPlugin(pluginId) {
    const plugin = createPlugin(pluginId)
    plugin.pull = async () => await DMF.pull(pluginId)
    return plugin
  }

  function createPushPlugin(pluginId) {
    const plugin = createPlugin(pluginId)
    plugin.push = async (payload, context) => await DMF.push(pluginId, payload, context)
    return plugin
  }

  DMF.plugin = (pluginId) => {
    return pluginId.startsWith('collector') ? createPullPlugin(pluginId) : createPushPlugin(pluginId)
  }

  DMF.getPluginFromElement = function (element) {
    const pluginId = element.dataset[DMF.settings.prefix + 'Plugin']
    return DMF.plugin(pluginId)
  }

  DMF.getAllPluginInstancesWithElements = function (pluginIdPattern, container) {
    const result = []
    DMF.fetchElements(pluginIdPattern, container).forEach((element) => {
      const plugin = DMF.getPluginFromElement(element)
      result.push({ element, plugin })
    })
    return result
  }

  DMF.getPluginBehaviour = function(element, defaultValue) {
    if (typeof defaultValue === 'undefined') {
      defaultValue = 'none'
    }
    return element.dataset[DMF.settings.prefix + 'PluginBehaviour'] || defaultValue
  }

  DMF.getPluginSnippets = function(element, container) {
    container = container || DMF.container
    const snippets = {}
    container.querySelectorAll('[data-' + DMF.settings.prefix + '-plugin-target][data-' + DMF.settings.prefix + '-plugin-snippet]').forEach(snippet => {
      const pluginElement = container.querySelector(snippet.dataset[DMF.settings.prefix + 'PluginTarget'])
      if (pluginElement === element) {
        const snippetName = snippet.dataset[DMF.settings.prefix + 'PluginSnippet']
        snippets[snippetName] = snippet
      }
    })
    return snippets
  }

  DMF.flushCache = function (pluginId) {
    if (typeof pluginId !== 'undefined') {
      const url = getAjaxUrl(pluginId)
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
