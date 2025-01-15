;(function () {

  // wait until DMF core is initialised so that you can add API functionality
  async function initDMF() {
    return new Promise(resolve => {
      document.addEventListener('dmf-init', event => {
        resolve(event.detail.DMF)
      })
    })
  }

  const DMF = initDMF()

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

;(function () {

  // wait until DMF is fully initialised
  async function loadDMF() {
    setTimeout(() => {
      document.dispatchEvent(new Event('dmf-request-ready'))
    }, 0)
    return new Promise(resolve => {
      document.addEventListener('dmf-ready', event => {
        resolve(event.detail.DMF)
      })
    })
  }

  const DMF = loadDMF()

  DMF.campaigns.add(window.pageMetaData.campaignId)
})()
