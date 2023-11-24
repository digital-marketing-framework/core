;(function () {
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

  const DMF = window.DMF

  if (typeof DMF === 'undefined') {
    console.error('No DMF settings found!')
    return
  }

  const defaults = {
    settings: {
      prefix: 'dmf',
    },
    urls: {
      useData: {},
      modifier: {},
    },
    pluginSettings: {
      userData: {},
      modifier: {},
    },
  }

  DMF = deepExtend({}, defaults, DMF)
  if (typeof DMF.container === 'undefined') {
    DMF.container = document
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

  DMF.getAjaxUrl = function (type, plugin) {
    if (this.settings.urls[type][plugin]) {
      return this.settings.urls[plugin]
    }
    return ''
  }

  DMF.fetch = async function (type, plugin) {
    const url = this.getAjaxUrl(type, plugin)
    const response = await fetch(url)
    const dataString = await response.text()
    const data = JSON.parse(dataString)
    return data
  }

  DMF.getSettings = function (type, plugin) {
    const pluginSettings = DMF.pluginSettings[type][plugin] || {}
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
      return {
        settings: DMF.getSettings(type, plugin),
        fetch: async () => await DMF.fetch(type, plugin),
      }
    }
  }
  for (let type in DMF.settings.urls) {
    setupPlugin(type)
  }

  window.DMF = DMF
  function callReady() {
    document.dispatchEvent(
      new CustomEvent('dmf-ready', { detail: { DMF: DMF } })
    )
  }
  document.addEventListener('dmf-request-ready', callReady)
  callReady()
})()
