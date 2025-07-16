// MODAL
function openCancelModal() {
  document.getElementById("cancelModal").classList.remove("hidden");
}

function closeCancelModal() {
  document.getElementById("cancelModal").classList.add("hidden");
}

// MENU RESPONSIVE
document.getElementById("menu-toggle").addEventListener("click", function () {
  const menu = document.getElementById("mobile-menu");
  menu.classList.toggle("open");

  // Cambiar ícono
  const icon = this.querySelector("svg");
  if (menu.classList.contains("open")) {
    icon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
  } else {
    icon.innerHTML =
      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>';
  }
});

// Menú desplegable de perfil para desktop
const profileButton = document.getElementById('profile-button');
const profileMenu = document.getElementById('profile-menu');

if (profileButton && profileMenu) {
    profileButton.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('hidden');
        profileButton.classList.toggle('profile-open');
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', () => {
        profileMenu.classList.add('hidden');
        profileButton.classList.remove('profile-open');
    });
}

// Menú desplegable de perfil para móvil
const mobileProfileButton = document.getElementById('mobile-profile-button');
const mobileProfileMenu = document.getElementById('mobile-profile-menu');

if (mobileProfileButton && mobileProfileMenu) {
    mobileProfileButton.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileProfileMenu.classList.toggle('hidden');
        mobileProfileMenu.classList.toggle('open');
        mobileProfileButton.classList.toggle('profile-open');
    });

    // Evitar que se cierre al hacer clic en el menú
    mobileProfileMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

// Cerrar todos los menús al hacer clic en cualquier parte
document.addEventListener('click', () => {
    if (profileMenu) profileMenu.classList.add('hidden');
    if (profileButton) profileButton.classList.remove('profile-open');
    if (mobileProfileMenu) {
        mobileProfileMenu.classList.add('hidden');
        mobileProfileMenu.classList.remove('open');
    }
    if (mobileProfileButton) mobileProfileButton.classList.remove('profile-open');
});