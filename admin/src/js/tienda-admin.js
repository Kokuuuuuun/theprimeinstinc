/** @format */

// Menu toggle functionality
document.getElementById('menu-toggle')?.addEventListener('click', function () {
  document.getElementById('menu-links')?.classList.toggle('active');
  this.classList.toggle('m-active');
});

// Single cart icon toggle with improved error handling
document.getElementById('cart-icon')?.addEventListener('click', function () {
  const cartDiv = document.getElementById('cart-div');
  if (cartDiv) {
    cartDiv.classList.toggle('cart-div-active');
  } else {
    console.error('Cart div element not found');
  }
});

// Consolidated search functionality with debouncing
let searchTimeout;
document.getElementById('search-input')?.addEventListener('input', function () {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    const searchTerm = this.value.toLowerCase();
    filterProducts(searchTerm);
  }, 300);
});

let cart = [];
let products = [];

// Improved products loading with error handling
document.addEventListener('DOMContentLoaded', function () {
  try {
    // Asignar evento al enlace de añadir producto si existe
    document.getElementById('add-product-link')?.addEventListener('click', function(e) {
      e.preventDefault();
      const modal = document.getElementById('product-modal');
      if (modal) {
        modal.style.display = 'block';
      }
    });

    const productElements = document.querySelectorAll('.product-container');
    if (!productElements.length) {
      console.warn('No product elements found');
      return; // No error, just return
    }

    products = Array.from(productElements)
      .map((el) => {
        const name = el.querySelector('h3')?.textContent?.trim();
        const priceElement = el.querySelector('.price');
        const priceText = priceElement?.textContent?.trim();
        const image = el.querySelector('img')?.getAttribute('src');

        if (!name || !priceText || !image) {
          console.warn('Incomplete product data:', { name, priceText, image });
          return null;
        }

        // Extract numeric value from price text (e.g., "$150.00" -> 150.00)
        const price = parseFloat(priceText.replace(/[^\d.-]/g, ''));

        return { name, price, image };
      })
      .filter(Boolean); // Filter out null values

    console.log('Loaded products:', products);
  } catch (error) {
    console.error('Error initializing products:', error);
  }

  // Carrusel
  let currentIndex = 0;
  const carouselItems = document.querySelectorAll('.carousel-item');
  const totalItems = carouselItems.length;

  function showSlide(index) {
    carouselItems.forEach((item) => {
      item.classList.remove('active');
    });
    carouselItems[index].classList.add('active');
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % totalItems;
    showSlide(currentIndex);
  }

  // Auto cambio de slide cada 3 segundos
  setInterval(nextSlide, 3000);

  // Modal para añadir producto
  const modal = document.getElementById('product-modal');
  const addBtn = document.getElementById('add-product-btn');
  const closeBtn = document.getElementById('close-modal');

  if (addBtn && modal) {
    addBtn.addEventListener('click', function () {
      modal.style.display = 'block';
    });
  }

  if (closeBtn && modal) {
    closeBtn.addEventListener('click', function () {
      modal.style.display = 'none';
    });
  }

  // Cerrar modal al hacer clic fuera
  window.addEventListener('click', function (event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });

  // Filtrado de productos por precio
  const priceFilter = document.getElementById('price-filter');
  if (priceFilter) {
    priceFilter.addEventListener('change', function () {
      const searchTerm = document.getElementById('search-input')?.value.toLowerCase() || '';
      filterProducts(searchTerm);
    });
  }

  // Añadir estilos para las notificaciones
  const styleElement = document.createElement('style');
  styleElement.textContent = `
    .cart-notification {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      opacity: 0;
      transform: translateY(-20px);
      transition: opacity 0.3s, transform 0.3s;
      pointer-events: none;
    }

    .cart-notification.show {
      opacity: 1;
      transform: translateY(0);
    }

    .cart-counter {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: #ff4444;
      color: white;
      font-size: 12px;
      font-weight: bold;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      pointer-events: none;
      user-select: none;
    }

    .cart-pulse {
      animation: pulse 0.5s 2;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.2);
      }
      100% {
        transform: scale(1);
      }
    }
  `;

  document.head.appendChild(styleElement);

  // Inicializar el contador del carrito
  updateCartCounter();

  // Mostrar el carrito al cargar la página si tiene elementos
  updateCartDisplay();
});

// Función para filtrar productos
function filterProducts(searchTerm) {
  const priceFilter = document.getElementById('price-filter')?.value || 'all';
  const productContainers = document.querySelectorAll('.product-container');

  productContainers.forEach((container) => {
    const name = container.querySelector('h3')?.textContent.toLowerCase() || '';
    const description = container.querySelector('p')?.textContent.toLowerCase() || '';
    const priceText = container.querySelector('.price')?.textContent || '';
    const price = parseFloat(priceText.replace(/[^\d.-]/g, ''));

    let showByPrice = true;

    // Aplicar filtro de precio
    if (priceFilter !== 'all') {
      const allPrices = Array.from(productContainers).map((pc) => {
        const pt = pc.querySelector('.price')?.textContent || '';
        return parseFloat(pt.replace(/[^\d.-]/g, ''));
      }).filter((p) => !isNaN(p));

      const minPrice = Math.min(...allPrices);
      const maxPrice = Math.max(...allPrices);
      const midPrice = (minPrice + maxPrice) / 2;

      if (priceFilter === 'low' && price > midPrice) {
        showByPrice = false;
      } else if (priceFilter === 'high' && price < midPrice) {
        showByPrice = false;
      }
    }

    // Mostrar u ocultar según término de búsqueda y filtro de precio
    const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm);
    container.style.display = matchesSearch && showByPrice ? 'block' : 'none';
  });
}

// Función para añadir al carrito
function addToCart(name, price, image) {
  const existingItem = cart.find((item) => item.name === name);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({
      name,
      price,
      image,
      quantity: 1,
    });
  }

  // Actualizar el carrito visualmente
  updateCartDisplay();

  // Mostrar una notificación visual
  showCartNotification(name);

  // Si el carrito no está visible, mostrar un indicador numérico
  updateCartCounter();
}

// Función para mostrar notificación visual al añadir producto
function showCartNotification(productName) {
  // Crear elemento de notificación
  const notification = document.createElement('div');
  notification.className = 'cart-notification';
  notification.innerHTML = `<span>¡Añadido al carrito: ${productName}!</span>`;

  // Añadir al DOM
  document.body.appendChild(notification);

  // Mostrar con animación
  setTimeout(() => {
    notification.classList.add('show');
  }, 10);

  // Remover después de 3 segundos
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      notification.remove();
    }, 500); // Tiempo para que termine la animación
  }, 3000);

  // Animar también el ícono del carrito
  const cartIcon = document.getElementById('cart-icon');
  if (cartIcon) {
    cartIcon.classList.add('cart-pulse');
    setTimeout(() => {
      cartIcon.classList.remove('cart-pulse');
    }, 1000);
  }
}

// Función para actualizar el contador del carrito
function updateCartCounter() {
  // Verificar si ya existe el contador
  let counter = document.getElementById('cart-counter');

  // Si no existe, crearlo
  if (!counter) {
    counter = document.createElement('span');
    counter.id = 'cart-counter';
    counter.className = 'cart-counter';

    // Añadir al ícono del carrito
    const cartIcon = document.getElementById('cart-icon');
    if (cartIcon) {
      cartIcon.style.position = 'relative'; // Asegurar que el icono tenga posición relativa para el contador
      cartIcon.appendChild(counter);
    }
  }

  // Calcular la cantidad total de items
  const totalItems = cart.reduce((total, item) => total + item.quantity, 0);

  // Actualizar el contador
  if (totalItems > 0) {
    counter.textContent = totalItems;
    counter.style.display = 'flex';
  } else {
    counter.style.display = 'none';
  }
}

// Función para actualizar el carrito
function updateCartDisplay() {
  const cartItems = document.getElementById('cart-items');
  const cartTotal = document.getElementById('cart-total');

  if (!cartItems || !cartTotal) {
    console.error('Cart elements not found');
    return;
  }

  cartItems.innerHTML = '';
  let total = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const cartItem = document.createElement('div');
    cartItem.className = 'cart-item';
    cartItem.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="cart-item-details">
        <h4>${item.name}</h4>
        <p>$${item.price.toFixed(2)} x ${item.quantity}</p>
      </div>
      <div class="cart-item-actions">
        <button onclick="updateCartItemQuantity(${index}, ${item.quantity - 1})">-</button>
        <span>${item.quantity}</span>
        <button onclick="updateCartItemQuantity(${index}, ${item.quantity + 1})">+</button>
        <button onclick="removeCartItem(${index})">🗑️</button>
      </div>
    `;

    cartItems.appendChild(cartItem);
  });

  cartTotal.textContent = total.toFixed(2);

  // Mostrar u ocultar el botón de checkout
  const checkoutBtn = document.getElementById('checkout-btn');
  if (checkoutBtn) {
    checkoutBtn.style.display = cart.length > 0 ? 'block' : 'none';
  }
}

// Función para actualizar cantidad de item en carrito
function updateCartItemQuantity(index, newQuantity) {
  if (newQuantity <= 0) {
    removeCartItem(index);
  } else {
    cart[index].quantity = newQuantity;
    updateCartDisplay();
    updateCartCounter();
  }
}

// Función para eliminar item del carrito
function removeCartItem(index) {
  cart.splice(index, 1);
  updateCartDisplay();
  updateCartCounter();
}

// Validación del formulario para añadir producto
document.getElementById('add-product-form')?.addEventListener('submit', function (e) {
  let isValid = true;
  const nombre = document.getElementById('nombre');
  const descripcion = document.getElementById('descripcion');
  const precio = document.getElementById('precio');
  const imagen = document.getElementById('imagen');

  // Resetear mensajes de error
  document.querySelectorAll('.error-message').forEach((el) => {
    el.textContent = '';
  });

  // Validar nombre
  if (!nombre?.value.trim()) {
    document.getElementById('nombre-error').textContent = 'El nombre es obligatorio';
    isValid = false;
  }

  // Validar descripción
  if (!descripcion?.value.trim()) {
    document.getElementById('descripcion-error').textContent = 'La descripción es obligatoria';
    isValid = false;
  }

  // Validar precio
  if (!precio?.value || isNaN(precio.value) || parseFloat(precio.value) <= 0) {
    document.getElementById('precio-error').textContent = 'El precio debe ser un número positivo';
    isValid = false;
  }

  // Validar imagen
  if (!imagen?.files.length) {
    document.getElementById('imagen-error').textContent = 'La imagen es obligatoria';
    isValid = false;
  } else {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    if (!allowedTypes.includes(imagen.files[0].type)) {
      document.getElementById('imagen-error').textContent = 'El formato de imagen no es válido';
      isValid = false;
    }
  }

  if (!isValid) {
    e.preventDefault();
  }
});
