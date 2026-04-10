;(function () {
  // defaults //

  const INTERACTIVE_ELEMENTS = ['A', 'BUTTON', 'FORM']

  const DEFAULTS = {
    settings: {
      prefix: 'dmf',
      events: {
        READY: 'dmf-ready',
        REQUEST_READY: 'dmf-request-ready'
      },
      classes: {
        HIDDEN: '',
        LOADING: 'loading',
        DISABLED: 'disabled'
      },
      html: {
        INTERACTIVE_ELEMENTS: ['A', 'BUTTON', 'FORM']
      },
      snippets: {
        LOADING_INDICATOR: 'loading-indicator'
      },
      frames: {
        ALLOWED_ORIGINS: '',
        TIMEOUT: 0,
        RESIZE_EVENT: 'frame-resize',
        AUTO_RESIZE_PARAM: 'dmfAutoResize',
        DEBOUNCE_MS: 50,
        MEASURING_CLASS: '',
        FORM_SUBMITTED_EVENT: 'form-submitted'
      }
    },
    urls: {},
    pluginSettings: {},
    content: {},
  }

  // state //

  const refreshCallbacks = []
  const rescanCallbacks = []
  const permissionChangeCallbacks = []
  const services = []
  let fetchCache = {}
  let DMF = null
  let initialized = false

  // helpers //

  function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1)
  }

  function lcfirst(str) {
    return str.charAt(0).toLowerCase() + str.slice(1)
  }

  function camelCaseToDashed(str) {
    return lcfirst(str).replace(/[A-Z]/g, c => '-' + c.toLowerCase())
  }

  function dashedToCamelCase(str) {
    return lcfirst(str.replace(/-./g, c => c.substring(1).toUpperCase()))
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

  function debounce(fn, ms) {
    let timer
    return function() {
      clearTimeout(timer)
      timer = setTimeout(fn, ms)
    }
  }

  function convertElementToArray(element) {
    if (element === null) {
      return []
    }
    if (typeof element === 'object' && NodeList.prototype.isPrototypeOf(element)) {
      return [...element]
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

  function disableHandler(event) {
    event.preventDefault()
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

  function callReady() {
    document.dispatchEvent(
      new CustomEvent(DMF.settings.events.READY, { detail: { DMF: DMF } })
    )
  }

  // init //

  initDMF()

  if (DMF === null) {
    console.error('No (valid) DMF settings found')
    return
  }

  DMF.getPluginAttributeDatasetName = function(name) {
    return DMF.settings.prefix + ucfirst(name)
  }

  DMF.getPluginAttributeName = function(name) {
    return 'data-' + DMF.settings.prefix + '-' + camelCaseToDashed(name)
  }

  DMF.getPluginAttributeSelector = function(name, value = null, prefix = false) {
    let selector = '[' + DMF.getPluginAttributeName(name)
    if (value !== null) {
      if (prefix) {
        selector += '^'
      }
      selector += '="' + value + '"'
    }
    return selector + ']'
  }

  DMF.getPluginAttribute = function(element, name) {
    return element.dataset[DMF.getPluginAttributeDatasetName(name)] || null
  }

  DMF.setPluginAttribute = function(element, name, value) {
    element.dataset[DMF.settings.prefix + ucfirst(name)] = value
  }

  DMF.getFormData = function(element) {
    const form = element.closest('form')
    const formData = new FormData(form)
    const data = {}
    for (let [name, value] of formData.entries()) {
      data[name] = value
    }
    return data
  }

  DMF.register = function(name, api) {
    DMF[name] = api
    if (!services.includes(name)) {
      services.push(name)
    }
    if (initialized) {
      callReady()
    }
  }

  DMF.servicesLoaded = function(names = []) {
    if (typeof names === 'string') {
      names = [names]
    }
    for (let name of names) {
      if (!services.includes(name)) {
        return false
      }
    }
    return true
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
    container = container || DMF.container
    const newPlugins = DMF.updateAllKnownElements(container)
    rescanCallbacks.forEach((callback) => {
      callback(newPlugins, container)
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
    rescanCallbacks.push((plugins, container) => {
      if (pluginIdPattern === '') {
        callback(plugins, container)
      } else {
        plugins.forEach((plugin) => {
          if (plugin.id.startsWith(pluginIdPattern)) {
            callback(plugin, container)
          }
        })
      }
    })
  }

  DMF.onPermissionChange = function(callback) {
    permissionChangeCallbacks.push(callback)
  }

  DMF.getInteractiveElements = function(elements, tags = INTERACTIVE_ELEMENTS) {
    if (!Array.isArray(tags) || tags.length === 0) {
      return convertElementToArray(elements)
    }

    const result = []
    convertElementToArray(elements).forEach(container => {
      if (tags.includes(container.tagName.toUpperCase()) && !result.includes(container)) {
        result.push(container)
      }
      container.querySelectorAll(tags.join(',')).forEach(element => {
        if (!result.includes(element)) {
          result.push(element)
        }
      })
    })
    return result
  }

  DMF.disableInteractivity = function(elements, tags = INTERACTIVE_ELEMENTS) {
    elements = DMF.getInteractiveElements(elements, tags)
    DMF.setAttribute(elements, DMF.getPluginAttributeName('disabled'), true)
    DMF.addClass(elements, DMF.settings.classes.DISABLED)
    DMF.on(elements, 'click submit', disableHandler)
  }

  DMF.enableInteractivity = function(elements, tags = INTERACTIVE_ELEMENTS) {
    elements = DMF.getInteractiveElements(elements, tags)
    DMF.off(elements, 'click submit', disableHandler)
    DMF.removeClass(elements, DMF.settings.classes.DISABLED)
    DMF.removeAttribute(elements, DMF.getPluginAttributeName('disabled'))
  }

  DMF.updateInteractivity = function(elements, condition, tags = INTERACTIVE_ELEMENTS) {
    if (condition) {
      DMF.enableInteractivity(elements, tags)
    } else {
      DMF.disableInteractivity(elements, tags)
    }
  }

  DMF.disableLinks = function(elements) {
    DMF.disableInteractivity(elements, ['A'])
  }

  DMF.enableLinks = function(elements) {
    DMF.enableInteractivity(elements, ['A'])
  }

  DMF.updateLinkAvailability = function(elements, condition) {
    DMF.updateInteractivity(elements, condition, ['A'])
  }

  DMF.setAttribute = function(elements, attributeName, attributeValue) {
    convertElementToArray(elements).forEach(element => {
      if (attributeValue === null) {
        element.removeAttribute(attributeName)
      } else {
        element.setAttribute(attributeName, attributeValue)
      }
    })
  }

  DMF.removeAttribute = function(elements, attributeName) {
    DMF.setAttribute(elements, attributeName, null)
  }

  DMF.updateAttribute = function(elements, attributeName, attributeValue, condition) {
    if (condition) {
      DMF.setAttribute(elements, attributeName, attributeValue)
    } else {
      DMF.removeAttribute(elements, attributeName)
    }
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

  DMF.updateClass = function(elements, classNames, condition) {
    if (condition) {
      DMF.addClass(elements, classNames)
    } else {
      DMF.removeClass(elements, classNames)
    }
  }

  DMF.markAsLoading = function(elements) {
    DMF.addClass(elements, DMF.settings.classes.LOADING)
  }

  DMF.markAsLoaded = function(elements) {
    DMF.removeClass(elements, DMF.settings.classes.LOADING)
  }

  DMF.show = function(elements) {
    convertElementToArray(elements).forEach(element => {
      if (DMF.settings.classes.HIDDEN) {
        element.classList.remove(DMF.settings.classes.HIDDEN)
      } else {
        element.style.display = ''
      }
    })
  }

  DMF.hide = function(elements) {
    convertElementToArray(elements).forEach(element => {
      if (DMF.settings.classes.HIDDEN) {
        element.classList.add(DMF.settings.classes.HIDDEN)
      } else {
        element.style.display = 'none'
      }
    })
  }

  DMF.updateVisibility = function(elements, condition) {
    if (condition) {
      DMF.show(elements)
    } else {
      DMF.hide(elements)
    }
  }

  DMF.on = function(elements, eventName, callback) {
    const names = eventName.split(' ')
    convertElementToArray(elements).forEach(element => {
      names.forEach(name => {
        element.addEventListener(name, callback)
      })
    })
  }

  DMF.off = function(elements, eventName, callback) {
    const names = eventName.split(' ')
    convertElementToArray(elements).forEach(element => {
      names.forEach(name => {
        element.removeEventListener(name, callback)
      })
    })
  }

  DMF.trigger = function(elements, eventName, payload = {}) {
    convertElementToArray(elements).forEach(element => {
      element.dispatchEvent(new CustomEvent(eventName, { detail: payload }))
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
        } else if (typeof element === 'object' && NodeList.prototype.isPrototypeOf(element)) {
          result = [...element]
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
      off: function(eventName, callback, element = null) {
        const elements = this.resolveElement(element)
        DMF.off(elements, eventName, callback)
      },
      show: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.show(elements)
      },
      hide: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.hide(elements)
      },
      updateVisibility: function(condition, element = null) {
        const elements = this.resolveElement(element)
        DMF.updateVisibility(elements, condition)
      },
      hydrate: function(variables, element = null) {
        const elements = this.resolveElement(element)
        elements.forEach(e => {
          DMF.hydrate(this, variables, e)
        })
      },
      getInteractiveElements: function(element = null, tags = INTERACTIVE_ELEMENTS) {
        const elements = this.resolveElement(element)
        return DMF.getInteractiveElements(elements, tags)
      },
      disableInteractivity: function(element = null, tags = INTERACTIVE_ELEMENTS) {
        const elements = this.resolveElement(element)
        DMF.disableElements(elements, tags)
      },
      enableInteractivity: function(element = null, tags = INTERACTIVE_ELEMENTS) {
        const elements = this.resolveElement(element)
        DMF.enableElements(elements, tags)
      },
      updateInteractivity: function(condition, element = null, tags = INTERACTIVE_ELEMENTS) {
        const elements = this.resolveElement(element)
        DMF.updateElementAvaiablility(elements, condition, tags)
      },
      getLinks: function(element = null) {
        const elements = this.resolveElement(element)
        return DMF.getLinks(elements)
      },
      disableLinks: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.disableLinks(elements)
      },
      enableLinks: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.enableLinks(elements)
      },
      updateLinkAvailability: function(condition, element = null) {
        const elements = this.resolveElement(element)
        DMF.updateLinkAvailability(elements, condition)
      },
      setAttribute: function(attributeName, attributeValue, element = null) {
        const elements = this.resolveElement(element)
        DMF.setAttribute(elements, attributeName, attributeValue)
      },
      removeAttribute: function(attributeName, element = null) {
        const elements = this.resolveElement(element)
        DMF.removeAttribute(elements, attributeName)
      },
      updateAttribute: function(attributeName, attributeValue, element = null) {
        const elements = this.resolveElement(element)
        DMF.updateAttribute(elements, attributeName, attributeValue)
      },
      addClass: function(classNames, element = null, timeout = 0) {
        const elements = this.resolveElement(element)
        DMF.addClass(elements, classNames, timeout)
      },
      removeClass: function(classNames, element = null) {
        const elements = this.resolveElement(element)
        DMF.removeClass(elements, classNames)
      },
      updateClass: function(classNames, condition, element = null) {
        const elements = this.resolveElement(element)
        DMF.updateClass(elements, classNames, condition)
      },
      getLoadingIndicator: function() {
        return this.snippet(this.settings.snippets.LOADING_INDICATOR)
      },
      markAsLoading: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.markAsLoading(elements)
        const loadingIndicator = this.getLoadingIndicator()
        DMF.show(loadingIndicator)
      },
      markAsLoaded: function(element = null) {
        const elements = this.resolveElement(element)
        DMF.markAsLoaded(elements)
        const loadingIndicator = this.getLoadingIndicator()
        DMF.hide(loadingIndicator)
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
      checkPermission: async function(permission = null) {
        if (permission === null) {
          permission = this.settings.requiredPermission
        }

        if (typeof permission === 'undefined' || permission === null) {
          return true
        }

        return await DMF.checkPermission(permission)
      }
    }
  }

  function createPullPlugin(pluginId, element = null, container = null, contentSettings = {}) {
    const plugin = createPlugin(pluginId, element, container, contentSettings)

    plugin.pull = async function(pullArguments = {}, bypassPermissions = false) {
      let proceed = true
      if (!bypassPermissions) {
        proceed = await this.checkPermission()
      }

      let result = false
      if (proceed) {
        result = await DMF.pull(pluginId, pullArguments)
      }

      return result
    }

    plugin.pullAndProcess = async function(processCallback, options = {}) {
      const {
        pullArguments = {},
        markAsLoading = this.settings.markAsLoading ?? true,
        defaultData = this.settings.defaultData ?? false,
        refresh = true,
      } = options

      processCallback = typeof processCallback === 'function' ? processCallback : () => {}

      const processElement = async () => {
        if (markAsLoading) {
          plugin.markAsLoading()
        }

        const data = await this.pull(pullArguments)
        processCallback.call(this, data || defaultData)

        if (markAsLoading) {
          plugin.markAsLoaded()
        }

        return data
      }

      if (defaultData) {
        processCallback.call(this, defaultData)
      }

      if (refresh) {
        this.onRefresh(async () => {
          await processElement()
        })
      }

      return await processElement()
    }

    plugin.pullAndHydrate = async function(
      pullArguments = {},
      markAsLoading = null,
      defaultVariables = null,
      refresh = true
    ) {
      return await this.pullAndProcess(
        function(variables) { this.hydrate(variables) },
        { pullArguments, markAsLoading, defaultData: defaultVariables, refresh }
      )
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
    const pluginKey = DMF.getPluginAttributeDatasetName('plugin')
    const attributePrefix = DMF.getPluginAttributeDatasetName('attribute')
    for (let key in element.dataset) {
      const value = element.dataset[key]
      if (key.startsWith(DMF.settings.prefix) && key !== pluginKey) {
        if (key === attributePrefix) {
          continue
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
    const pluginElements = container.querySelectorAll(DMF.getPluginAttributeSelector('plugin'))
    const plugins = []
    for (let i = 0; i < pluginElements.length; i++) {
      const element = pluginElements[i]
      const pluginId = DMF.getPluginAttribute(element, 'plugin')

      if (!element.id) {
        element.id = getUniqueId(container)
      }

      if (DMF.getPluginAttribute(element, 'settingsUpdated')) {
        continue
      }

      DMF.content[pluginId] ??= {}
      DMF.content[pluginId][element.id] ??= []

      const settings = DMF.getSettingsFromElement(element)
      const index = DMF.content[pluginId][element.id].length
      settings.index = index

      DMF.content[pluginId][element.id][index] = settings
      DMF.containers[element.id] = container
      DMF.setPluginAttribute(element, 'settingsUpdated', true)

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
    const snippetSelector = DMF.getPluginAttributeSelector('pluginSnippet')
    const targetedSnippetSelector = snippetSelector + DMF.getPluginAttributeSelector('pluginTarget')
    container.querySelectorAll(targetedSnippetSelector).forEach(snippet => {
      const pluginSelector = DMF.getPluginAttribute(snippet, 'pluginTarget')
      const pluginElement = container.querySelector(pluginSelector)
      if (pluginElement === plugin.element) {
        const snippetName = DMF.getPluginAttribute(snippet, 'pluginSnippet')
        snippets[snippetName] = snippets[snippetName] || []
        snippets[snippetName].push(snippet)
      }
    })

    // snippets without a plugin target are considered to be part of the plugin as soon as they are inside the plugin element
    plugin.element.querySelectorAll(snippetSelector).forEach(snippet => {
      if (!DMF.getPluginAttribute(snippet, 'pluginTarget')) {
        const snippetName = DMF.getPluginAttribute(snippet, 'pluginSnippet')
        snippets[snippetName] ??= []
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
    DMF.setPluginAttribute(element, 'defaultContent', defaultContent)
    element.innerHTML = DMF.render(template, variables, defaultContent)
  }

  function processAttributes(element, settings, variables) {
    for (let name in settings.attributes) {
      template = settings.attributes[name]
      const value = DMF.render(template, variables)
      if (value) {
        element.setAttribute(camelCaseToDashed(name), value)
      }
    }
  }

  function processField(element, settings, variables) {
    const fieldName = settings.field
    const defaultContent = settings.defaultContent ?? element.innerHTML
    DMF.setPluginAttribute(element, 'defaultContent', defaultContent)
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

    element.querySelectorAll(DMF.getPluginAttributeSelector('content')).forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processTemplate(subElement, subSettings, variables)
    })
    element.querySelectorAll(DMF.getPluginAttributeSelector('attribute')).forEach((subElement) => {
      const subSettings = DMF.getSettingsFromElement(subElement)
      processAttributes(subElement, subSettings, variables)
    })
    element.querySelectorAll(DMF.getPluginAttributeSelector('field')).forEach((subElement) => {
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

  DMF.register('cookies', {
    get: function(name) {
      return document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)')?.pop() || null
    },
    exists: function(name) {
      return this.get(name) !== null
    },
    set: function(name, value, days = 0, path = '/', domain = null) {
      const date = new Date()
      date.setTime(date.getTime() + (days*24*60*60*1000))
      const expires = date.toUTCString()
      document.cookie = name + '=' + (value || '')
          + (days !== 0 ? '; expires= ' + expires : '')
          + (path ? '; path=' + path : '')
          + (domain ? '; domain=' + domain : '')
    },
    delete: function(name, path = '/', domain = null) {
      this.set(name, '', -1, path, domain)
    }
  })

  DMF.register('frames', (function() {
    const listeners = []
    const handlers = []
    const pendingRequests = {}

    function isOriginAllowed(origin) {
      if (origin === window.origin) {
        return true
      }
      const setting = DMF.settings.frames.ALLOWED_ORIGINS
      if (!setting) {
        return true
      }
      const origins = setting.split(',').map(o => o.trim()).filter(o => o)
      if (origins.length === 0) {
        return true
      }
      return origins.includes(origin)
    }

    function resolveFrame(frame) {
      if (frame && frame.contentWindow) {
        return frame.contentWindow
      }
      return frame
    }

    function generateId() {
      return Math.random().toString(36).substring(2) + Date.now().toString(36)
    }

    function sendMessage(targetWindow, name, payload, requestId) {
      const message = {
        dmf: true,
        name: name,
        payload: payload || {}
      }
      if (requestId) {
        message.requestId = requestId
      }
      targetWindow.postMessage(message, '*')
    }

    window.addEventListener('message', function(event) {
      const data = event.data
      if (!data || !data.dmf) {
        return
      }

      if (!isOriginAllowed(event.origin)) {
        return
      }

      const source = event.source
      const name = data.name
      const payload = data.payload || {}
      const requestId = data.requestId

      // resolve pending requests
      if (requestId && pendingRequests[requestId]) {
        pendingRequests[requestId](payload)
        delete pendingRequests[requestId]
        return
      }

      // handle incoming requests
      if (name.startsWith('request-')) {
        const responseName = name.substring(8)
        for (let i = handlers.length - 1; i >= 0; i--) {
          const h = handlers[i]
          if ((!h.frame || h.frame === source) && h.name === responseName) {
            Promise.resolve(h.handler(payload, source)).then(function(response) {
              sendMessage(source, responseName, response, requestId)
            })
            break
          }
        }
        return
      }

      // notify listeners
      listeners.forEach(function(l) {
        if ((!l.frame || l.frame === source) && l.name === name) {
          l.callback(payload, source)
        }
      })
    })

    return {
      isParentFrame: window.parent === window,
      send: function(frame, name, payload) {
        sendMessage(resolveFrame(frame), name, payload)
      },
      listen: function(name, callback, frame) {
        listeners.push({ frame: resolveFrame(frame) || null, name: name, callback: callback })
      },
      request: function(frame, name, payload) {
        frame = resolveFrame(frame)
        const requestId = generateId()
        const timeout = DMF.settings.frames.TIMEOUT
        return new Promise(function(resolve, reject) {
          pendingRequests[requestId] = resolve
          if (timeout > 0) {
            setTimeout(function() {
              if (pendingRequests[requestId]) {
                delete pendingRequests[requestId]
                reject(new Error('Frame request "' + name + '" timed out'))
              }
            }, timeout)
          }
          sendMessage(frame, 'request-' + name, payload, requestId)
        })
      },
      respond: function(name, handler, frame) {
        frame = resolveFrame(frame) || null
        const existing = handlers.findIndex(function(h) {
          return h.frame === frame && h.name === name
        })
        if (existing !== -1) {
          handlers[existing] = { frame: frame, name: name, handler: handler }
        } else {
          handlers.push({ frame: frame, name: name, handler: handler })
        }
      },
      sendToParent: function(name, payload) {
        if (this.isParentFrame) {
          return
        }
        this.send(window.parent, name, payload)
      },
      listenToParent: function(name, callback) {
        if (this.isParentFrame) {
          return
        }
        this.listen(name, callback, window.parent)
      },
      requestFromParent: function(name, payload) {
        if (this.isParentFrame) {
          return Promise.reject(new Error('Cannot request from parent: already the top frame'))
        }
        return this.request(window.parent, name, payload)
      },
      respondToParent: function(name, handler) {
        if (this.isParentFrame) {
          return
        }
        this.respond(name, handler, window.parent)
      },
      findIframeBySource: function(source) {
        const iframes = document.querySelectorAll('iframe')
        for (const iframe of iframes) {
          if (iframe.contentWindow === source) {
            return iframe
          }
        }
        return null
      },
      getDocumentHeight: function() {
        const measuringClass = DMF.settings.frames.MEASURING_CLASS
        if (measuringClass) {
          document.documentElement.classList.add(measuringClass)
        }
        const height = document.body.scrollHeight
        if (measuringClass) {
          document.documentElement.classList.remove(measuringClass)
        }
        return height
      },
      sendResizeToParent: function() {
        this.sendToParent(DMF.settings.frames.RESIZE_EVENT, {
          height: this.getDocumentHeight()
        })
      },
      listenToResize: function(callback) {
        this.listen(DMF.settings.frames.RESIZE_EVENT, callback)
      },
      sendFormSubmittedToParent: function(data) {
        this.sendToParent(DMF.settings.frames.FORM_SUBMITTED_EVENT, data || {})
      },
      listenToFormSubmitted: function(callback) {
        this.listen(DMF.settings.frames.FORM_SUBMITTED_EVENT, function(payload, source) {
          callback(payload, DMF.frames.findIframeBySource(source), source)
        })
      },
      initFrameResize: function() {
        this.listenToResize(function(payload, source) {
          const iframe = DMF.frames.findIframeBySource(source)
          if (iframe) {
            iframe.style.height = payload.height + 'px'
          }
        })

        if (!DMF.frames.isParentFrame) {
          const params = new URLSearchParams(window.location.search)
          if (params.get(DMF.settings.frames.AUTO_RESIZE_PARAM) === '1') {
            const observer = new ResizeObserver(debounce(function() {
              DMF.frames.sendResizeToParent()
            }, DMF.settings.frames.DEBOUNCE_MS))
            observer.observe(document.documentElement)
            DMF.frames.sendResizeToParent()
          }
        }
      }
    }
  })())

  // ready //

  window.DMF = DMF
  DMF.updateAllKnownElements()

  setTimeout(() => {
    initialized = true
    callReady()
    document.addEventListener(DMF.settings.events.REQUEST_READY, callReady)

    DMF.frames.initFrameResize()
  }, 0)
})()
