document
  .getElementById("guarnicion-form")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    const descripcion = document.getElementById("descripcion").value.trim();
    const precio = document.getElementById("precio").value.trim();
    const stock = document.getElementById("stock").value.trim();
    const estado = document.getElementById("estado").value;
    const imagen = document.getElementById("imagen").files[0];
    const alertDiv = document.getElementById("message_error");
    const alertMessage = alertDiv.querySelector("strong").nextSibling;

    alertDiv.classList.add("hidden_2");

    function showError(message) {
      alertMessage.textContent = " " + message;
      alertDiv.classList.remove("hidden_2");
      return false;
    }

    if (!nombre || !descripcion || !precio || !stock || !estado) {
      return showError("Todos los campos obligatorios deben estar completos.");
    }

    const nombreRegex = /^[a-zA-Z0-9\s]*$/;
    if (!nombreRegex.test(nombre)) {
      return showError(
        "El nombre no debe contener caracteres especiales como @, / o _."
      );
    }

    if (descripcion.length > 200) {
      return showError("La descripción no debe exceder los 200 caracteres.");
    }

    const precioValue = parseFloat(precio);
    if (isNaN(precioValue) || precioValue <= 0) {
      return showError(
        "El precio debe ser un número positivo mayor que cero, sin letras ni espacios."
      );
    }

    const stockValue = parseInt(stock);
    if (isNaN(stockValue) || stockValue <= 0) {
      return showError("El stock debe ser un número positivo mayor que cero.");
    }

    if (estado === null || estado === "") {
      return showError("El estado debe ser seleccionado.");
    }

    if (imagen) {
      const validImageTypes = [
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif",
      ];
      if (!validImageTypes.includes(imagen.type)) {
        return showError(
          "La imagen debe ser un archivo válido (jpg, png, webp, gif)."
        );
      }
    }

    alertDiv.classList.add("hidden_2");
    this.submit();
  });
