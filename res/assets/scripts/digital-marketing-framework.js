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

  function convertElementToArray(element) {
    if (element === null) {
      return []
    }
    if (!Array.isArray(element)) {
      return [element]
    }
    return element
  }

  function fetchSettings() {
    const instances = []
    if (window.DMF) {
      instances.push(window.DMF)
    }

    const settingsScript = document.querySelector('[data-dmf-selector="dmf-settings-json"]')
    if (settingsScript) {
      try {
        const instance = JSON.parse(settingsScript.innerHTML.trim())
        instances.push(instance)
      } catch (e) {
        console.error(e.message)
      }
    }

    if (instances.length === 0) {
      return null
    }

    return deepExtend({}, ...instances)
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

  DMF.addClass = function(elements, classNames, timeout = 0) {
    if (typeof classNames === 'string') {
      classNames = classNames.split(' ')
    }
    convertElementToArray(elements).forEach((element) => {
      element.classList.add(...classNames)
    })
    if (timeout > 0) {
      setTimeout(() => {
        DMF.removeClass(elements, classNames)
      }, timeout)
    }
  }

  DMF.removeClass = function(elements, classNames) {
    if (typeof classNames === 'string') {
      classNames = classNames.split(' ')
    }
    convertElementToArray(elements).forEach((element) => {
      element.classList.remove(...classNames)
    })
  }

  DMF.markAsLoading = function(elements) {
    DMF.addClass(elements, CLASS_LOADING)
  }

  DMF.markAsLoaded = function(elements) {
    DMF.removeClass(elements, CLASS_LOADING)
  }

  DMF.getCookie = function(name) {
    return document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)')?.pop() || null
  }

  DMF.setCookie = function(name, value, days = 0, path = '/', domain = null) {
    const date = new Date()
    date.setTime(date.getTime() + (days*24*60*60*1000))
    const expires = date.toUTCString()
    document.cookie = name + '=' + (value || '')
      + (expires ? '; expires= ' + expires : '')
      + (path ? '; path=' + path : '')
      + (domain ? '; domain=' + domain : '')
  }

  DMF.deleteCookie = function(name, path = '/', domain = null) {
    DMF.setCookie(name, '', -1, path, domain)
  }

  DMF.show = function(elements) {
    convertElementToArray(elements).forEach(element => {
      element.style.display = ''
    })
  }

  DMF.hide = function(elements) {
    convertElementToArray(elements).forEach(element => {
      element.style.display = 'none'
    })
  }

  DMF.on = function(elements, eventName, callback) {
    convertElementToArray(elements).forEach(element => {
      element.addEventListener(eventName, callback)
    })
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
      getAllSnippetElements: function(container = null) {
        const result = []
        const snippets = this.getSnippets()
        Object.keys(snippets).forEach(name => {
          result.push(...snippets[name])
        })
        return result
      },
      resolveElement: function(element, first = false) {
        let result;
        if (typeof element === 'string') {
          if (element === 'all') {
            // string "all" means the plugin element itself plus all of its snippets
            result = [this.element, ...this.getAllSnippetElements()]
          } else {
            // all other strings are names of snippets
            result = this.snippet(element)
          }
        } else if (Array.isArray(element)) {
          // an array means that there is a (mixed) list of elements and snippet names
          result = []
          element.forEach((e) => {
            this.resolveElement(e, false).forEach((subElement) => {
              if (!result.includes(subElement)) {
                result.push(subElement)
              }
            })
          })
        } else if (element === null) {
          // no element means the plugin element
          result = [this.element]
        } else {
          // everything else is just any element
          result = [element]
        }

        if (first) {
          return result[0] ?? null
        }

        return result
      },
      is: function(pluginIdPattern) {
        return this.id.startsWith(pluginIdPattern)
      },
      snippet: function(name, first = false, container = null) {
        const result = this.getSnippets(container)[name] || []
        if (first) {
          return result[0] ?? null
        }
        return result
      },
      snippetSettings: function(element) {
        element = this.resolveElement(element, true)
        return DMF.getSettingsFromElement(element)
      },
      on: function(eventName, callback, element = null) {
        const elements = this.resolveElement(element)
        DMF.on(elements, eventName, callback)
      },
      show: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.show(elements)
      },
      hide: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.hide(elements)
      },
      hydrate: function(variables, element = null) {
        const elements = this.resolveElement(element)
        elements.forEach(e => {
          DMF.hydrate(this, variables, e)
        })
      },
      addClass: function(classNames, element = null, timeout = 0) {
        const elements = this.resolveElement(element)
        DMF.addClass(elements, classNames, timeout)
      },
      removeClass: function(classNames, element = null) {
        const elements = this.resolveElement(element)
        DMF.removeClass(elements, classNames)
      },
      markAsLoading: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.markAsLoading(elements)
      },
      markAsLoaded: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.markAsLoaded(elements)
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

    // snippets that define a plugin target can be outside of a plugin element and still be connected
    // and they can be inside a plugin element and still not be connected
    container.querySelectorAll('[data-' + DMF.settings.prefix + '-plugin-target][data-' + DMF.settings.prefix + '-plugin-snippet]').forEach(snippet => {
      const pluginElement = container.querySelector(snippet.dataset[DMF.settings.prefix + 'PluginTarget'])
      if (pluginElement === plugin.element) {
        const snippetName = snippet.dataset[DMF.settings.prefix + 'PluginSnippet']
        snippets[snippetName] = snippets[snippetName] || []
        snippets[snippetName].push(snippet)
      }
    })

    // snippets without a plugin target are considered to be part of the plugin as soon as they are inside the plugin element
    plugin.element.querySelectorAll('[data-' + DMF.settings.prefix + '-plugin-snippet]').forEach(snippet => {
      if (!snippet.dataset[DMF.settings.prefix + 'PluginTarget']) {
        const snippetName = snippet.dataset[DMF.settings.prefix + 'PluginSnippet']
        snippets[snippetName] = snippets[snippetName] || []
        snippets[snippetName].push(snippet)
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

  function processField(element, settings, variables) {
    const fieldName = settings.field
    const defaultContent = settings.defaultContent ?? element.innerHTML
    element.dataset[DMF.settings.prefix + 'DefaultContent'] = defaultContent
    element.innerHTML = variables[fieldName] ?? defaultContent
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
    if (settings.field) {
      processField(element, settings, variables)
    }

    element.querySelectorAll('[data-' + DMF.settings.prefix + '-content]').forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processTemplate(subElement, subSettings, variables)
    })
    element.querySelectorAll('[data-' + DMF.settings.prefix + '-attribute]').forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processAttributes(subElement, subSettings, variables)
    })
    element.querySelectorAll('[data-' + DMF.settings.prefix + '-field]').forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processField(subElement, subSettings, variables)
    })
  }

  DMF.flushCache = function(pluginId) {
    function flush(url) {
      if (fetchCache[url] && typeof fetchCache[url].response !== 'undefined') {
        // flush only if the request is already finished
        delete fetchCache[url]
      }
    }
    if (typeof pluginId !== 'undefined') {
      const url = getAjaxUrl(pluginId)
      flush(url)
    } else {
      Object.keys(fetchCache).forEach(url => {
        flush(url)
      })
    }
  }

  // ready //

  DMF.updateAllKnownElements()

  window.DMF = DMF

  document.addEventListener(EVENT_REQUEST_READY, callReady)
  callReady(true)
})()
