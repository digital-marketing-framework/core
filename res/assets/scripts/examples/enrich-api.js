;(async function () {

  // wait until DMF is fully initialised
  async function loadDMF(services = []) {
    return new Promise(resolve => {
      document.addEventListener('dmf-ready', ({ detail: { DMF } }) => DMF.servicesLoaded(services) && resolve(DMF))
      document.dispatchEvent(new Event('dmf-request-ready'))
    })
  }

  const DMF = await loadDMF()

  DMF.register('campaigns', {
    get: function(name) {
      return window.localStorage.getItem('campaign-' + name)
    },
    set: function(name, value) {
      window.localStorage.setItem(name, value)
    },
    add: function(name, value) {
      let campaigns = this.get(name)
      campaigns += ';' + value
      this.set(name, campaigns)
    }
  })
})()

;(async function () {

  // wait until DMF is fully initialised
  async function loadDMF(services = []) {
    return new Promise(resolve => {
      document.addEventListener('dmf-ready', ({ detail: { DMF } }) => DMF.servicesLoaded(services) && resolve(DMF))
      document.dispatchEvent(new Event('dmf-request-ready'))
    })
  }

  const DMF = await loadDMF(['campaigns'])

  DMF.campaigns.add(window.pageMetaData.campaignId)
})()
