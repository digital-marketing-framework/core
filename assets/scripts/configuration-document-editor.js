(function() {

  function start(textarea, stage, mode) {
    console.log('Hello DCE');
  }

  function setupEmbedded(textarea) {
    const stage = document.createElement('DIV');
    stage.classList.add('dmf-configuration-document-editor-stage')
    textarea.parentNode.insertBefore(stage, textarea.nextSibling);
    start(textarea, stage, 'embedded');
  }

  function setupModal(textarea) {
    const stage = document.createElement('DIV');
    stage.classList.add('dmf-configuration-document-editor-stage');
    document.body.appendChild(stage);

    const startButton = document.createElement('BUTTON');
    textarea.parentNode.insertBefore(startButton, textarea.nextSibling);
    startButton.innerHTML = 'configure';
    startButton.classList.add('btn', 'btn-default');

    startButton.addEventListener('click', () => {
      start(textarea, stage, 'modal');
    });
  }

  function setup(textarea) {
    const mode = textarea.dataset.mode;
    if (mode === 'embedded') {
      setupEmbedded(textarea);
    } else { // mode === 'modal'
      setupModal(textarea);
    }
  }

  function init() {
    let textarea = document.querySelector('textarea.dmf-configuration-document');
    if (textarea !== null && textarea.dataset.app === 'true') {
      setup(textarea);
    } else {
      document.addEventListener('dmf-start-app', () => {
        textarea = document.querySelector('textarea.dmf-configuration-document');
        setup(textarea);
      });
    }
  }

  init();
})();
