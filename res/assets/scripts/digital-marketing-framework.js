;(function () {
  // defaults //

  const EVENT_INIT = 'dmf-init'
  const EVENT_READY = 'dmf-ready'
  const EVENT_REQUEST_READY = 'dmf-request-ready'

  const CLASS_LOADING = 'loading'

  // general plugin attributes
  const ATTRIBUTE_PLUGIN_BEHAVIOUR = 'PluginBehaviour'

  // field hydration attributes
  const ATTRIBUTE_FIELD = 'Field'
  const ATTRIBUTE_FIELD_DEFAULT = 'FieldDefault'

  // template hydration attributes
  const ATTRIBUTE_PLUGIN_TEMPLATE = 'Template'
  const ATTRIBUTE_DEFAULT_CONTENT = 'DefaultContent'
  const ATTRIBUTE_HIDE_UNDEFINED_VARS = 'HideUndefinedVars'

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
      return '';
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

  function callReady() {
    // event for other scripts to enrich the API
    document.dispatchEvent(
      new CustomEvent(EVENT_INIT, { detail: { DMF: DMF } })
    )
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

  // DMF.fetchElements = function(pluginIdPattern, container) {
  //   container = container || DMF.container
  //   return container.querySelectorAll(['[data-' + DMF.settings.prefix + '-plugin^="' + pluginIdPattern + '"]'])
  // }

  DMF.fetchElements = function(pluginIdPattern, container) {
    container = container || DMF.container
    this.updateAllKnownElements(container)
    const elements = []
    for (let pluginId in DMF.content) {
      if (pluginId.startsWith(pluginIdPattern)) {
        for (let elementId in DMF.content[pluginId]) {
          const element = container.getElementById(elementId)
          if (element) {
            elements.push(element)
          }
        }
      }
    }
    return elements
  }

  function createPlugin(pluginId, element = null, contentSettings = {}) {
    return {
      id: pluginId,
      settings: getPluginSettings(pluginId, contentSettings),
      element: element,
      hydrate: function(element, variables) {
        DMF.hydrate(element, variables)
      },
      markAsLoading: function(element) {
        DMF.markAsLoading([element])
      },
      markAsLoaded: function(element) {
        DMF.markAsLoaded([element])
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
      element,
      arguments = {},
      markAsLoading = true,
      defaultVariables = {},
      refresh = true
    ) {
      const processElement = async () => {
        if (markAsLoading) {
          plugin.markAsLoading(element)
        }
        const variables = await this.pull(arguments)
        plugin.hydrate(element, variables)
        if (markAsLoading) {
          plugin.markAsLoaded(element)
        }

        return variables
      }

      if (typeof defaultVariables === 'object' && defaultVariables !== null) {
        this.hydrate(element, defaultVariables)
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

  // DMF.getPluginAttribute = function(element, name, defaultValue = '', write = false) {
  //   const scalar = typeof defaultValue !== 'object'
  //   let value = defaultValue
  //
  //   if (typeof element.dataset[DMF.settings.prefix + name] !== 'undefined') {
  //     value = element.dataset[DMF.settings.prefix + name]
  //     if (!scalar) {
  //       value = JSON.parse(value)
  //     }
  //   }
  //
  //   if (write) {
  //     element.dataset[DMF.settings.prefix + name] = typeof value !== 'object' ? value : JSON.stringify(value)
  //   }
  //
  //   return value
  // }

  DMF.plugin = function(pluginId, element = null, contentSettings = {}) {
    const plugin = pluginId.startsWith('distributor') ? createPushPlugin(pluginId, element, contentSettings) : createPullPlugin(pluginId, element, contentSettings)
  }

  DMF.plugins = function(pluginIdPattern = '') {
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

  // DMF.updateElementFromPlugin = function(plugin, element) {
  //   const elementId = element === document.body ? '<page>' : element.id
  //   const updateElementSetting = (element, key, value) => {
  //     const dataKey = DMF.settings.prefix + ucfirst(key)
  //     if (typeof element.dataset[dataKey] === 'undefined') {
  //       element.dataset[dataKey] = typeof value !== 'object' ? value : JSON.stringify(value)
  //     }
  //   }
  //
  //   if (typeof DMF.content[plugin.id] !== 'undefined' && typeof DMF.content[plugin.id][elementId] !== 'undefined') {
  //     updateElementSetting(element, 'plugin', plugin.id)
  //     for (let key in DMF.content[plugin.id][element.id]) {
  //       updateElementSetting(element, key, DMF.content[plugin.id][elementId][key])
  //     }
  //   }
  // }

  DMF.getPluginFromElement = function(element) {
    const pluginId = element.dataset[DMF.settings.prefix + 'Plugin']
    const plugin = DMF.plugin(pluginId)
    DMF.updateElementFromPlugin(plugin, element)
    return plugin
  }

  // DMF.updateAllKnownElements = function() {
  //   for (let pluginId in DMF.content) {
  //     for (let elementId in DMF.content[pluginId]) {
  //       const element = elementId === '<page>' ? document.body : DMF.container.getElementById(elementId)
  //       if (element) {
  //         const plugin = DMF.plugin(pluginId)
  //         DMF.updateElementFromPlugin(plugin, element)
  //       }
  //     }
  //   }
  // }

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

  // DMF.getAllPluginInstancesWithElements = function(pluginIdPattern, container) {
  //   const result = []
  //   DMF.fetchElements(pluginIdPattern, container).forEach((element) => {
  //     const plugin = DMF.getPluginFromElement(element)
  //     result.push({ element, plugin })
  //   })
  //   return result
  // }

  DMF.getAllPluginInstancesWithElements = function(pluginIdPattern, container) {
    const result = []
    DMF.fetchElements(pluginIdPattern, container).forEach((element) => {
      const plugin = DMF.getPluginFromElement(element)
      result.push({ element, plugin })
    })
    return result
  }

  // DMF.getPluginBehaviour = function(element, defaultValue = 'none') {
  //   return DMF.getPluginAttribute(element, ATTRIBUTE_PLUGIN_BEHAVIOUR, defaultValue, false)
  // }

  DMF.getPluginSnippets = function(plugin, container) {
    const element = plugin.element
    if (element === null) {
      console.error('no element found for plugin', plugin.id)
      return {}
    }
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

  DMF.hydrate = function(plugin, variables) {
    const element = plugin.element
    if (element === null) {
      console.error('no element to hydrate found for plugin', plugin.id)
      return
    }
    function processTemplate(element, variables) {
      const settings = deepExtend({}, plugin.settings, {
        template: element.innerHTML,
        defaultContent: element.innerHTML,
        hideUndefinedVariables: false
      })

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
  callReady()
})()
