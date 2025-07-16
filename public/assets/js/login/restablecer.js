function togglePassword(icon) {
  const input = icon.previousElementSibling.previousElementSibling;
  const isPassword = input.type === "password";
  input.type = isPassword ? "text" : "password";
  icon.textContent = isPassword ? "visibility" : "visibility_off";
}
const claveInput = document.getElementById("clave");
const confirmarClaveInput = document.getElementById("confirmar-clave");
const form = document.querySelector("form");

const criterios = {
  length: (password) => password.length >= 8,
  uppercase: (password) => /[A-Z]/.test(password),
  lowercase: (password) => /[a-z]/.test(password),
  number: (password) => /\d/.test(password),
  special: (password) => /[@#$%^&*(),.?":{}|<>]/.test(password),
};

const actualizarCriterios = () => {
  const password = claveInput.value;
  for (const [id, check] of Object.entries(criterios)) {
    const elemento = document.getElementById(id);
    if (check(password)) {
      elemento.style.color = "green";
    } else {
      elemento.style.color = "red";
    }
  }
};

claveInput.addEventListener("input", actualizarCriterios);

form.addEventListener("submit", (e) => {
  const password = claveInput.value;
  const confirm = confirmarClaveInput.value;
  let valido = true;

  for (const [id, check] of Object.entries(criterios)) {
    const elemento = document.getElementById(id);
    if (!check(password)) {
      elemento.style.color = "red";
      valido = false;
    }
  }

  if (password !== confirm) {
    confirmarClaveInput.style.borderColor = "red";
    valido = false;
  } else {
    confirmarClaveInput.style.borderColor = "";
  }

  if (!valido) {
    e.preventDefault();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const dniInput = document.getElementById("dni");
  const digitoInput = document.getElementById("digito");

  if (dniInput) {
    dniInput.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(0, 8);
    });
  }

  if (digitoInput) {
    digitoInput.addEventListener("input", function () {
      this.value = this.value.replace(/\D/g, "").slice(0, 1);
    });
  }
});
