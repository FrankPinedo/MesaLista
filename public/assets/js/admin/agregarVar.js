const BASE_URL = window.location.origin + "/MesaLista";

document.addEventListener("DOMContentLoaded", function () {
  const disableModal = new bootstrap.Modal(
    document.getElementById("disableModal")
  );
  const enableModal = new bootstrap.Modal(
    document.getElementById("enableModal")
  );
  const disableProductName = document.getElementById("disableProductName");
  const enableProductName = document.getElementById("enableProductName");
  const disableButton = document.querySelector("#disableModal .btn-danger");
  const enableButton = document.querySelector("#enableModal .btn-success");

  let currentId, currentTable;

  // Configurar modal de deshabilitar
  document
    .querySelectorAll('[data-bs-target="#disableModal"]')
    .forEach((button) => {
      button.addEventListener("click", function () {
        currentId = this.getAttribute("data-product-id");
        currentTable = this.closest("table").getAttribute("data-table"); // Agrega data-table en cada tabla
        disableProductName.textContent = this.getAttribute("data-product-name");
        disableModal.show();
      });
    });

  // Configurar modal de habilitar
  document
    .querySelectorAll('[data-bs-target="#enableModal"]')
    .forEach((button) => {
      button.addEventListener("click", function () {
        currentId = this.getAttribute("data-product-id");
        currentTable = this.closest("table").getAttribute("data-table"); // Agrega data-table en cada tabla
        enableProductName.textContent = this.getAttribute("data-product-name");
        enableModal.show();
      });
    });

  // Acción para deshabilitar
  disableButton.addEventListener("click", function () {
    fetch(`${BASE_URL}/admin/variaciones`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `accion=deshabilitar&id=${currentId}&tabla=${currentTable}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload(); // Recargar la página para reflejar cambios
        } else {
          alert("Error: " + data.error);
        }
        disableModal.hide();
      })
      .catch((error) => {
        alert("Error en la solicitud: " + error);
        disableModal.hide();
      });
  });

  // Acción para habilitar
  enableButton.addEventListener("click", function () {
    fetch(`${BASE_URL}/admin/variaciones`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `accion=habilitar&id=${currentId}&tabla=${currentTable}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload(); // Recargar la página para reflejar cambios
        } else {
          alert("Error: " + data.error);
        }
        enableModal.hide();
      })
      .catch((error) => {
        alert("Error en la solicitud: " + error);
        enableModal.hide();
      });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const btnBebida = document.getElementById("btnBebida");
  const btnPlato = document.getElementById("btnPlato");
  const btnTamano = document.getElementById("btnTamano");

  const seccionBebida = document.getElementById("seccionBebida");
  const seccionPlato = document.getElementById("seccionPlato");
  const seccionTamano = document.getElementById("seccionTamano");

  // Mostrar sección Bebida por defecto
  seccionBebida.classList.remove("hidden");

  btnBebida.addEventListener("click", function () {
    seccionBebida.classList.remove("hidden");
    seccionPlato.classList.add("hidden");
    seccionTamano.classList.add("hidden");
  });

  btnPlato.addEventListener("click", function () {
    seccionBebida.classList.add("hidden");
    seccionPlato.classList.remove("hidden");
    seccionTamano.classList.add("hidden");
  });

  btnTamano.addEventListener("click", function () {
    seccionBebida.classList.add("hidden");
    seccionPlato.classList.add("hidden");
    seccionTamano.classList.remove("hidden");
  });
});
