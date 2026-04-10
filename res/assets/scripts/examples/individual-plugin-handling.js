;(async function () {

  // wait until DMF is fully initialised
  async function loadDMF(services = []) {
    return new Promise(resolve => {
      document.addEventListener('dmf-ready', ({ detail: { DMF } }) => DMF.servicesLoaded(services) && resolve(DMF))
      document.dispatchEvent(new Event('dmf-request-ready'))
    })
  }

  const DMF = await loadDMF()

  // deal with plugins individually
  DMF.plugins('collector:contentModifiers:myModifierName', async plugin => {
    plugin.hide()
    const data = await plugin.pull()
    plugin.hydrate(data)
    plugin.show()
    plugin.show('done-notification')
  })
})()
