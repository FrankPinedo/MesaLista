function toggleLogoutMenu() {
  const menu = document.getElementById("logoutMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function logout() {
  alert("Sesión cerrada.");
  // Aquí puedes redirigir a login.html si tienes uno:
  // window.location.href = "login.html";
}

// Ya existente
function toggleLogoutMenu() {
  const menu = document.getElementById("logoutMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function logout() {
  alert("Sesión cerrada.");
  // window.location.href = "login.html";
}

// Nuevo: manejo del formulario de contacto
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("contactForm");
  const respuesta = document.getElementById("respuesta");

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const nombre = form.nombre.value.trim();
      const correo = form.correo.value.trim();
      const mensaje = form.mensaje.value.trim();

      if (!nombre || !correo || !mensaje) {
        respuesta.textContent = "Por favor, completa todos los campos.";
        respuesta.style.color = "red";
        return;
      }

      // Simulación de envío
      respuesta.textContent = "Mensaje enviado correctamente. ¡Gracias!";
      respuesta.style.color = "green";
      form.reset();
    });
  }
});
