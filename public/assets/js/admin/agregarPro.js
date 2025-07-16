// productManagement.js
document.addEventListener("DOMContentLoaded", function () {
  // Manejo de campos específicos según tipo de producto
  const tipoProductoSelect = document.querySelector(
    'select[name="tipo_producto_id"]'
  );
  if (tipoProductoSelect) {
    tipoProductoSelect.addEventListener("change", function () {
      const tipo = this.value;
      document.querySelectorAll("#tipo-especifico > div").forEach((div) => {
        div.classList.add("hidden");
      });

      if (tipo === "1") {
        document.querySelector(".bebida-fields").classList.remove("hidden");
      } else if (tipo === "2") {
        document.querySelector(".plato-fields").classList.remove("hidden");
      } else if (tipo === "4") {
        document.querySelector(".combo-fields").classList.remove("hidden");
      }
    });
  }

  // Modales para habilitar/deshabilitar productos
  const disableModal = document.getElementById("disableModal");
  if (disableModal) {
    disableModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      const productId = button.getAttribute("data-product-id");
      const productName = button.getAttribute("data-product-name");
      disableModal.querySelector("#disableProductName").textContent =
        productName;

      const confirmBtn = disableModal.querySelector(".btn-danger");
      confirmBtn.onclick = function () {
        window.location.href = `?accion=deshabilitar&id=${productId}`;
      };
    });
  }

  const enableModal = document.getElementById("enableModal");
  if (enableModal) {
    enableModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      const productId = button.getAttribute("data-product-id");
      const productName = button.getAttribute("data-product-name");
      enableModal.querySelector("#enableProductName").textContent = productName;

      const confirmBtn = enableModal.querySelector(".btn-success");
      confirmBtn.onclick = function () {
        window.location.href = `?accion=habilitar&id=${productId}`;
      };
    });
  }

  // Manejo de componentes de combos
  const btnAgregar = document.getElementById("agregar-componente");
  const contenedor = document.getElementById("combo-componentes");
  const plantilla = document.getElementById("plantilla-componente");

  if (btnAgregar && contenedor && plantilla) {
    btnAgregar.addEventListener("click", () => {
      const clon = plantilla.content.cloneNode(true);
      contenedor.appendChild(clon);
    });

    document.addEventListener("click", function (e) {
      if (e.target.classList.contains("eliminar-componente")) {
        e.target.closest(".componente").remove();
      }
    });
  }
});

document
  .getElementById("producto-form")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent form submission until validation passes

    const nombre = document.querySelector('input[name="nombre"]').value.trim();
    const descripcion = document
      .querySelector('textarea[name="descripcion"]')
      .value.trim();
    const precio = document.querySelector('input[name="precio"]').value;
    const stock = document.querySelector('input[name="stock"]').value;
    const tipoProducto = document.querySelector(
      'select[name="tipo_producto_id"]'
    ).value;
    const tamano = document.querySelector('select[name="tamano_id"]').value;
    const imagen = document.querySelector('input[name="imagen"]').files[0];

    const errorDiv = document.getElementById("message_error");
    errorDiv.classList.add("hidden_2"); // Hide error div initially
    errorDiv.classList.remove("show");

    // Regular expression to check for special characters (@, /, _)
    const specialChars = /[@/_]/;

    // Validation for Nombre
    if (!nombre) {
      showError("El campo Nombre no puede estar vacío.");
      return;
    }
    if (nombre.length > 100) {
      showError("El campo Nombre no puede exceder los 100 caracteres.");
      return;
    }
    if (specialChars.test(nombre)) {
      showError(
        "El campo Nombre no puede contener caracteres especiales como @, / o _."
      );
      return;
    }

    // Validation for Descripción
    if (!descripcion) {
      showError("El campo Descripción no puede estar vacío.");
      return;
    }
    if (descripcion.length > 200) {
      showError("El campo Descripción no puede exceder los 200 caracteres.");
      return;
    }

    // Validation for Precio
    if (!precio) {
      showError("El campo Precio no puede estar vacío.");
      return;
    }
    if (parseFloat(precio) <= 0) {
      showError("El campo Precio debe ser mayor a 0.");
      return;
    }

    // Validation for Stock
    if (!stock) {
      showError("El campo Stock no puede estar vacío.");
      return;
    }
    if (parseInt(stock) < 0) {
      showError("El campo Stock no puede ser menor a 0.");
      return;
    }

    // Validation for Tipo de Producto
    if (!tipoProducto) {
      showError("Debe seleccionar un Tipo de Producto.");
      return;
    }

    // Validation for Tamaño (optional, but must be valid if selected)
    if (!tamano) {
      showError("Debe seleccionar un Tamaño.");
      return;
    }

    // Validation for Imagen (optional, but must be valid image type if provided)
    if (imagen) {
      const validImageTypes = ["image/jpeg", "image/png", "image/webp"];
      if (!validImageTypes.includes(imagen.type)) {
        showError("La imagen debe ser de tipo JPG, PNG o WEBP.");
        return;
      }
    }

    // If all validations pass, submit the form
    this.submit();
  });

// Function to show error message
function showError(message) {
  const errorDiv = document.getElementById("message_error");
  errorDiv.innerHTML = `<strong>Atención:</strong> ${message}`;
  errorDiv.classList.remove("hidden_2");
  errorDiv.classList.add("show");
}
