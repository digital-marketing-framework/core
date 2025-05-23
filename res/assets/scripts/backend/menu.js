const menu = document.getElementById('section-menu')
if (menu !== null) {
  menu.addEventListener('change', (event) => {
    window.location = menu.value
  })
}
