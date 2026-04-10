;(async function () {

  // wait until DMF is fully initialised
  async function loadDMF(services = []) {
    return new Promise(resolve => {
      document.addEventListener('dmf-ready', ({ detail: { DMF } }) => DMF.servicesLoaded(services) && resolve(DMF))
      document.dispatchEvent(new Event('dmf-request-ready'))
    })
  }

  const DMF = await loadDMF()

  // pull data for all of your modifiers and hydrate the linked elements automatically
  DMF.plugins('collector:contentModifiers:myModifierName', async plugin => {
    const data = await plugin.pull({
      name: plugin.settings.name
    })
    plugin.hydrate(data)
  })
})()
