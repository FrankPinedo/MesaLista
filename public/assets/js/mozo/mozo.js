document.addEventListener("DOMContentLoaded", function () {
  const btnAgregarMesa = document.getElementById("btnAgregarMesa");
  const formAgregarMesa = document.getElementById("formAgregarMesa");
  const btnQuitarMesa = document.getElementById("btnQuitarMesa");
  const mensajeEliminar = document.getElementById("mensajeEliminarMesa");

  let modoEliminarActivo = false;

  // ✅ Agregar mesa
  if (btnAgregarMesa && formAgregarMesa) {
    btnAgregarMesa.addEventListener("click", function () {
      formAgregarMesa.submit();
    });
  }

  // ✅ Activar/desactivar modo eliminar
  if (btnQuitarMesa) {
    btnQuitarMesa.addEventListener("click", function () {
      modoEliminarActivo = !modoEliminarActivo;
      document.querySelectorAll(".mesa-card").forEach((mesa) => {
        mesa.classList.toggle("modo-eliminar", modoEliminarActivo);
      });

      if (modoEliminarActivo) {
        mensajeEliminar.classList.remove("d-none");
        // Ocultar otros mensajes
        document.getElementById("mensajeJuntarMesas").classList.add("d-none");
        document.getElementById("mensajeSepararMesas").classList.add("d-none");
        document
          .getElementById("mensajeSeleccionMultiple")
          .classList.add("d-none");
      } else {
        mensajeEliminar.classList.add("d-none");
      }
    });
  }

  // ✅ Eliminar mesa al hacer clic en modo eliminación
  document.querySelectorAll(".mesa-card").forEach((mesa) => {
    mesa.addEventListener("click", function (e) {
      // Evitar el comportamiento si se hace clic en botones internos
      if (e.target.closest(".btn")) {
        return;
      }

      if (modoEliminarActivo && this.classList.contains("modo-eliminar")) {
        const id = this.dataset.id;
        const nombre = this.dataset.mesa;
        document.getElementById("mesaAEliminarId").value = id;
        document.getElementById("mesaAEliminarNombre").textContent = nombre;

        const modal = new bootstrap.Modal(
          document.getElementById("modalEliminarMesa")
        );
        modal.show();

        // Desactivar modo eliminar después de mostrar el modal
        mensajeEliminar.classList.add("d-none");
        modoEliminarActivo = false;
        document
          .querySelectorAll(".mesa-card")
          .forEach((m) => m.classList.remove("modo-eliminar"));
      }
    });
  });

  // ✅ Mostrar modal al agregar mesa correctamente
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("mesa_agregada") === "1") {
    const modal = new bootstrap.Modal(
      document.getElementById("modalMesaAgregada")
    );
    modal.show();

    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  }

  // ✅ Cambiar estado desde botón
  document.querySelectorAll(".btn-cambiar-estado").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();

      // Si el botón está deshabilitado, no hacer nada
      if (this.disabled) {
        return;
      }

      const id = this.dataset.id;
      const nombre = this.dataset.nombre;
      const estadoActual = this.dataset.estado;

      document.getElementById("cambiarEstadoId").value = id;
      document.getElementById(
        "nombreMesaCambio"
      ).textContent = `Mesa: ${nombre}`;

      // Mostrar estado actual
      const estadoInfo = document.getElementById("estadoActualInfo");
      estadoInfo.textContent = `Estado actual: ${
        estadoActual.charAt(0).toUpperCase() + estadoActual.slice(1)
      }`;

      // Obtener el select
      const selectEstado = document.getElementById("selectNuevoEstado");

      // Limpiar opciones anteriores
      selectEstado.innerHTML = "";
      selectEstado.disabled = false;

      // Agregar opciones según el estado actual
      if (estadoActual === "libre") {
        selectEstado.innerHTML = `
                    <option value="">Seleccione nuevo estado</option>
                    <option value="reservado">Reservado</option>
                    <option value="ocupada">Ocupada</option>
                `;
      } else if (estadoActual === "reservado") {
        selectEstado.innerHTML = `
                    <option value="">Seleccione nuevo estado</option>
                    <option value="ocupada">Ocupada</option>
                `;
      } else if (estadoActual === "ocupada") {
        selectEstado.innerHTML = `
                    <option value="">Seleccione nuevo estado</option>
                    <option value="libre">Libre</option>
                `;
      } else {
        // No debería llegar aquí si el botón está correctamente deshabilitado
        selectEstado.innerHTML = `
                    <option value="" disabled selected>No se puede cambiar este estado manualmente</option>
                `;
        selectEstado.disabled = true;
      }
    });
  });
});
