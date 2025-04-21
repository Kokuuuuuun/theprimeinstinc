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
    const productElements = document.querySelectorAll('.product-container');
    if (!productElements.length) {
      throw new Error('No product elements found');
    }

    products = Array.from(productElements)
      .map((el) => {
        const name = el.querySelector('h3')?.textContent?.trim();
        const priceElement = el.querySelector('.price'); // Changed this line
        const priceText = priceElement?.textContent?.trim();
        const image = el.querySelector('img')?.getAttribute('src');

        if (!name || !priceText || !image) {
          console.warn('Incomplete product data:', { name, priceText, image });
          return null;
        }

        const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
        if (isNaN(price)) {
          console.warn('Invalid price format:', priceText);
          return null;
        }

        return {
          id: Math.random().toString(36).substr(2, 9),
          name,
          price,
          image,
        };
      })
      .filter(Boolean);

    // Add click handlers to buy buttons
    document.querySelectorAll('.buy').forEach((button, index) => {
      if (products[index]) {
        button.addEventListener('click', () => {
          addToCart(products[index]);
          console.log('Added to cart:', products[index]);
        });
      }
    });

    console.log('Loaded products:', products); // Debug line
  } catch (error) {
    console.error('Error loading products:', error);
  }
});

function updateCartDisplay() {
  const cartItems = document.getElementById('cart-items');
  const cartTotal = document.getElementById('cart-total');

  if (!cartItems || !cartTotal) {
    console.error('Cart elements not found');
    return;
  }

  cartItems.innerHTML = '';
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

  cart.forEach((item) => {
    const itemElement = document.createElement('div');
    itemElement.className = 'cart-item';
    itemElement.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="cart-item-info">
        <div>${item.name}</div>
        <div>Cantidad: ${item.quantity}</div>
        <div class="cart-item-price">$${(item.price * item.quantity).toFixed(
          2
        )}</div>
      </div>
      <div class="remove-item" onclick="removeFromCart('${item.id}')">✕</div>
    `;
    cartItems.appendChild(itemElement);
  });

  cartTotal.textContent = total.toFixed(2);
}

function addToCart(product) {
  if (!product || !product.name || !product.price) {
    console.error('Invalid product data:', product);
    return;
  }

  const existingItem = cart.find((item) => item.name === product.name);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({
      id: Math.random().toString(36).substr(2, 9), // Add unique ID
      name: product.name,
      price: parseFloat(product.price),
      quantity: 1,
      image: product.image || '',
    });
  }

  updateCartDisplay();
  console.log('Current cart:', cart);
}

function removeFromCart(productId) {
  if (!productId) return;
  cart = cart.filter((item) => item.id !== productId);
  updateCartDisplay();
}

function filterProducts(searchTerm) {
  if (typeof searchTerm !== 'string') return;

  document.querySelectorAll('.product-container').forEach((container) => {
    const title =
      container.querySelector('h3')?.textContent?.toLowerCase() || '';
    const description =
      container.querySelector('p')?.textContent?.toLowerCase() || '';

    container.style.display =
      title.includes(searchTerm) || description.includes(searchTerm)
        ? 'flex'
        : 'none';
  });
}

// Replace the existing price filter event listener
document
  .getElementById('price-filter')
  ?.addEventListener('change', function () {
    const sortOrder = this.value;
    const productsContainer = document.querySelector('.products');
    const productContainers = Array.from(
      document.querySelectorAll('.product-container')
    );

    if (!productsContainer) {
      console.error('Products container not found');
      return;
    }

    if (productContainers.length === 0) {
      console.error('No products found');
      return;
    }

    const sortedContainers = productContainers.sort((a, b) => {
      const priceA = extractPrice(a.querySelector('.price'));
      const priceB = extractPrice(b.querySelector('.price'));

      if (sortOrder === 'all') return 0;
      return sortOrder === 'low' ? priceA - priceB : priceB - priceA;
    });

    // Clear and repopulate the container with sorted items
    productsContainer.innerHTML = '';
    sortedContainers.forEach((container) => {
      productsContainer.appendChild(container);
    });
  });

// Update the extractPrice function
function extractPrice(element) {
  if (!element) return 0;
  const priceText = element.textContent.trim();
  // Remove currency symbol and any non-numeric characters except decimal point
  const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
  return isNaN(price) ? 0 : price;
}

// El carrito
document.addEventListener('click', function (event) {
  const cartDiv = document.getElementById('cart-div');
  const cartIcon = document.getElementById('cart-icon');

  if (
    cartDiv &&
    cartIcon &&
    !cartDiv.contains(event.target) &&
    !cartIcon.contains(event.target)
  ) {
    cartDiv.classList.remove('cart-div-active');
  }
});

// Updated search functionality to include new products
document.getElementById('search-input')?.addEventListener('input', function () {
  const searchTerm = this.value.toLowerCase();
  const products = document.querySelectorAll('.product-container');

  products.forEach((product) => {
    const name = product.querySelector('h3').textContent.toLowerCase();
    const description =
      product.querySelector('p')?.textContent.toLowerCase() || '';
    const price = product.querySelector('.price').textContent.toLowerCase();

    const matches =
      name.includes(searchTerm) ||
      description.includes(searchTerm) ||
      price.includes(searchTerm);

    product.style.display = matches ? 'flex' : 'none';
  });
});

// Reemplaza la función existente del checkout-btn
document.getElementById('checkout-btn').addEventListener('click', function () {
  if (cart.length === 0) {
    alert('El carrito está vacío');
    return;
  }

  // Create form to submit cart data
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'checkout.php';

  // Add cart items as hidden inputs
  cart.forEach((item, index) => {
    const nameInput = document.createElement('input');
    nameInput.type = 'hidden';
    nameInput.name = `products[${index}][name]`;
    nameInput.value = item.name;

    const priceInput = document.createElement('input');
    priceInput.type = 'hidden';
    priceInput.name = `products[${index}][price]`;
    priceInput.value = item.price;

    const quantityInput = document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.name = `products[${index}][quantity]`;
    quantityInput.value = item.quantity;

    const imageInput = document.createElement('input');
    imageInput.type = 'hidden';
    imageInput.name = `products[${index}][image]`;
    imageInput.value = item.image;

    form.appendChild(nameInput);
    form.appendChild(priceInput);
    form.appendChild(quantityInput);
    form.appendChild(imageInput);
  });

  // Add total
  const totalInput = document.createElement('input');
  totalInput.type = 'hidden';
  totalInput.name = 'total';
  totalInput.value = cart.reduce(
    (sum, item) => sum + item.price * item.quantity,
    0
  );
  form.appendChild(totalInput);

  document.body.appendChild(form);
  form.submit();
});

// Add this function for form validation
function validateProductForm(event) {
  event.preventDefault();

  const nombre = document.getElementById('nombre').value.trim();
  const descripcion = document.getElementById('descripcion').value.trim();
  const precio = document.getElementById('precio').value;
  const imagen = document.getElementById('imagen').files[0];

  let isValid = true;
  let errorMessage = '';

  // Reset previous error styles
  const inputs = document.querySelectorAll('.inputarea');
  inputs.forEach((input) => {
    input.style.borderColor = 'rgba(0, 0, 0, 0.1)';
  });

  if (nombre === '') {
    isValid = false;
    document.getElementById('nombre').style.borderColor = '#ff4444';
    errorMessage += 'El nombre del producto es requerido\n';
  }

  if (descripcion === '') {
    isValid = false;
    document.getElementById('descripcion').style.borderColor = '#ff4444';
    errorMessage += 'La descripción es requerida\n';
  }

  if (precio === '' || parseFloat(precio) <= 0) {
    isValid = false;
    document.getElementById('precio').style.borderColor = '#ff4444';
    errorMessage += 'El precio debe ser mayor a 0\n';
  }

  if (!imagen) {
    isValid = false;
    document.getElementById('imagen').style.borderColor = '#ff4444';
    errorMessage += 'La imagen es requerida\n';
  }

  if (!isValid) {
    return false;
  }

  // If validation passes, submit the form
  event.target.submit();
}

document.addEventListener('DOMContentLoaded', () => {
  const productForm = document.querySelector('form[action="add_product.php"]');
  if (productForm) {
    productForm.addEventListener('submit', validateProductForm);
  }
});

// Add user dropdown functionality
document.addEventListener('DOMContentLoaded', function () {
  const userIcon = document.getElementById('user-icon');
  const userDropdown = document.getElementById('user-dropdown');

  // Toggle dropdown when clicking user icon
  userIcon.addEventListener('click', function (e) {
    e.stopPropagation();
    userDropdown.classList.toggle('active');
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', function (e) {
    if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
      userDropdown.classList.remove('active');
    }
  });
});
