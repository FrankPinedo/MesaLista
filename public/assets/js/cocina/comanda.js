// Variables globales
let currentItemToCancel = null;
let currentOrderToCancel = null;
let previousOrderIds = [];
let eventListeners = {};

const BASE_URL = window.location.origin + "/MesaLista";

// Funciones de utilidad
function showToast(message, type = "success") {
  const toast = document.createElement("div");
  toast.className = `fixed bottom-4 right-4 px-4 py-2 shadow-lg text-white p-4 border-l-4 ${
    type === "success" ? "bg-green-500" : "bg-red-500"
  }`;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}

// Funciones para manejar modales
function openCancelItemModal(itemId) {
  currentItemToCancel = itemId;
  document.getElementById("cancelItemModal").classList.remove("hidden");
}

// Funciones para manejar modales de cancelación
function closeCancelItemModal() {
  document.getElementById("cancelItemModal").classList.add("hidden");
  currentItemToCancel = null;
}
function openCancelOrderModal(orderId) {
  currentOrderToCancel = orderId;
  document.getElementById("cancelOrderModal").classList.remove("hidden");
}
function closeCancelOrderModal() {
  document.getElementById("cancelOrderModal").classList.add("hidden");
  currentOrderToCancel = null;
}

// Renderizado principal de comandas
function renderOrders(orders) {
  const container = document.querySelector(".grid.grid-cols-1");

  if (!orders || !Array.isArray(orders)) {
    console.error("Datos de órdenes no válidos recibidos:", orders);
    return;
  }

  container.innerHTML = "";

  if (orders.length === 0) {
    container.innerHTML = `
      <div class="col-span-full flex flex-col items-center justify-center py-12">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-700">No hay comandas pendientes</h3>
        <p class="mt-1 text-sm text-gray-500">Las nuevas comandas aparecerán aquí automáticamente</p>
      </div>
    `;

    previousOrderIds = [];
    return;
  }

  const newOrderIds = orders.map((order) => order.id);
  const newOrders = newOrderIds.filter((id) => !previousOrderIds.includes(id));

  orders.sort((a, b) => {
    const estadoOrden = { recibido: 0, pendiente: 1 };
    return estadoOrden[a.estado] - estadoOrden[b.estado];
  });

  orders.forEach((order, index) => {
    const isNew = newOrders.includes(order.id);
    const orderElement = createOrderElement(order, index + 1, isNew);
    container.appendChild(orderElement);
  });

  previousOrderIds = newOrderIds;
}

// Aplicar parpadeo según tiempo y estado
function applyBlinkEffect(orderElement, order) {
  const minutes = order.minutos_transcurridos;
  orderElement.classList.remove("blink", "blink-red", "blink-red-intense");

  if (order.estado === "pendiente") {
    if (minutes > 10) {
      orderElement.classList.add("blink-red");
    } else if (minutes > 6) {
      orderElement.classList.add("blink");
    }
  } else if (order.estado === "recibido") {
    if (minutes > 15) {
      orderElement.classList.add("blink-red-intense");
    }
  }
}

// Crear elemento HTML de una comanda
function createOrderElement(order, orderNumber, isNew = false) {
  const orderElement = document.createElement("div");
  orderElement.className =
    "shadow-md border rounded-md order-card flex flex-col transition-all duration-300 ease-in-out";
  if (isNew) {
    orderElement.classList.add("order-highlight");
  }

  orderElement.dataset.id = order.id;
  orderElement.dataset.estado = order.estado;

  // Header de la comanda
  const header = `
        <div class="p-3">
            <div class="relative flex justify-between items-center text-sm">
                <span class="font-bold z-10">#${orderNumber}</span>
                <span class="absolute left-1/2 -translate-x-1/2 text-gray-500 font-bold">ORD#${
                  order.id
                }</span>
                <span class="text-red-500 font-bold z-10">${
                  order.minutos_transcurridos
                } min</span>
            </div>
        </div>
        <div>
             <span class="justify-center inline-flex items-center px-3 py-2 w-100 ${
               order.tipo_entrega === "para llevar"
                 ? "bg-red-200 text-red-800"
                 : order.tipo_entrega === "delivery"
                 ? "bg-green-200 text-green-800"
                 : "bg-blue-200 text-blue-800"
             } text-sm text-center">
                    <span class="font-bold uppercase">${
                      order.tipo_entrega
                    }</span>
              </span>
        </div>
    `;

  // Items de la comanda
  const itemsList = document.createElement("div");
  itemsList.className = "flex-1 overflow-y-auto order-items p-2 space-y-2";

  order.items.forEach((item) => {
    const itemElement = createItemElement(item);
    itemsList.appendChild(itemElement);
  });

  // Botones de acción
  const buttons = `
  <div class="p-3">
    <div class="flex gap-2">
      <button onclick="updateOrderStatus(${order.id}, 'recibido')" 
        class="flex-1 py-2 rounded text-sm transition-colors font-bold
          ${
            order.estado === "recibido"
              ? "bg-gray-400 cursor-not-allowed text-white"
              : "bg-sky-400 hover:bg-sky-600 text-white"
          }"
        ${order.estado === "recibido" ? "disabled" : ""}>
        Recibido
      </button>
      <button onclick="updateOrderStatus(${order.id}, 'listo')" 
        class="flex-1 py-2 rounded text-sm transition-colors font-bold
          ${
            order.estado !== "recibido"
              ? "bg-gray-400 cursor-not-allowed text-white"
              : "bg-green-500 hover:bg-green-600 text-white"
          }"
        ${order.estado !== "recibido" ? "disabled" : ""}>
        Listo
      </button>
    </div>
    <button onclick="openCancelOrderModal(${order.id})" 
      class="flex-1 py-2 mt-2 w-full bg-red-500 text-white rounded text-sm hover:bg-red-600 font-bold">
      Cancelar
    </button>
  </div>
`;

  orderElement.innerHTML = header;
  orderElement.appendChild(itemsList);

  const buttonsWrapper = document.createElement("div");
  buttonsWrapper.innerHTML = buttons;
  orderElement.appendChild(buttonsWrapper);

  applyBlinkEffect(orderElement, order);
  return orderElement;
}

// Crear elemento HTML para cada ítem de comanda
function createItemElement(item) {
  const itemElement = document.createElement("div");
  itemElement.className = "group relative";

  let itemHTML = `
        <div class="grid grid-cols-6 gap-1 items-center py-2">
            <div class="col-span-1 font-medium text-center">${item.cantidad}</div>
            <div class="col-span-4">${item.nombre}</div>
            <div class="col-span-1 flex justify-end">
                <button onclick="openCancelItemModal(${item.id})" 
                    class="cancel-item-btn p-1 text-gray-400 hover:text-red-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    `;

  // Agregar observaciones si existen
  if (item.comentario) {
    itemHTML += `
            <div class="grid grid-cols-6 gap-1 text-sm pl-2">
                <div class="col-span-1"></div>
                <div class="col-span-4">
                    <div>• ${item.comentario}</div>
                </div>
            </div>
        `;
  }

  // Agregar guarniciones si existen
  if (item.guarniciones && item.guarniciones.length > 0) {
    itemHTML += `
            <div class="grid grid-cols-6 gap-1 text-sm pl-2">
                <div class="col-span-1"></div>
                <div class="col-span-4">
                    ${item.guarniciones
                      .map((g) => `<div>• ${g.nombre}</div>`)
                      .join("")}
                </div>
            </div>
        `;
  }

  itemElement.innerHTML = itemHTML;
  return itemElement;
}

// Funciones modificadas para manejo de event listeners -- Tiempo 5s
function setupEventListeners() {
  cleanupEventListeners();

  eventListeners.confirmCancelItem = cancelItem;
  eventListeners.confirmCancelOrder = cancelOrder;
  eventListeners.fetchOrders = fetchOrders;

  document
    .getElementById("confirmCancelItem")
    .addEventListener("click", eventListeners.confirmCancelItem);
  document
    .getElementById("confirmCancelOrder")
    .addEventListener("click", eventListeners.confirmCancelOrder);

  eventListeners.pollingInterval = setInterval(
    eventListeners.fetchOrders,
    5000
  );
}

function cleanupEventListeners() {
  if (eventListeners.confirmCancelItem) {
    document
      .getElementById("confirmCancelItem")
      .removeEventListener("click", eventListeners.confirmCancelItem);
  }

  if (eventListeners.confirmCancelOrder) {
    document
      .getElementById("confirmCancelOrder")
      .removeEventListener("click", eventListeners.confirmCancelOrder);
  }

  if (eventListeners.pollingInterval) {
    clearInterval(eventListeners.pollingInterval);
  }

  eventListeners = {};
}

// Obtener comandas desde el backend
async function fetchOrders() {
  try {
    const response = await fetch(`${BASE_URL}/cocina/obtenerComandas`, {
      credentials: "include",
    });

    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      const text = await response.text();
      console.error("Respuesta no JSON recibida:", text.substring(0, 100));
      throw new Error("El servidor respondió con HTML en lugar de JSON");
    }

    const orders = await response.json();
    if (!Array.isArray(orders)) {
      throw new Error("Datos recibidos no son un array de órdenes");
    }

    renderOrders(orders);
  } catch (error) {
    console.error("Error completo:", error);
    showToast("Error al cargar comandas: " + error.message, "error");

    if (error.message.includes("HTML") || error.message.includes("403")) {
      cleanupEventListeners();
      setTimeout(() => location.reload(), 2000);
    }
  }
}

// Actualizar estado de una comanda
async function updateOrderStatus(orderId, status) {
  const allowedTransitions = {
    recibido: ["pendiente"],
    listo: ["recibido"],
  };

  const order = document.querySelector(`[data-id='${orderId}']`);
  const currentState = order?.dataset.estado;

  if (currentState && !allowedTransitions[status]?.includes(currentState)) {
    showToast(
      `No se puede marcar como '${status}' desde '${currentState}'`,
      "error"
    );
    return;
  }

  try {
    const response = await fetch(`${BASE_URL}/cocina/actualizarEstado`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        comanda_id: orderId,
        estado: status,
      }),
    });

    const result = await response.json();

    if (result.success) {
      showToast(`Estado actualizado a ${status}`);

      if (status === "recibido") {
        window.scrollTo({ top: 0, behavior: "smooth" });
      }

      fetchOrders();
    } else {
      showToast(result.error || "Error al actualizar estado", "error");
    }
  } catch (error) {
    console.error("Error al actualizar estado:", error);
    showToast("Error al actualizar estado", "error");
  }
}

// Cancelar ítem individual
async function cancelItem() {
  if (!currentItemToCancel) return;

  try {
    const response = await fetch(`${BASE_URL}/cocina/cancelarItem`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        item_id: currentItemToCancel,
      }),
    });

    const result = await response.json();

    if (result.success) {
      showToast("Ítem cancelado correctamente");
      closeCancelItemModal();
      fetchOrders();
    } else {
      showToast(result.error || "Error al cancelar ítem", "error");
    }
  } catch (error) {
    console.error("Error al cancelar ítem:", error);
    showToast("Error al cancelar ítem", "error");
  }
}

// Cancelar comanda completar
async function cancelOrder() {
  if (!currentOrderToCancel) return;

  try {
    const response = await fetch(`${BASE_URL}/cocina/cancelarComanda`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        comanda_id: currentOrderToCancel,
      }),
    });

    const result = await response.json();

    if (result.success) {
      showToast("Comanda cancelada correctamente");
      closeCancelOrderModal();
      fetchOrders();
    } else {
      showToast(result.error || "Error al cancelar comanda", "error");
    }
  } catch (error) {
    console.error("Error al cancelar comanda:", error);
    showToast("Error al cancelar comanda", "error");
  }
}

// Inicialización: carga y polling de comandas
document.addEventListener("DOMContentLoaded", () => {
  setupEventListeners();
  fetchOrders();
});

// Limpieza cuando se descarga la página
window.addEventListener("beforeunload", () => {
  cleanupEventListeners();
});
  