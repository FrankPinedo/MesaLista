document.addEventListener("DOMContentLoaded", () => {
  const header = document.getElementById("headerDesktop");
  const btnDesk = document.getElementById("btn_Desktop");
  const iconSpan = btnDesk.querySelector("span");
  const main = document.querySelector("main");

  const updateLayout = (isCollapsed) => {
    header.classList.toggle("collapsed", isCollapsed);
    iconSpan.textContent = isCollapsed ? "keyboard_tab" : "keyboard_tab_rtl";
    main.style.paddingLeft = isCollapsed ? "60px" : "240px";
  };

  const savedState = localStorage.getItem("headerCollapsed") === "true";
  updateLayout(savedState);

  btnDesk.addEventListener("click", () => {
    const isCollapsed = header.classList.toggle("collapsed");
    updateLayout(isCollapsed);
    localStorage.setItem("headerCollapsed", isCollapsed);
  });
});

[...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

// Pagina de carga
document.addEventListener("DOMContentLoaded", function () {
  const pageLoader = document.getElementById("page-loader");

  document.body.classList.add("no-scroll");

  window.onload = function () {
    pageLoader.style.display = "none";
    document.body.classList.remove("no-scroll");
  };
});

// Activar sección por nombre de la carpeta
const currentPath = window.location.pathname.replace(/^\//, "");
const currentSection = currentPath.split("/").slice(-1)[0];
const navLinks = document.querySelectorAll(".menuItems_desktop .navlink");

navLinks.forEach((link) => {
  const linkPath = link.getAttribute("href").replace(/^\//, "");
  const linkSection = linkPath.split("/").slice(-1)[0];
  if (linkSection === currentSection) {
    link.classList.add("activeItem");
  }
});

// Tooplin
const tooltipTriggerList = document.querySelectorAll(
  '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

// boton cerrar sesión
document.getElementById("btnProfile").addEventListener("click", function () {
  const containProfile = document.getElementById("containProfile");
  const btnProfile = document.getElementById("btnProfile");

  containProfile.classList.toggle("show");

  btnProfile.classList.toggle("active");
});
