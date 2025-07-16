// Función para mostrar alertas con diseño
  function mostrarAlertaModal(titulo, mensaje, tipo = 'info') {
    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('alertaModal');
    if (modalAnterior) {
      modalAnterior.remove();
    }

    const iconos = {
      success: '<i class="bi bi-check-circle-fill text-success"></i>',
      danger: '<i class="bi bi-x-circle-fill text-danger"></i>',
      warning: '<i class="bi bi-exclamation-triangle-fill text-warning"></i>',
      info: '<i class="bi bi-info-circle-fill text-info"></i>'
    };

    const colores = {
      success: 'success',
      danger: 'danger',
      warning: 'warning',
      info: 'info'
    };

    const modalHTML = `
      <div class="modal fade" id="alertaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0">
              <h5 class="modal-title d-flex align-items-center gap-2">
                ${iconos[tipo]}
                <span>${titulo}</span>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p class="mb-0">${mensaje}</p>
            </div>
            <div class="modal-footer border-0">
              <button type="button" class="btn btn-${colores[tipo]}" data-bs-dismiss="modal">Aceptar</button>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('alertaModal'));
    modal.show();
  }

  // Función para confirmar acciones
  function confirmarAccion(titulo, mensaje, callback) {
    const modalHTML = `
      <div class="modal fade" id="confirmarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0">
              <h5 class="modal-title">
                <i class="bi bi-question-circle-fill text-warning me-2"></i>
                ${titulo}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p class="mb-0">${mensaje}</p>
            </div>
            <div class="modal-footer border-0">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-primary" id="confirmarBtn">Confirmar</button>
            </div>
          </div>
        </div>
      </div>
    `;

    // Remover modal anterior si existe
    const modalAnterior = document.getElementById('confirmarModal');
    if (modalAnterior) {
      modalAnterior.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('confirmarModal'));
    
    document.getElementById('confirmarBtn').addEventListener('click', function() {
      modal.hide();
      callback();
    });
    
    modal.show();
  }


document.addEventListener("DOMContentLoaded", function () {
  let mesasSeleccionadas = [];
  const BASE_URL = window.location.origin + "/MesaLista";
  const btnComanda = document.getElementById("btnComanda");
  const btnJuntarMesas = document.getElementById("btnJuntarMesas");
  const btnSepararMesas = document.getElementById("btnSepararMesas");
  const btnRecargar = document.getElementById("btnRecargar");
  const btnDelivery = document.getElementById("btnDelivery");
  const btnCerrarCuenta = document.getElementById("btnCerrarCuenta");

  // Función para actualizar estado de botones
  function actualizarBotones() {
    const totalSeleccionadas = mesasSeleccionadas.length;
    const todasLibres = mesasSeleccionadas.every(
      (mesa) => mesa.estado === "libre"
    );
    const hayCombinadaSeleccionada = mesasSeleccionadas.some(
      (mesa) => mesa.combinada === "true"
    );

    // Botón Comanda: solo activo si hay 1 mesa seleccionada
    btnComanda.disabled = totalSeleccionadas !== 1;

    // Botón Juntar: activo si hay 2 o más mesas libres seleccionadas
    btnJuntarMesas.disabled = !(totalSeleccionadas >= 2 && todasLibres);

    // Botón Separar: activo si hay 1 mesa combinada seleccionada
    btnSepararMesas.disabled = !(
      totalSeleccionadas === 1 && hayCombinadaSeleccionada
    );

    // Botón Cerrar Cuenta: solo activo si hay 1 mesa seleccionada en estado "atendido"
    btnCerrarCuenta.disabled = !(
      totalSeleccionadas === 1 && mesasSeleccionadas[0].estado === "atendido"
    );

    // Mostrar mensaje si hay selección múltiple
    const mensajeMultiple = document.getElementById("mensajeSeleccionMultiple");
    if (totalSeleccionadas > 1) {
      mensajeMultiple.classList.remove("d-none");
      if (!todasLibres) {
        mensajeMultiple.textContent =
          "⚠️ Para juntar mesas, todas deben estar libres";
        mensajeMultiple.classList.remove("alert-success");
        mensajeMultiple.classList.add("alert-warning");
      } else {
        mensajeMultiple.textContent =
          "✅ Mesas seleccionadas: " +
          totalSeleccionadas +
          ". Puedes juntarlas si están libres.";
        mensajeMultiple.classList.remove("alert-warning");
        mensajeMultiple.classList.add("alert-success");
      }
    } else {
      mensajeMultiple.classList.add("d-none");
    }
  }

  // Manejar clic en mesa
  document.querySelectorAll(".mesa-card").forEach((mesa) => {
    mesa.addEventListener("click", function (e) {
      // Si está en modo eliminar, no seleccionar
      if (this.classList.contains("modo-eliminar")) {
        return;
      }

      // Evitar selección si se hace clic en botones
      if (e.target.closest(".btn")) {
        return;
      }

      // Obtener datos de la mesa
      const mesaData = {
        id: this.dataset.id,
        nombre: this.dataset.mesa,
        estado: this.dataset.estado,
        combinada: this.dataset.combinada,
        elemento: this,
      };

      // Toggle selección
      if (
        this.classList.contains("seleccionada") ||
        this.classList.contains("seleccionada-multiple")
      ) {
        // Deseleccionar
        this.classList.remove("seleccionada", "seleccionada-multiple");
        mesasSeleccionadas = mesasSeleccionadas.filter(
          (m) => m.id !== mesaData.id
        );
      } else {
        // Seleccionar
        if (mesasSeleccionadas.length === 0) {
          this.classList.add("seleccionada");
        } else {
          this.classList.add("seleccionada-multiple");
        }
        mesasSeleccionadas.push(mesaData);
      }

      // Actualizar clases de todas las mesas seleccionadas
      if (mesasSeleccionadas.length > 1) {
        mesasSeleccionadas.forEach((mesa) => {
          mesa.elemento.classList.remove("seleccionada");
          mesa.elemento.classList.add("seleccionada-multiple");
        });
      } else if (mesasSeleccionadas.length === 1) {
        mesasSeleccionadas[0].elemento.classList.remove(
          "seleccionada-multiple"
        );
        mesasSeleccionadas[0].elemento.classList.add("seleccionada");
      }

      actualizarBotones();
    });
  });

  // Manejar clic en botón Comanda
  btnComanda.addEventListener("click", function () {
    if (mesasSeleccionadas.length === 1) {
      window.location.href = `${BASE_URL}/mozo/comanda/${mesasSeleccionadas[0].id}`;
    }
  });

  // Manejar clic en botón Juntar Mesas
  btnJuntarMesas.addEventListener("click", function () {
    if (mesasSeleccionadas.length >= 2) {
      const todasLibres = mesasSeleccionadas.every(
        (mesa) => mesa.estado === "libre"
      );

      if (!todasLibres) {
        alert("Solo se pueden juntar mesas que estén libres");
        return;
      }

      // Mostrar información de las mesas seleccionadas
      const infoDiv = document.getElementById("mesasSeleccionadasInfo");
      const nombresJuntos = mesasSeleccionadas.map((m) => m.nombre).join(" + ");
      infoDiv.innerHTML = `<strong>${nombresJuntos}</strong>`;

      // Guardar IDs para el formulario
      document.getElementById("mesaIds").value = JSON.stringify(
        mesasSeleccionadas.map((m) => m.id)
      );

      // Mostrar modal
      const modal = new bootstrap.Modal(
        document.getElementById("modalJuntarMesas")
      );
      modal.show();
    }
  });

  // Limpiar selección al cerrar modal de juntar mesas
  const modalJuntarMesas = document.getElementById("modalJuntarMesas");
  modalJuntarMesas.addEventListener("hidden.bs.modal", function () {
    limpiarSeleccion();
  });

  // Manejar clic en botón Separar Mesas
  btnSepararMesas.addEventListener("click", function () {
    if (
      mesasSeleccionadas.length === 1 &&
      mesasSeleccionadas[0].combinada === "true"
    ) {
      const mesaNombre = mesasSeleccionadas[0].nombre;

      if (confirm(`¿Deseas separar la mesa ${mesaNombre}?`)) {
        const form = document.createElement("form");
        form.method = "POST";
        form.style.display = "none";

        const input = document.createElement("input");
        input.name = "separar_mesa_nombre";
        input.value = mesaNombre;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }

      limpiarSeleccion();
    }
  });

  // Manejar clic en botón Cerrar Cuenta
  btnCerrarCuenta.addEventListener("click", function () {
    if (
      mesasSeleccionadas.length === 1 &&
      mesasSeleccionadas[0].estado === "atendido"
    ) {
      const mesaId = mesasSeleccionadas[0].id;

      // Verificar si hay comandas para esta mesa
      fetch(`${BASE_URL}/mozo/verificarComandasMesa/${mesaId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.tieneComandas) {
            // Redirigir a la vista de cuenta
            window.location.href = `${BASE_URL}/mozo/mostrarCuenta/${mesaId}`;
          } else {
            alert("Esta mesa no tiene comandas activas");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Error al verificar comandas de la mesa");
        });
    } else {
      alert('Solo puedes cerrar cuenta de mesas en estado "Atendido"');
    }
  });

  // Manejar clic en botón Recargar
  btnRecargar.addEventListener("click", function () {
    location.reload();
  });

  // Manejar clic en botón Delivery
  btnDelivery.addEventListener("click", function () {
    // Ir directamente a la vista de comanda delivery
    window.location.href = `${BASE_URL}/mozo/comanda?tipo=delivery`;
  });

  // Función para limpiar selección
  function limpiarSeleccion() {
    document.querySelectorAll(".mesa-card").forEach((m) => {
      m.classList.remove("seleccionada", "seleccionada-multiple");
    });
    mesasSeleccionadas = [];
    actualizarBotones();
    document.getElementById("mensajeSeleccionMultiple").classList.add("d-none");
  }

  // Deseleccionar mesas al hacer clic fuera
  document.addEventListener("click", function (e) {
    if (
      !e.target.closest(".mesa-card") &&
      !e.target.closest(".menu-icon-btn") &&
      !e.target.closest(".modal") &&
      !e.target.closest(".modal-backdrop")
    ) {
      limpiarSeleccion();
    }
  });

  // Manejar eliminación de mesa desde el botón de basura
  document.addEventListener("click", function (e) {
    if (e.target.closest(".btn-eliminar-mesa")) {
      e.stopPropagation(); // Evitar que se seleccione la mesa
      const btn = e.target.closest(".btn-eliminar-mesa");
      document.getElementById("mesaAEliminarId").value = btn.dataset.id;
      document.getElementById("mesaAEliminarNombre").textContent =
        btn.dataset.nombre;
      const modal = new bootstrap.Modal(
        document.getElementById("modalEliminarMesa")
      );
      modal.show();
    }
  });

  // Prevenir propagación en botones de cambiar estado
  document.querySelectorAll(".btn-cambiar-estado").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  });

  // Actualizar información de tiempo en mesas con comandas activas
  function actualizarTiempoMesas() {
    document
      .querySelectorAll('.mesa-card[data-estado="esperando"]')
      .forEach((mesa) => {
        const tiempoElem = mesa.querySelector(".tiempo-comanda");
        if (tiempoElem) {
          const minutos = parseInt(tiempoElem.dataset.minutos) + 1;
          tiempoElem.dataset.minutos = minutos;
          tiempoElem.textContent = `⏱ ${minutos} min`;

          // Cambiar color según el tiempo
          tiempoElem.classList.remove("text-warning", "text-danger");
          if (minutos > 30) {
            tiempoElem.classList.add("text-danger");
          } else if (minutos > 15) {
            tiempoElem.classList.add("text-warning");
          }
        }
      });
  }

  // Sistema de notificaciones
  let notificaciones = [];
  const campanaIcon = document.querySelector(".bi-bell-fill");
  const notificationBadge = document.createElement("span");
  notificationBadge.className =
    "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
  notificationBadge.style.display = "none";
  campanaIcon.parentElement.style.position = "relative";
  campanaIcon.parentElement.appendChild(notificationBadge);

  // Verificar comandas listas
  function verificarComandasListas() {
    fetch(`${BASE_URL}/mozo/verificarComandasListas`)
      .then((response) => response.json())
      .then((data) => {
        if (data.comandasListas && data.comandasListas.length > 0) {
          // Actualizar badge
          notificationBadge.textContent = data.comandasListas.length;
          notificationBadge.style.display = "block";

          // Mostrar notificación toast
          data.comandasListas.forEach((comanda) => {
            if (!notificaciones.includes(comanda.id)) {
              notificaciones.push(comanda.id);
              mostrarNotificacion(
                `¡Comanda #${comanda.id} de ${comanda.mesa} está lista!`
              );
            }
          });
        } else {
          notificationBadge.style.display = "none";
        }
      })
      .catch((error) => console.error("Error verificando comandas:", error));
  }

  function mostrarNotificacion(mensaje) {
    const toast = document.createElement("div");
    toast.className = "position-fixed bottom-0 end-0 p-3";
    toast.style.zIndex = "1050";
    toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong class="me-auto">Comanda Lista</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${mensaje}
                </div>
            </div>
        `;
    document.body.appendChild(toast);

    // Sonido de notificación
    const audio = new Audio(
      "data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEARKwAAIhYAQACABAAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZijUHGWm98OScTgwOUarm7blmFgU7k9n1unEiBC13yO/eizEIHWq+8+OWT"
    );
    audio.play();

    setTimeout(() => toast.remove(), 5000);
  }

  // Agregar click en campana para ver notificaciones
  campanaIcon.addEventListener("click", function () {
    window.location.href = `${BASE_URL}/mozo/notificaciones`;
  });

  // Verificar cada 10 segundos
  setInterval(verificarComandasListas, 10000);
  verificarComandasListas(); // Verificar al cargar

  // Actualizar tiempo cada minuto
  setInterval(actualizarTiempoMesas, 60000);

  // Inicializar estado de botones
  actualizarBotones();

  // Debug - Verificar que los elementos existen
  console.log("Elementos cargados:", {
    btnComanda: !!btnComanda,
    btnJuntarMesas: !!btnJuntarMesas,
    btnSepararMesas: !!btnSepararMesas,
    btnRecargar: !!btnRecargar,
    btnDelivery: !!btnDelivery,
    btnCerrarCuenta: !!btnCerrarCuenta,
    totalMesas: document.querySelectorAll(".mesa-card").length,
  });
  // Inicializar tooltips de Bootstrap
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
