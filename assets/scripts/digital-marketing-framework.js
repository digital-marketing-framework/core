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

  let fetchCache = {}

  let DMF = window.DMF

  if (typeof DMF === 'undefined') {
    console.error('No DMF settings found!')
    return
  }

  const defaults = {
    settings: {
      prefix: 'dmf',
    },
    urls: {},
    pluginSettings: {},
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
    if (this.urls[type][plugin]) {
      return this.urls[type][plugin]
    }
    return ''
  }

  function _fetch(url) {
    return new Promise(async function (resolve) {
      if (typeof fetchCache[url] !== 'undefined') {
        if (typeof fetchCache[url].response !== 'undefined') {
          resolve(fetchCache[url].response)
        } else {
          fetchCache[url].callbacks.push(function () {
            resolve(fetchCache[url].response)
          })
        }
      } else {
        fetchCache[url] = {
          callbacks: [
            function () {
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

  DMF.fetch = async function (type, plugin) {
    const url = this.getAjaxUrl(type, plugin)
    return await _fetch(url)
  }

  DMF.flushCache = function (type, plugin) {
    if (typeof type !== 'undefined' && typeof plugin !== 'undefined') {
      const url = this.getAjaxUrl(type, plugin)
      delete fetchCache[url]
    } else {
      fetchCache = {}
    }
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
        flushCache: () => DMF.flushCache(type, plugin),
      }
    }
  }
  for (let type in DMF.urls) {
    setupPlugin(type)
  }
  for (let type in DMF.pluginSettings) {
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
