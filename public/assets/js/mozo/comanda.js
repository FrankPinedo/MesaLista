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
  let productosEnPantalla = new Set(); // Para rastrear productos mostrados

  const puedeEditar = document.getElementById("puede-editar")
    ? document.getElementById("puede-editar").value === "1"
    : true;

  // Recopilar IDs de productos en pantalla
  document.querySelectorAll(".producto-card").forEach((card) => {
    productosEnPantalla.add(parseInt(card.dataset.idPlato));
  });

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

  // Función para agregar producto con validación de stock
  function agregarProductoComanda(idPlato, nombre, precio, comentario = "") {
    if (!puedeEditar) {
      mostrarAlerta("Esta comanda no se puede editar", "warning");
      return;
    }
    
    // Verificar stock antes de agregar
    const card = document.querySelector(`[data-id-plato="${idPlato}"]`);
    const stockActual = parseInt(card.querySelector('.badge').textContent.replace('Stock: ', ''));
    
    if (stockActual <= 0) {
      mostrarAlerta("No hay stock disponible para este producto", "danger");
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
          
          // Actualizar stock localmente de inmediato
          actualizarStockLocal(idPlato, -1);
          
          if (data.pendiente) {
            mostrarAlerta("Producto agregado como cambio pendiente. Presiona 'Actualizar en Cocina' para confirmar.", "info");
            // Mostrar botones de cambios pendientes
            mostrarBotonesCambiosPendientes(true);
          } else {
            mostrarAlerta("Producto agregado", "success");
          }
          
          // Actualizar todos los stocks después de un pequeño delay
          setTimeout(actualizarTodosLosStocks, 500);
        } else {
          mostrarAlerta(data.message || "Error al agregar producto", "danger");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarAlerta("Error de conexión", "danger");
      });
  }

  // Función para actualizar stock localmente
  function actualizarStockLocal(productoId, cambio) {
    const card = document.querySelector(`[data-id-plato="${productoId}"]`);
    if (card) {
      const badge = card.querySelector('.badge');
      const stockActual = parseInt(badge.textContent.replace('Stock: ', ''));
      const nuevoStock = Math.max(0, stockActual + cambio);
      
      badge.textContent = `Stock: ${nuevoStock}`;
      badge.className = `badge ${nuevoStock > 0 ? 'bg-success' : 'bg-danger'}`;
      
      // Actualizar disponibilidad
      if (nuevoStock <= 0) {
        card.classList.add('disabled', 'bg-light');
        card.dataset.disponible = '0';
      } else {
        card.classList.remove('disabled', 'bg-light');
        card.dataset.disponible = '1';
      }
    }
  }

  // Función para actualizar todos los stocks desde el servidor
  function actualizarTodosLosStocks() {
    if (productosEnPantalla.size === 0) return;
    
    fetch(`${BASE_URL}/mozo/obtenerStockActualizado`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        producto_ids: Array.from(productosEnPantalla)
      }),
    })
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.stocks) {
        Object.entries(data.stocks).forEach(([productoId, stock]) => {
          const card = document.querySelector(`[data-id-plato="${productoId}"]`);
          if (card) {
            const badge = card.querySelector('.badge');
            badge.textContent = `Stock: ${stock}`;
            badge.className = `badge ${stock > 0 ? 'bg-success' : 'bg-danger'}`;
            
            // Actualizar disponibilidad
            if (stock <= 0 && puedeEditar) {
              card.classList.add('disabled', 'bg-light');
              card.dataset.disponible = '0';
            } else if (stock > 0 && puedeEditar) {
              card.classList.remove('disabled', 'bg-light');
              card.dataset.disponible = '1';
            }
          }
        });
      }
    })
    .catch((error) => {
      console.error("Error actualizando stocks:", error);
    });
  }

  // Función para actualizar vista de comanda
  function actualizarComanda() {
    const estadoComanda = document.getElementById("estado-comanda")?.value || 'nueva';
    
    // Si es pendiente o recibido, obtener con pendientes
    if (estadoComanda === 'pendiente' || estadoComanda === 'recibido') {
      fetch(`${BASE_URL}/mozo/obtenerComandaConPendientes/${idComanda}`)
        .then((response) => response.json())
        .then((data) => {
          actualizarTablaComanda(data);
        })
        .catch((error) => {
          console.error("Error al actualizar comanda:", error);
        });
    } else {
      fetch(`${BASE_URL}/mozo/obtenerComanda/${idComanda}`)
        .then((response) => response.json())
        .then((data) => {
          actualizarTablaComanda({detalles: data.detalles, detallesPendientes: []});
        })
        .catch((error) => {
          console.error("Error al actualizar comanda:", error);
        });
    }
  }

  // Función para actualizar la tabla de comanda
  function actualizarTablaComanda(data) {
    comandaItems.innerHTML = "";
    let total = 0;

    // Renderizar items confirmados
    if (data.detalles && data.detalles.length > 0) {
      data.detalles.forEach((item) => {
        total += item.precio * item.cantidad;

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${item.cantidad}</td>
          <td>
            ${item.nombre}
            ${item.comentario ? `<small class="text-muted d-block">${item.comentario}</small>` : ""}
          </td>
          <td>S/ ${(item.precio * item.cantidad).toFixed(2)}</td>
          ${puedeEditar && document.getElementById("estado-comanda")?.value === 'nueva' ? `
            <td>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-sm btn-outline-secondary comentario-btn" 
                  data-id-detalle="${item.id_detalle}"
                  data-comentario="${item.comentario || ""}">
                  <i class="bi bi-chat-left-text"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger eliminar-btn" 
                  data-id-detalle="${item.id_detalle}"
                  data-producto-id="${item.id_plato}">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          ` : ''}
        `;
        comandaItems.appendChild(tr);
      });
    }

    // Renderizar items pendientes si existen
    if (data.detallesPendientes && data.detallesPendientes.length > 0) {
      // Agregar separador
      const trSeparador = document.createElement("tr");
      trSeparador.className = "seccion-pendientes";
      trSeparador.innerHTML = `
        <td colspan="${puedeEditar ? '4' : '3'}" class="text-center py-2 bg-warning bg-opacity-25">
          <strong>CAMBIOS PENDIENTES DE ENVIAR A COCINA</strong>
        </td>
      `;
      comandaItems.appendChild(trSeparador);

      // Agregar items pendientes
      data.detallesPendientes.forEach((item) => {
        total += item.precio * item.cantidad;

        const tr = document.createElement("tr");
        tr.className = "cambio-pendiente";
        tr.innerHTML = `
          <td>
            ${item.cantidad}
            <span class="badge badge-pendiente bg-warning text-dark">NUEVO</span>
          </td>
          <td>
            ${item.nombre}
            ${item.comentario ? `<small class="text-muted d-block">${item.comentario}</small>` : ""}
          </td>
          <td>S/ ${(item.precio * item.cantidad).toFixed(2)}</td>
          ${puedeEditar ? `
            <td>
              <button class="btn btn-sm btn-outline-danger eliminar-pendiente-btn" 
                data-id-detalle="${item.id_detalle}"
                data-producto-id="${item.id_plato}">
                <i class="bi bi-x-circle"></i>
              </button>
            </td>
          ` : ''}
        `;
        comandaItems.appendChild(tr);
      });

      // Mostrar botones de cambios pendientes
      mostrarBotonesCambiosPendientes(true);
    } else {
      mostrarBotonesCambiosPendientes(false);
    }

    if (data.detalles.length === 0 && (!data.detallesPendientes || data.detallesPendientes.length === 0)) {
      comandaItems.innerHTML = `<tr><td colspan="${puedeEditar ? '4' : '3'}" class="text-center py-3">No hay items en la comanda</td></tr>`;
    }

    totalElement.textContent = `S/ ${total.toFixed(2)}`;

    // Re-asignar eventos a los nuevos botones
    asignarEventosBotones();
  }

  // Función para mostrar/ocultar botones de cambios pendientes
  function mostrarBotonesCambiosPendientes(mostrar) {
    const btnConfirmarCambios = document.getElementById("btn-confirmar-cambios");
    const btnCancelarCambios = document.getElementById("btn-cancelar-cambios");
    const alertaCambios = document.querySelector('.alert-warning[role="alert"]');
    
    if (btnConfirmarCambios && btnCancelarCambios && alertaCambios) {
      if (mostrar) {
        btnConfirmarCambios.style.display = 'block';
        btnCancelarCambios.style.display = 'block';
        alertaCambios.style.display = 'flex';
      } else {
        btnConfirmarCambios.style.display = 'none';
        btnCancelarCambios.style.display = 'none';
        alertaCambios.style.display = 'none';
      }
    }
  }

  // Función para asignar eventos a botones dinámicos
  function asignarEventosBotones() {
    // Botones de comentario en items existentes
    document.querySelectorAll(".comentario-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        modoComentario = "editar";
        document.getElementById("id-detalle").value = this.dataset.idDetalle;
        document.getElementById("comentario").value = this.dataset.comentario || "";
        document.getElementById("modo").value = "editar";
        comentarioModal.show();
      });
    });

    // Botones de eliminar con restauración de stock
    document.querySelectorAll(".eliminar-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        if (confirm("¿Eliminar este producto de la comanda?")) {
          const idDetalle = this.dataset.idDetalle;
          const productoId = this.dataset.productoId;
          eliminarItem(idDetalle, productoId);
        }
      });
    });

    // Botones para eliminar items pendientes
    document.querySelectorAll(".eliminar-pendiente-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        if (confirm("¿Eliminar este producto pendiente?")) {
          const idDetalle = this.dataset.idDetalle;
          const productoId = this.dataset.productoId;
          eliminarItem(idDetalle, productoId);
        }
      });
    });
  }

  // Función para eliminar item con restauración de stock
  function eliminarItem(idDetalle, productoId) {
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
          
          // Actualizar stock localmente
          if (productoId) {
            actualizarStockLocal(productoId, 1);
          }
          
          mostrarAlerta("Producto eliminado", "success");
          
          // Actualizar todos los stocks después de un pequeño delay
          setTimeout(actualizarTodosLosStocks, 500);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarAlerta("Error al eliminar producto", "danger");
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
  document.getElementById("guardarComentario").addEventListener("click", function () {
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

  // Confirmar cambios pendientes
  const btnConfirmarCambios = document.getElementById("btn-confirmar-cambios");
  if (btnConfirmarCambios) {
    btnConfirmarCambios.addEventListener("click", function() {
      if (confirm("¿Confirmar los cambios y enviarlos a cocina?")) {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Actualizando...';
        
        fetch(`${BASE_URL}/mozo/confirmarCambios`, {
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
            mostrarAlerta("Cambios enviados a cocina", "success");
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            mostrarAlerta("Error al confirmar cambios", "danger");
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-check-circle"></i> Actualizar en Cocina';
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          mostrarAlerta("Error de conexión", "danger");
          this.disabled = false;
          this.innerHTML = '<i class="bi bi-check-circle"></i> Actualizar en Cocina';
        });
      }
    });
  }

  // Cancelar cambios pendientes con restauración de stock
  const btnCancelarCambios = document.getElementById("btn-cancelar-cambios");
  if (btnCancelarCambios) {
    btnCancelarCambios.addEventListener("click", function() {
      if (confirm("¿Cancelar todos los cambios pendientes?")) {
        fetch(`${BASE_URL}/mozo/cancelarCambios`, {
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
            mostrarAlerta("Cambios cancelados", "info");
            // Actualizar stocks antes de recargar
            actualizarTodosLosStocks();
            setTimeout(() => {
              location.reload();
            }, 1000);
          } else {
            mostrarAlerta("Error al cancelar cambios", "danger");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          mostrarAlerta("Error de conexión", "danger");
        });
      }
    });
  }

  // Botón Nueva Comanda
  const btnNuevaComanda = document.getElementById("btn-nueva-comanda");
  if (btnNuevaComanda) {
    btnNuevaComanda.addEventListener("click", function () {
      if (confirm("¿Deseas crear una nueva comanda para esta mesa?")) {
        const mesaId = document.getElementById("mesa-id").value;
        window.location.href = `${BASE_URL}/mozo/comanda/${mesaId}`;
      }
    });
  }

  // Botón Aceptar - Enviar a cocina
  if (btnAceptar) {
    btnAceptar.addEventListener("click", function () {
      // Verificar si hay items en la comanda
      const hayItems = comandaItems.querySelector('tr td[colspan="4"]') === null &&
                      comandaItems.querySelector('tr td[colspan="3"]') === null;

      if (!hayItems) {
        mostrarAlerta("La comanda está vacía", "warning");
        return;
      }

      confirmarEnvioModal.show();
    });
  }

  // Confirmar envío
  const btnConfirmarEnvio = document.getElementById("confirmarEnvio");
  if (btnConfirmarEnvio) {
    btnConfirmarEnvio.addEventListener("click", function () {
      // Deshabilitar botón mientras procesa
      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';

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
  }

  // Botón Salir
  if (btnSalir) {
    btnSalir.addEventListener("click", function () {
      // Verificar si hay cambios sin guardar
      const hayItems = comandaItems.querySelector('tr td[colspan="4"]') === null &&
                      comandaItems.querySelector('tr td[colspan="3"]') === null;

      if (!hayItems) {
        if (confirm("¿Deseas salir sin enviar la comanda? Los cambios se perderán.")) {
          window.location.href = `${BASE_URL}/mozo`;
        }
      } else {
        window.location.href = `${BASE_URL}/mozo`;
      }
    });
  }

  // Función para cambiar la interfaz a modo "enviada"
  function cambiarModoEnviada() {
    // Cambiar botón aceptar
    if (btnAceptar) {
      btnAceptar.textContent = "Nueva Comanda";
      btnAceptar.classList.remove("btn-success");
      btnAceptar.classList.add("btn-primary");
      btnAceptar.onclick = function () {
        if (confirm("¿Crear una nueva comanda para esta mesa?")) {
          location.reload();
        }
      };
    }

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
    const cardBody = document.querySelector(".card-body.p-0");
    if (cardBody) {
      cardBody.prepend(alertDiv);
    }
  }

  // Atajos de teclado
  document.addEventListener("keydown", function (e) {
    // F5 - Actualizar comanda
    if (e.key === "F5") {
      e.preventDefault();
      actualizarComanda();
      actualizarTodosLosStocks();
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

  // Cargar comanda inicial
  actualizarComanda();

  // Auto-actualizar cada 30 segundos
  setInterval(() => {
    actualizarComanda();
    actualizarTodosLosStocks();
  }, 30000);

  // Actualizar stocks al cargar la página
  setTimeout(actualizarTodosLosStocks, 1000);

  // Inicializar eventos de botones al cargar
  asignarEventosBotones();
});