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

  // deal with plugins individually
  DMF.plugins('collector:contentModifiers:myModifierName').forEach(async plugin => {
    const snippets = plugin.getSnippets()
    plugin.hide()
    const data = await plugin.pull()
    plugin.hydrate(data)
    plugin.show()
    plugin.show(snippets['done-notification']);
  })
})()
