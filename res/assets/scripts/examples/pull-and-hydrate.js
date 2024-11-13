;(async function () {

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

  const DMF = await loadDMF()

  // pull data for all of your modifiers and hydrate the linked elements automatically
  DMF.plugins('collector:contentModifiers:myModifierName').forEach(plugin => {
    plugin.pullAndHydrate()
  })
})()
