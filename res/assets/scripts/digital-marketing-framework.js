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
  const rescanCallbacks = []
  const permissionChangeCallbacks = []
  const plugins = {}
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
    } else if (window.DMF) {
      return window.DMF;
    }
    return null;
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
      DMF.containers = {}
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

  DMF.rescan = function(container) {
    const newPlugins = DMF.updateAllKnownElements(container)
    rescanCallbacks.forEach((callback) => {
      callback(newPlugins)
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

  DMF.onRescan = function(callback, pluginIdPattern = '') {
    rescanCallbacks.push((plugins) => {
      if (pluginIdPattern = '') {
        callback(plugins)
      } else {
        plugins.forEach((plugin) => {
          if (plugin.id.startsWith(pluginIdPattern)) {
            callback(plugin)
          }
        })
      }
    })
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

  DMF.getCookie = function(name) {
    return document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)')?.pop() || null;
  }

  DMF.setCookie = function(name, value, days) {
    let expires = "";
    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + (days*24*60*60*1000));
      expires = "; expires=" + date.toUTCString();
    }
    d.cookie = name + "=" + (value || "")  + expires + "; path=/";
  }

  DMF.show = function(element) {
    if (element) {
      element.style.display = ''
    }
  }

  DMF.hide = function(element) {
    if (element) {
      element.style.display = 'none'
    }
  }

  function createPlugin(pluginId, element = null, container = null, contentSettings = {}) {
    if (!container && element) {
      container = DMF.containers[element.id]
    }
    return {
      id: pluginId,
      settings: getPluginSettings(pluginId, contentSettings),
      container: container || DMF.container,
      element: element,
      snippets: null,
      getSnippets: function(container = null) {
        if (this.snippets === null) {
          this.snippets = DMF.getPluginSnippets(this, container || this.container)
        }
        return this.snippets
      },
      resolveElement: function(element) {
        if (typeof element === 'string') {
          return this.snippet(element)
        }
        return element || this.element
      },
      is: function(pluginIdPattern) {
        return this.id.startsWith(pluginIdPattern)
      },
      snippet: function(name, container = null) {
        return this.getSnippets(container)[name]
      },
      snippetSettings: function(element) {
        element = this.resolveElement(element)
        return DMF.getSettingsFromElement(element)
      },
      on: function(eventName, callback, element = null) {
        element = this.resolveElement(element)
        if (element) {
          element.addEventListener(eventName, callback)
        }
      },
      show: function(element = null) {
        element = this.resolveElement(element)
        DMF.show(element)
      },
      hide: function(element = null) {
        element = this.resolveElement(element)
        DMF.hide(element)
      },
      hydrate: function(variables, element = null) {
        element = this.resolveElement(element)
        DMF.hydrate(this, variables, element)
      },
      markAsLoading: function(element = null) {
        element = this.resolveElement(element)
        DMF.markAsLoading([element])
      },
      markAsLoaded: function(element = null) {
        element = this.resolveElement(element)
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

  function createPullPlugin(pluginId, element = null, container = null, contentSettings = {}) {
    const plugin = createPlugin(pluginId, element, container, contentSettings)

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
      defaultVariables = false,
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

  function createPushPlugin(pluginId, element = null, container = null, contentSettings = {}) {
    const plugin = createPlugin(pluginId, element, container, contentSettings)

    plugin.push = async function(payload, context) {
      return await DMF.push(pluginId, payload, context)
    }

    return plugin
  }

  DMF.plugin = function(pluginId, element = null, container = null, contentSettings = {}) {
    return pluginId.startsWith('distributor')
      ? createPushPlugin(pluginId, element, container, contentSettings)
      : createPullPlugin(pluginId, element, container, contentSettings)
  }

  DMF.plugins = function(pluginIdPattern = '', callback) {
    DMF.updateAllKnownElements()
    const plugins = []
    for (let pluginId in DMF.content) {
      if (pluginIdPattern === '' || pluginId.startsWith(pluginIdPattern)) {
        for (let elementId in DMF.content[pluginId]) {
          const element = DMF.container.getElementById(elementId)
          if (element) {
            for (let i = 0; i < DMF.content[pluginId][elementId].length; i++) {
              const contentSettings = DMF.content[pluginId][elementId][i]
              const plugin = DMF.plugin(pluginId, element, null, contentSettings)
              plugins.push(plugin)
            }
          }
        }
      }
    }
    if (callback) {
      plugins.forEach((plugin) => {
        callback(plugin)
      })
      DMF.onRescan(callback, pluginIdPattern)
    }
    return plugins
  }

  DMF.pullAndHydrateAll = function(pluginIdPattern = '') {
    DMF.plugins(pluginIdPattern, (plugin) => {
      plugin.pullAndHydrate()
    })
  }

  let number = 1
  function getUniqueId(container = null) {
    container = container || DMF.container
    let id
    do {
      id = 'dmf-element-' + number
      number++
    } while (container.getElementById(id) !== null)
    return id
  }

  DMF.getSettingsFromElement = function(element) {
    const settings = {}
    const pluginKey = DMF.settings.prefix + 'Plugin'
    const attributePrefix = DMF.settings.prefix + 'Attribute'
    for (let key in element.dataset) {
      const value = element.dataset[key]
      if (key.startsWith(DMF.settings.prefix) && key !== pluginKey) {
        if (key === attributePrefix) {
          continue;
        }
        if (key.startsWith(attributePrefix)) {
          settings.attributes = settings.attributes || {}
          const attributeName = lcfirst(key.substring(attributePrefix.length))
          settings.attributes[attributeName] = value
        } else {
          settings[lcfirst(key.replace(DMF.settings.prefix, ''))] = value
        }
      }
    }
    return settings
  }

  DMF.updateAllKnownElements = function(container) {
    container = container || DMF.container
    const pluginElements = container.querySelectorAll('[data-' + DMF.settings.prefix + '-plugin]')
    const plugins = []
    for (let i = 0; i < pluginElements.length; i++) {
      const element = pluginElements[i]
      const pluginId = element.dataset[DMF.settings.prefix + 'Plugin']

      if (!element.id) {
        element.id = getUniqueId(container)
      }

      if (element.dataset[DMF.settings.prefix + 'SettingsUpdated']) {
        continue
      }

      if (typeof DMF.content[pluginId] === 'undefined') {
        DMF.content[pluginId] = {}
      }

      if (typeof DMF.content[pluginId][element.id] === 'undefined') {
        DMF.content[pluginId][element.id] = []
      }

      const settings = DMF.getSettingsFromElement(element)

      DMF.content[pluginId][element.id].push(settings)
      DMF.containers[element.id] = container
      element.dataset[DMF.settings.prefix + 'SettingsUpdated'] = true

      plugins.push(DMF.plugin(pluginId, element, container, settings))
    }
    return plugins
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

  DMF.render = function(template, variables, defaultContent = '') {
    if (!variables) {
      return defaultContent
    }
    let rendered = template
    for (let field in variables) {
      rendered = rendered.replace('{{' + field + '}}', variables[field])
    }
    rendered = rendered.replace(/\{\{[^}]+\}\}/, '')
    return rendered
  }

  function processTemplate(element, settings, variables) {
    const template = settings.content
    const defaultContent = settings.defaultContent ?? element.innerHTML
    element.dataset[DMF.settings.prefix + 'DefaultContent'] = defaultContent
    element.innerHTML = DMF.render(template, variables, defaultContent)
  }

  function processAttributes(element, settings, variables) {
    for (let name in settings.attributes) {
      template = settings.attributes[name]
      const value = DMF.render(template, variables)
      if (value) {
        element.setAttribute(name, value) // TODO transform camelCase name to dashed-case
      }
    }
  }

  DMF.hydrate = function(plugin, variables, element = null) {
    element ??= plugin.element

    if (!element) {
      console.warn('No element to hydrate found')
      return
    }

    const isPluginElement = plugin.element && (element === plugin.element)
    const elementSettings = DMF.getSettingsFromElement(element)
    const settings = isPluginElement ? deepExtend({}, plugin.settings, elementSettings) : elementSettings

    if (settings.content) {
      processTemplate(element, settings, variables)
    }
    if (settings.attributes) {
      processAttributes(element, settings, variables)
    }

    element.querySelectorAll('[data-' + DMF.settings.prefix + '-content]').forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processTemplate(subElement, subSettings, variables)
    })
    element.querySelectorAll('[data-' + DMF.settings.prefix + '-attribute]').forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processAttributes(subElement, subSettings, variables)
    })
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
