;(function () {
  // defaults //

  const EVENT_INIT = 'dmf-init'
  const EVENT_READY = 'dmf-ready'
  const EVENT_REQUEST_READY = 'dmf-request-ready'

  const CLASS_LOADING = 'loading'

  const SETTINGS_REQUIRED_PERMISSION = 'requiredPermission'

  const DEFAULTS = {
    settings: {
      prefix: 'dmf',
    },
    urls: {},
    pluginSettings: {},
    content: {},
  }

  // state //

  const refreshCallbacks = []
  const permissionChangeCallbacks = []
  let fetchCache = {}
  let DMF = null

  // helpers //

  function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1)
  }

  function lcfirst(str) {
    return str.charAt(0).toLowerCase() + str.slice(1)
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

  function getAjaxUrl(pluginId, arguments = {}) {
    if (!DMF.urls[pluginId]) {
      console.error('No URL found for plugin', pluginId)
      return ''
    }

    let url = DMF.urls[pluginId]
    let parameters = []
    for (let key in arguments) {
      const value = arguments[key]
      if (typeof value === 'object') {
        for (let subKey in value) {
          parameters.push(key + '[]=' + encodeURIComponent(value[subKey]))
        }
      } else {
        parameters.push(key + '=' + encodeURIComponent(arguments[key]))
      }
    }
    if (parameters.length > 0) {
      url += '?' + parameters.join('&')
    }

    return url
  }

  async function fetchData(pluginId, arguments = {}) {
    const url = getAjaxUrl(pluginId, arguments)
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

  function getPluginSettings(pluginId, contentSettings = {}) {
    const pluginSettings = DMF.pluginSettings[pluginId] || {}
    const urlSettings = DMF.urls[pluginId]
      ? { url: DMF.urls[pluginId] }
      : {}

    return deepExtend({}, DMF.settings, pluginSettings, urlSettings, contentSettings)
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

  function callReady(init = false) {
    // event for other scripts to enrich the API
    if (init) {
      document.dispatchEvent(
        new CustomEvent(EVENT_INIT, { detail: { DMF: DMF } })
      )
    }
    // event for other scripts to start using the API
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

  DMF.register = function(name, api) {
    DMF[name] = api
  }

  DMF.pull = async function(pluginId, arguments = {}) {
    return await fetchData(pluginId, arguments)
  }

  DMF.push = async function(pluginId, payload, context) {
    return await sendData(pluginId, payload, context)
  }

  DMF.refresh = function() {
    this.flushCache()
    refreshCallbacks.forEach((callback) => {
      callback()
    })
  }

  DMF.permissionUpdate = function() {
    this.refresh()
    permissionChangeCallbacks.forEach((callback) => {
      callback()
    })
  }

  DMF.getPermissions = async function() {
    return await DMF.pull('core:permissions')
  }

  DMF.checkPermission = async function(permission) {
    const permissions = await DMF.getPermissions()
    return permissions.granted.includes(permission)
  }

  DMF.onRefresh = function(callback) {
    refreshCallbacks.push(callback)
  }

  DMF.onPermissionChange = function(callback) {
    permissionChangeCallbacks.push(callback)
  }

  DMF.markAsLoading = function(elements) {
    elements.forEach((element) => {
      element.classList.add(CLASS_LOADING)
    })
  }

  DMF.markAsLoaded = function(elements) {
    elements.forEach((element) => {
      element.classList.remove(CLASS_LOADING)
    })
  }

  DMF.show = function(element) {
    element.style.display = ''
  }

  DMF.hide = function(element) {
    element.style.display = 'none'
  }

  function createPlugin(pluginId, element = null, contentSettings = {}) {
    return {
      id: pluginId,
      settings: getPluginSettings(pluginId, contentSettings),
      element: element,
      getSnippets: function(container = null) {
        return DMF.getPluginSnippets(this, container)
      },
      show: function(element = null) {
        DMF.show(element || this.element)
      },
      hide: function(element = null) {
        DMF.hide(element || this.element)
      },
      hydrate: function(variables, element = null) {
        DMF.hydrate(this, variables, element || this.element)
      },
      markAsLoading: function(element = null) {
        DMF.markAsLoading([element || this.element])
      },
      markAsLoaded: function(element = null) {
        DMF.markAsLoaded([element || this.element])
      },
      flushCache: function() {
        DMF.flushCache(pluginId)
      },
      onRefresh: function(callback) {
        DMF.onRefresh(callback)
      },
      getPermissions: async function() {
        return await DMF.getPermissions()
      },
      checkPermission: async function(permission) {
        if (typeof permission === 'undefined') {
          permission = this.settings[SETTINGS_REQUIRED_PERMISSION]
        }

        return await DMF.checkPermission(permission)
      }
    }
  }

  function createPullPlugin(pluginId, element = null, contentSettings = {}) {
    const plugin = createPlugin(pluginId, element, contentSettings)

    plugin.pull = async function(arguments = {}, bypassPermissions = false) {
      let proceed = true
      if (!bypassPermissions && typeof this.settings[SETTINGS_REQUIRED_PERMISSION] !== 'undefined') {
        proceed = await this.checkPermission()
      }

      if (proceed) {
        return await DMF.pull(pluginId, arguments)
      }

      return false
    }

    plugin.pullAndHydrate = async function(
      arguments = {},
      markAsLoading = true,
      defaultVariables = {},
      refresh = true
    ) {
      const processElement = async () => {
        if (markAsLoading) {
          plugin.markAsLoading()
        }
        const variables = await this.pull(arguments)
        plugin.hydrate(variables)
        if (markAsLoading) {
          plugin.markAsLoaded()
        }

        return variables
      }

      if (typeof defaultVariables === 'object' && defaultVariables !== null) {
        this.hydrate(defaultVariables)
      }

      if (refresh) {
        this.onRefresh(async () => {
          await processElement()
        })
      }

      return await processElement()
    }

    return plugin
  }

  function createPushPlugin(pluginId, element = null, contentSettings = {}) {
    const plugin = createPlugin(pluginId, element, contentSettings)

    plugin.push = async function(payload, context) {
      return await DMF.push(pluginId, payload, context)
    }

    return plugin
  }

  DMF.plugin = function(pluginId, element = null, contentSettings = {}) {
    const plugin = pluginId.startsWith('distributor') ? createPushPlugin(pluginId, element, contentSettings) : createPullPlugin(pluginId, element, contentSettings)
    return plugin
  }

  DMF.plugins = function(pluginIdPattern = '') {
    DMF.updateAllKnownElements()
    const plugins = []
    for (let pluginId in DMF.content) {
      if (pluginIdPattern === '' || pluginId.startsWith(pluginIdPattern)) {
        for (let elementId in DMF.content[pluginId]) {
          const element = DMF.container.getElementById(elementId)
          if (element) {
            for (let i = 0; i < DMF.content[pluginId][elementId].length; i++) {
              const contentSettings = DMF.content[pluginId][elementId][i]
              const plugin = DMF.plugin(pluginId, element, contentSettings)
              plugins.push(plugin)
            }
          }
        }
      }
    }
    return plugins
  }

  DMF.updateAllKnownElements = function(container) {
    container = container || DMF.container
    const pluginElements = container.querySelectorAll('[data-' + DMF.settings.prefix + '-plugin]')
    for (let i = 0; i < pluginElements.length; i++) {
      const element = pluginElements[i]
      const pluginId = element.dataset[DMF.settings.prefix + 'Plugin']

      if (element.dataset[DMF.settings.prefix + 'SettingsUpdated']) {
        continue
      }

      if (typeof DMF.content[pluginId] === 'undefined') {
        DMF.content[pluginId] = {}
      }

      if (typeof DMF.content[pluginId][element.id] === 'undefined') {
        DMF.content[pluginId][element.id] = []
      }

      const settings = {}
      for (let key in element.dataset) {
        const value = element.dataset[key]

        if (key.startsWith(DMF.settings.prefix) && key !== DMF.settings.prefix + 'Plugin') {
          settings[lcfirst(key.replace(DMF.settings.prefix, ''))] = value
        }
      }

      DMF.content[pluginId][element.id].push(settings)
      element.dataset[DMF.settings.prefix + 'SettingsUpdated'] = true
    }
  }

  DMF.getPluginSnippets = function(plugin, container = null) {
    if (!plugin.element) {
      console.error('no element found for plugin', plugin.id)
      return {}
    }
    container = container || DMF.container
    const snippets = {}
    container.querySelectorAll('[data-' + DMF.settings.prefix + '-plugin-target][data-' + DMF.settings.prefix + '-plugin-snippet]').forEach(snippet => {
      const pluginElement = container.querySelector(snippet.dataset[DMF.settings.prefix + 'PluginTarget'])
      if (pluginElement === plugin.element) {
        const snippetName = snippet.dataset[DMF.settings.prefix + 'PluginSnippet']
        snippets[snippetName] = snippet
      }
    })
    return snippets
  }

  DMF.hydrate = function(plugin, variables, element = null) {
    element ??= plugin.element
    if (!element) {
      console.error('no element to hydrate found for plugin', plugin.id)
      return
    }
    function processTemplate(element, variables) {
      // TODO use a template engine
      const settings = deepExtend({}, {
        template: element.innerHTML,
        defaultContent: element.innerHTML,
        hideUndefinedVariables: false
      }, plugin.settings)

      let rendered = settings.template
      for (let field in variables) {
        rendered = rendered.replace('{' + field + '}', variables[field])
      }

      if (settings.hideUndefinedVariables) {
        rendered = rendered.replace(/\{[^}]\}/, '')
      }

      if (rendered.match(/\{[^}]+\}/)) {
        // if some placeholders could not be filled,
        // the rendering is incomplete and will be discarded
        rendered = settings.defaultContent
      }

      element.innerHTML = rendered
    }

    function processField(element, variables) {
      const settings = deepExtend({}, plugin.settings, {
        fieldDefault: element.innerHTML
      })
      element.innerHTML = typeof variables[field] !== 'undefined' ? variables[field] : fieldDefault
    }

    if (plugin.settings.field) {
      processField(element, variables)
    } else {
      processTemplate(element, variables)
    }
  }

  DMF.flushCache = function(pluginId) {
    if (typeof pluginId !== 'undefined') {
      const url = getAjaxUrl(pluginId)
      delete fetchCache[url]
    } else {
      fetchCache = {}
    }
  }

  // ready //

  DMF.updateAllKnownElements()

  window.DMF = DMF

  document.addEventListener(EVENT_REQUEST_READY, callReady)
  callReady(true)
})()
