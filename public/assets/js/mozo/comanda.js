document.addEventListener("DOMContentLoaded", function () {
  const BASE_URL = window.location.origin + "/MesaLista";
  const comandaItems = document.getElementById("comanda-items");
  const totalElement = document.getElementById("total-comanda");
  const idComanda = document.getElementById("id-comanda").value;
  const btnAceptar = document.getElementById("btn-aceptar");
  const btnSalir = document.getElementById("btn-salir");
  const comentarioModal = new bootstrap.Modal(
    document.getElementById("comentarioModal")
  );
  const confirmarEnvioModal = new bootstrap.Modal(
    document.getElementById("confirmarEnvioModal")
  );

  let modoComentario = "editar";
  let productoParaAgregar = null;

  const puedeEditar = document.getElementById("puede-editar")
    ? document.getElementById("puede-editar").value === "1"
    : true;

  // Doble clic para agregar plato
  document.querySelectorAll(".producto-card").forEach((card) => {
    card.addEventListener("dblclick", function () {
      if (this.dataset.disponible === "0") {
        mostrarAlerta("Este producto no está disponible", "warning");
        return;
      }

      const idPlato = this.dataset.idPlato;
      const nombre = this.dataset.nombre;
      const precio = parseFloat(this.dataset.precio);

      agregarProductoComanda(idPlato, nombre, precio);
    });
  });

  // Función para agregar producto
  function agregarProductoComanda(idPlato, nombre, precio, comentario = "") {
    if (!puedeEditar) {
      mostrarAlerta("Esta comanda no se puede editar", "warning");
      return;
    }
    fetch(`${BASE_URL}/mozo/agregarItem`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_comanda: idComanda,
        id_plato: idPlato,
        cantidad: 1,
        comentario: comentario,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          actualizarComanda();
          mostrarAlerta("Producto agregado", "success");
        } else {
          mostrarAlerta(data.message || "Error al agregar producto", "danger");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarAlerta("Error de conexión", "danger");
      });
  }

  // Función para actualizar vista de comanda
  function actualizarComanda() {
    fetch(`${BASE_URL}/mozo/obtenerComanda/${idComanda}`)
      .then((response) => response.json())
      .then((data) => {
        // Actualizar tabla
        comandaItems.innerHTML = "";
        let total = 0;

        if (data.detalles && data.detalles.length > 0) {
          data.detalles.forEach((item) => {
            total += item.precio * item.cantidad;

            const tr = document.createElement("tr");
            tr.innerHTML = `
                        <td>${item.cantidad}</td>
                        <td>
                            ${item.nombre}
                            ${
                              item.comentario
                                ? `<small class="text-muted d-block">${item.comentario}</small>`
                                : ""
                            }
                        </td>
                        <td>S/ ${(item.precio * item.cantidad).toFixed(2)}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-sm btn-outline-secondary comentario-btn" 
                                    data-id-detalle="${item.id_detalle}"
                                    data-comentario="${item.comentario || ""}">
                                    <i class="bi bi-chat-left-text"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger eliminar-btn" 
                                    data-id-detalle="${item.id_detalle}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
            comandaItems.appendChild(tr);
          });
        } else {
          comandaItems.innerHTML =
            '<tr><td colspan="4" class="text-center py-3">No hay items en la comanda</td></tr>';
        }

        totalElement.textContent = `S/ ${total.toFixed(2)}`;

        // Re-asignar eventos a los nuevos botones
        asignarEventosBotones();
      })
      .catch((error) => {
        console.error("Error al actualizar comanda:", error);
      });
  }

  // Función para asignar eventos a botones dinámicos
  function asignarEventosBotones() {
    // Botones de comentario en items existentes
    document.querySelectorAll(".comentario-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        modoComentario = "editar";
        document.getElementById("id-detalle").value = this.dataset.idDetalle;
        document.getElementById("comentario").value =
          this.dataset.comentario || "";
        document.getElementById("modo").value = "editar";
        comentarioModal.show();
      });
<<<<<<< HEAD
    });

    // Botones de eliminar
    document.querySelectorAll(".eliminar-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        if (confirm("¿Eliminar este producto de la comanda?")) {
          const idDetalle = this.dataset.idDetalle;
          eliminarItem(idDetalle);
        }
      });
    });
  }

  // Botones de comentario para productos nuevos
  document.querySelectorAll(".comentario-plato-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const card = this.closest(".producto-card");

      if (card.dataset.disponible === "0") {
        mostrarAlerta("Este producto no está disponible", "warning");
        return;
      }

      modoComentario = "nuevo";
      productoParaAgregar = {
        id: card.dataset.idPlato,
        nombre: card.dataset.nombre,
        precio: parseFloat(card.dataset.precio),
      };

      document.getElementById("id-plato-nuevo").value = productoParaAgregar.id;
      document.getElementById("comentario").value = "";
      document.getElementById("modo").value = "nuevo";
      comentarioModal.show();
    });
  });

  // Guardar comentario
  document
    .getElementById("guardarComentario")
    .addEventListener("click", function () {
      const comentario = document.getElementById("comentario").value;

      if (modoComentario === "editar") {
        // Actualizar comentario existente
        const idDetalle = document.getElementById("id-detalle").value;

        fetch(`${BASE_URL}/mozo/actualizarComentario`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            id_detalle: idDetalle,
            comentario: comentario,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              actualizarComanda();
              comentarioModal.hide();
              mostrarAlerta("Comentario actualizado", "success");
            }
          });
      } else {
        // Agregar nuevo producto con comentario
        if (productoParaAgregar) {
          agregarProductoComanda(
            productoParaAgregar.id,
            productoParaAgregar.nombre,
            productoParaAgregar.precio,
            comentario
          );
          comentarioModal.hide();
        }
      }
    });

  // Función para eliminar item
  function eliminarItem(idDetalle) {
    if (!puedeEditar) {
      mostrarAlerta("Esta comanda no se puede editar", "warning");
      return;
    }
    fetch(`${BASE_URL}/mozo/eliminarItem`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_detalle: idDetalle,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          actualizarComanda();
          mostrarAlerta("Producto eliminado", "success");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarAlerta("Error al eliminar producto", "danger");
      });
  }

  const btnNuevaComanda = document.getElementById("btn-nueva-comanda");
  if (btnNuevaComanda) {
    btnNuevaComanda.addEventListener("click", function () {
      if (confirm("¿Deseas crear una nueva comanda para esta mesa?")) {
        const mesaId = document.getElementById("mesa-id").value;
        window.location.href = `${BASE_URL}/mozo/comanda/${mesaId}`;
      }
    });
  }

// Función para mostrar alertas mejoradas
  function mostrarAlerta(mensaje, tipo) {
    // Remover alertas anteriores
    document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());
    
    const iconos = {
      success: '<i class="bi bi-check-circle-fill me-2"></i>',
      danger: '<i class="bi bi-exclamation-circle-fill me-2"></i>',
      warning: '<i class="bi bi-exclamation-triangle-fill me-2"></i>',
      info: '<i class="bi bi-info-circle-fill me-2"></i>'
    };
    
    const alertDiv = document.createElement("div");
    alertDiv.className = `custom-alert alert alert-${tipo} alert-dismissible fade show position-fixed shadow-lg`;
    alertDiv.style.cssText = `
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      animation: slideIn 0.3s ease-out;
    `;
    alertDiv.innerHTML = `
      <div class="d-flex align-items-center">
        ${iconos[tipo] || iconos.info}
        <div class="flex-grow-1">${mensaje}</div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
      </div>
    `;
    
    // Agregar estilos de animación si no existen
    if (!document.getElementById('alert-animations')) {
      const style = document.createElement('style');
      style.id = 'alert-animations';
      style.textContent = `
        @keyframes slideIn {
          from {
            transform: translateX(100%);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
      `;
      document.head.appendChild(style);
    }
    
    document.body.appendChild(alertDiv);

    setTimeout(() => {
      alertDiv.classList.remove('show');
      setTimeout(() => alertDiv.remove(), 150);
    }, 4000);
  }

  // Botón Aceptar - Enviar a cocina
  btnAceptar.addEventListener("click", function () {
    // Verificar si hay items en la comanda
    const hayItems = comandaItems.querySelector('tr td[colspan="4"]') === null;

    if (!hayItems) {
      mostrarAlerta("La comanda está vacía", "warning");
      return;
    }

    confirmarEnvioModal.show();
  });

  // Confirmar envío
  document
    .getElementById("confirmarEnvio")
    .addEventListener("click", function () {
      // Deshabilitar botón mientras procesa
      this.disabled = true;
      this.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> Enviando...';

      fetch(`${BASE_URL}/mozo/enviarComanda`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id_comanda: idComanda,
        }),
      })
        .then((response) => response.json())
        .then(data => {
        if (data.success) {
            mostrarAlerta('Comanda enviada a cocina', 'success');
            confirmarEnvioModal.hide();

            // Cambiar interfaz a modo "enviada"
            cambiarModoEnviada();
            setTimeout(() => {
                location.reload();
            }, 1000);
          } else {
            mostrarAlerta(data.message || "Error al enviar comanda", "danger");
            this.disabled = false;
            this.innerHTML = "Enviar a cocina";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          mostrarAlerta("Error de conexión", "danger");
          this.disabled = false;
          this.innerHTML = "Enviar a cocina";
        });
    });

=======
    });

    // Botones de eliminar
    document.querySelectorAll(".eliminar-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        if (confirm("¿Eliminar este producto de la comanda?")) {
          const idDetalle = this.dataset.idDetalle;
          eliminarItem(idDetalle);
        }
      });
    });
  }

  // Botones de comentario para productos nuevos
  document.querySelectorAll(".comentario-plato-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const card = this.closest(".producto-card");

      if (card.dataset.disponible === "0") {
        mostrarAlerta("Este producto no está disponible", "warning");
        return;
      }

      modoComentario = "nuevo";
      productoParaAgregar = {
        id: card.dataset.idPlato,
        nombre: card.dataset.nombre,
        precio: parseFloat(card.dataset.precio),
      };

      document.getElementById("id-plato-nuevo").value = productoParaAgregar.id;
      document.getElementById("comentario").value = "";
      document.getElementById("modo").value = "nuevo";
      comentarioModal.show();
    });
  });

  // Guardar comentario
  document
    .getElementById("guardarComentario")
    .addEventListener("click", function () {
      const comentario = document.getElementById("comentario").value;

      if (modoComentario === "editar") {
        // Actualizar comentario existente
        const idDetalle = document.getElementById("id-detalle").value;

        fetch(`${BASE_URL}/mozo/actualizarComentario`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            id_detalle: idDetalle,
            comentario: comentario,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              actualizarComanda();
              comentarioModal.hide();
              mostrarAlerta("Comentario actualizado", "success");
            }
          });
      } else {
        // Agregar nuevo producto con comentario
        if (productoParaAgregar) {
          agregarProductoComanda(
            productoParaAgregar.id,
            productoParaAgregar.nombre,
            productoParaAgregar.precio,
            comentario
          );
          comentarioModal.hide();
        }
      }
    });

  // Función para eliminar item
  function eliminarItem(idDetalle) {
    if (!puedeEditar) {
      mostrarAlerta("Esta comanda no se puede editar", "warning");
      return;
    }
    fetch(`${BASE_URL}/mozo/eliminarItem`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_detalle: idDetalle,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          actualizarComanda();
          mostrarAlerta("Producto eliminado", "success");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarAlerta("Error al eliminar producto", "danger");
      });
  }

  const btnNuevaComanda = document.getElementById("btn-nueva-comanda");
  if (btnNuevaComanda) {
    btnNuevaComanda.addEventListener("click", function () {
      if (confirm("¿Deseas crear una nueva comanda para esta mesa?")) {
        const mesaId = document.getElementById("mesa-id").value;
        window.location.href = `${BASE_URL}/mozo/comanda/${mesaId}`;
      }
    });
  }

  // Función para mostrar alertas
  function mostrarAlerta(mensaje, tipo) {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = "9999";
    alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
    document.body.appendChild(alertDiv);

    setTimeout(() => {
      alertDiv.remove();
    }, 3000);
  }

  // Botón Aceptar - Enviar a cocina
  btnAceptar.addEventListener("click", function () {
    // Verificar si hay items en la comanda
    const hayItems = comandaItems.querySelector('tr td[colspan="4"]') === null;

    if (!hayItems) {
      mostrarAlerta("La comanda está vacía", "warning");
      return;
    }

    confirmarEnvioModal.show();
  });

  // Confirmar envío
  document
    .getElementById("confirmarEnvio")
    .addEventListener("click", function () {
      // Deshabilitar botón mientras procesa
      this.disabled = true;
      this.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> Enviando...';

      fetch(`${BASE_URL}/mozo/enviarComanda`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id_comanda: idComanda,
        }),
      })
        .then((response) => response.json())
        .then(data => {
        if (data.success) {
            mostrarAlerta('Comanda enviada a cocina', 'success');
            confirmarEnvioModal.hide();

            // Cambiar interfaz a modo "enviada"
            cambiarModoEnviada();
            setTimeout(() => {
                location.reload();
            }, 1000);
          } else {
            mostrarAlerta(data.message || "Error al enviar comanda", "danger");
            this.disabled = false;
            this.innerHTML = "Enviar a cocina";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          mostrarAlerta("Error de conexión", "danger");
          this.disabled = false;
          this.innerHTML = "Enviar a cocina";
        });
    });

>>>>>>> 0a5fb383f4b41b9ff4c2dc2c615601f43fb07122
  // Botón Salir
  btnSalir.addEventListener("click", function () {
    // Verificar si hay cambios sin guardar
    const hayItems = comandaItems.querySelector('tr td[colspan="4"]') === null;

    if (hayItems) {
      if (
        confirm("¿Deseas salir sin enviar la comanda? Los cambios se perderán.")
      ) {
        window.location.href = `${BASE_URL}/mozo`;
      }
    } else {
      window.location.href = `${BASE_URL}/mozo`;
    }
  });

  // Atajos de teclado
  document.addEventListener("keydown", function (e) {
    // F5 - Actualizar comanda
    if (e.key === "F5") {
      e.preventDefault();
      actualizarComanda();
      mostrarAlerta("Comanda actualizada", "info");
    }

    // ESC - Volver al panel
    if (e.key === "Escape") {
      e.preventDefault();
      if (confirm("¿Deseas salir de la comanda?")) {
        window.location.href = `${BASE_URL}/mozo`;
      }
    }
  });

  // Confirmar envío
  document
    .getElementById("confirmarEnvio")
    .addEventListener("click", function () {
      // Deshabilitar botón mientras procesa
      this.disabled = true;
      this.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> Enviando...';

      fetch(`${BASE_URL}/mozo/enviarComanda`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id_comanda: idComanda,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            mostrarAlerta("Comanda enviada a cocina", "success");
            confirmarEnvioModal.hide();

            // Cambiar interfaz a modo "enviada"
            cambiarModoEnviada();
          } else {
            mostrarAlerta(data.message || "Error al enviar comanda", "danger");
            this.disabled = false;
            this.innerHTML = "Enviar a cocina";
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          mostrarAlerta("Error de conexión", "danger");
          this.disabled = false;
          this.innerHTML = "Enviar a cocina";
        });
    });

  // Función para cambiar la interfaz a modo "enviada"
  function cambiarModoEnviada() {
    // Cambiar botón aceptar
    btnAceptar.textContent = "Nueva Comanda";
    btnAceptar.classList.remove("btn-success");
    btnAceptar.classList.add("btn-primary");
    btnAceptar.onclick = function () {
      if (confirm("¿Crear una nueva comanda para esta mesa?")) {
        location.reload();
      }
    };

    // Deshabilitar productos
    document.querySelectorAll(".producto-card").forEach((card) => {
      card.classList.add("disabled");
      card.dataset.disponible = "0";
    });

    // Mostrar mensaje de estado
    const alertDiv = document.createElement("div");
    alertDiv.className = "alert alert-info alert-dismissible fade show";
    alertDiv.innerHTML = `
            <i class="bi bi-info-circle"></i> 
            <strong>Comanda enviada a cocina.</strong> 
            Puedes agregar más items hasta que cocina marque como "recibido".
        `;
    document.querySelector(".card-body.p-0").prepend(alertDiv);

    // Verificar estado cada 5 segundos
    verificarEstadoComanda();
  }

  // Verificar si cocina ya recibió la comanda
  function verificarEstadoComanda() {
    const intervalo = setInterval(() => {
      fetch(`${BASE_URL}/mozo/verificarEstadoComanda/${idComanda}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.success && data.estado) {
            // Si cocina ya recibió, bloquear edición
            if (data.estado === "recibido" || data.estado === "listo") {
              clearInterval(intervalo);
              bloquearEdicion();
            }
          }
        })
        .catch((error) => {
          console.error("Error verificando estado:", error);
        });
    }, 5000);
  }

  // Bloquear edición completa
  function bloquearEdicion() {
    // Ocultar botones de eliminar y comentario
    document
      .querySelectorAll(".eliminar-btn, .comentario-btn")
      .forEach((btn) => {
        btn.style.display = "none";
      });

    // Cambiar mensaje
    document.querySelector(".alert-info").innerHTML = `
            <i class="bi bi-lock"></i> 
            <strong>Comanda en preparación.</strong> 
            No se pueden hacer cambios. Crea una nueva comanda si necesitas agregar más items.
        `;
  }

  // Cargar comanda inicial
  actualizarComanda();

  // Auto-actualizar cada 30 segundos
  setInterval(actualizarComanda, 30000);
});
