document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const emailInput = document.getElementById("email");
  const errorEmail = document.getElementById("error-email");

  form.addEventListener("submit", function (e) {
    const email = emailInput.value;

    if (/\s/.test(email)) {
      e.preventDefault();
      errorEmail.textContent = "El correo no debe contener espacios.";
      errorEmail.classList.remove("d-none");
    } else {
      errorEmail.classList.add("d-none");
    }
  });
});

const mensaje = document.getElementById("mensaje-confirmacion");
if (mensaje) {
  setTimeout(() => {
    mensaje.style.display = "none";
  }, 4000);
}


document.addEventListener("DOMContentLoaded", function () {
  const dniInput = document.getElementById("dni");

  if (dniInput) {
    dniInput.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(0, 8);
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const checkbox = document.getElementById("checkDefault");
  const passwordInput = document.getElementById("password");

  if (checkbox && passwordInput) {
    checkbox.addEventListener("change", function () {
      passwordInput.type = this.checked ? "text" : "password";
    });
  }
});
