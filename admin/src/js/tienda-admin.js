/** @format */

// Menu toggle functionality
document.getElementById("menu-toggle")?.addEventListener("click", function () {
  document.getElementById("menu-links")?.classList.toggle("active");
  this.classList.toggle("m-active");
});

// Single cart icon toggle with improved error handling
document.getElementById("cart-icon")?.addEventListener("click", function () {
  const cartDiv = document.getElementById("cart-div");
  if (cartDiv) {
    cartDiv.classList.toggle("cart-div-active");
  } else {
    console.error("Cart div element not found");
  }
});

// Consolidated search functionality with debouncing
let searchTimeout;
document.getElementById("search-input")?.addEventListener("input", function () {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    const searchTerm = this.value.toLowerCase();
    filterProducts(searchTerm);
  }, 300);
});

let cart = [];
let products = [];

// Improved products loading with error handling
document.addEventListener("DOMContentLoaded", () => {
  try {
    const productElements = document.querySelectorAll(".product-container");
    if (!productElements.length) {
      throw new Error("No product elements found");
    }

    products = Array.from(productElements)
      .map((el) => {
        const name = el.querySelector("h3")?.textContent?.trim();
        const priceElement = el.querySelector(".price"); // Changed this line
        const priceText = priceElement?.textContent?.trim();
        const image = el.querySelector("img")?.getAttribute("src");

        if (!name || !priceText || !image) {
          console.warn("Incomplete product data:", { name, priceText, image });
          return null;
        }

        const price = Number.parseFloat(priceText.replace(/[^0-9.]/g, ""));
        if (Number.isNaN(price)) {
          console.warn("Invalid price format:", priceText);
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
    document.querySelectorAll(".buy").forEach((button, index) => {
      if (products[index]) {
        button.addEventListener("click", () => {
          addToCart(products[index]);
          console.log("Added to cart:", products[index]);
        });
      }
    });

    console.log("Loaded products:", products); // Debug line
  } catch (error) {
    console.error("Error loading products:", error);
  }
});

function updateCartDisplay() {
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  if (!cartItems || !cartTotal) {
    console.error("Cart elements not found");
    return;
  }

  cartItems.innerHTML = "";
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

  // biome-ignore lint/complexity/noForEach: <explanation>
  cart.forEach((item) => {
    const itemElement = document.createElement("div");
    itemElement.className = "cart-item";
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
    console.error("Invalid product data:", product);
    return;
  }

  const existingItem = cart.find((item) => item.name === product.name);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push({
      name: product.name,
      price: Number.parseFloat(product.price),
      quantity: 1,
      image: product.image || "",
    });
  }

  updateCartDisplay();
  console.log("Current cart:", cart); // Para debugging
}

function removeFromCart(productId) {
  if (!productId) return;
  cart = cart.filter((item) => item.id !== productId);
  updateCartDisplay();
}

function filterProducts(searchTerm) {
  if (typeof searchTerm !== "string") return;

  // biome-ignore lint/complexity/noForEach: <explanation>
  document.querySelectorAll(".product-container").forEach((container) => {
    const title =
      container.querySelector("h3")?.textContent?.toLowerCase() || "";
    const description =
      container.querySelector("p")?.textContent?.toLowerCase() || "";

    container.style.display =
      title.includes(searchTerm) || description.includes(searchTerm)
        ? "flex"
        : "none";
  });
}

document
  .getElementById("price-filter")
  ?.addEventListener("change", function () {
    const sortOrder = this.value;
    const productsContainer = document.querySelector(
      ".products-grid, .products"
    );

    if (!productsContainer) {
      console.error("Products container not found");
      return;
    }

    if (!productContainers.length) return;

    const sortedContainers = productContainers.sort((a, b) => {
      const priceB = extractPrice(b.querySelector("#precio, p"));
      return sortOrder === "low" ? priceA - priceB : priceB - priceA;
    });

    // biome-ignore lint/complexity/noForEach: <explanation>
    sortedContainers.forEach((container) =>
      productsContainer.appendChild(container)
    );
  });

// funcion pa amanga esos precios
function extractPrice(element) {
  if (!element) return 0;
  const price = Number.parseFloat(element.textContent.replace(/[^\d.]/g, ""));
  return Number.isNaN(price) ? 0 : price;
}

// El carrito
document.addEventListener("click", (event) => {
  const cartDiv = document.getElementById("cart-div");
  const cartIcon = document.getElementById("cart-icon");

  if (
    cartDiv &&
    cartIcon &&
    !cartDiv.contains(event.target) &&
    !cartIcon.contains(event.target)
  ) {
    cartDiv.classList.remove("cart-div-active");
  }
});

// Se declaran las variables del modal
const modal = document.getElementById("product-modal");
const addProductBtn = document.getElementById("add-product-btn");
const closeModal = document.querySelector("#close-modal");

addProductBtn.addEventListener("click", () => {
  modal.style.display = "block";
});

closeModal.addEventListener("click", () => {
  modal.style.display = "none";
});

window.addEventListener("click", (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
});

// Add product functionality
document.getElementById("add-product-form")?.addEventListener("submit", (e) => {
  e.preventDefault();

  const imageFile = document.getElementById("imagen").files[0];
  const name = document.getElementById("nombre").value.trim();
  const description = document.getElementById("descripcion").value.trim();
  const price = Number.parseFloat(document.getElementById("precio").value);

  // Validar formulario
  if (!name || !description || Number.isNaN(price)) {
    return;
  }

  // Validate image
  if (!imageFile) {
    return;
  }

  if (!imageFile.type.startsWith("image/")) {
    return;
  }

  const reader = new FileReader();

  reader.onerror = () => {
    alert("Error al leer la imagen");
  };

  reader.onload = () => {
    const newProduct = {
      name,
      description,
      price,
      image: reader.result,
      id: Date.now().toString(),
    };

    // Create new product element
    const productElement = document.createElement("div");
    productElement.className = "product-container";
    productElement.innerHTML = `
            <img class="product" src="${reader.result}" alt="${name}">
            <h3>${name}</h3>
            <p>${description}</p>
            <p class="price">$${price.toFixed(2)}</p>
            <button class="buy">Comprar</button>
            <button class="delete-product" onclick="deleteProduct(this.parentElement)">
                <i class='bx bx-trash'></i>
            </button>
        `;

    // Add buy functionality
    const buyButton = productElement.querySelector(".buy");
    buyButton.addEventListener("click", () => addToCart(newProduct));

    // Add to DOM
    document.querySelector(".products").appendChild(productElement);

    // Reset form and close modal
    e.target.reset();
    modal.style.display = "none";
  };

  // Read the image file as data URL
  reader.readAsDataURL(imageFile);
});

// asi se añade la imagen
document.getElementById("imagen")?.addEventListener("change", (e) => {
  const preview = document.getElementById("image-preview");
  if (!preview) {
    console.error("Elemento image-preview no encontrado");
    return;
  }

  const file = e.target.files[0];
  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
    };
    reader.readAsDataURL(file);
  } else {
    preview.innerHTML =
      '<span style="color: #888;">Vista previa de la imagen</span>';
    alert("Por favor selecciona un archivo de imagen válido");
  }
});

// el boton de borra tu sabe xdxd

document
  .getElementById("price-filter")
  ?.addEventListener("change", function () {
    const sortOrder = this.value;
    const productsContainer = document.querySelector(".products");

    if (!productsContainer || sortOrder === "all") return;

    const productContainers = Array.from(
      document.querySelectorAll(".product-container")
    );

    const sortedContainers = productContainers.sort((a, b) => {
      const priceA = parseFloat(
        eFloat(a.querySelector(".price").textContent.replace(/[^\d.]/g, ""))
      );
      const priceB = parseFloat(
        eFloat(b.querySelector(".price").textContent.replace(/[^\d.]/g, ""))
      );
      return sortOrder === "low" ? priceA - priceB : priceB - priceA;
    });

    // Clear and re-append sorted products
    productsContainer.innerHTML = "";
    sortedContainers.forEach((container) =>
      productsContainer.appendChild(container)
    );
  });

// Updated product form submission
document
  .getElementById("add-product-form")
  ?.addEventListener("submit", function (e) {
    e.preventDefault();

    const imageFile = document.getElementById("imagen").files[0];
    if (!imageFile) {
      alert("Por favor selecciona una imagen");
      return;
    }

    // Validate image type
    if (!imageFile.type.match("image.*")) {
      alert("Por favor selecciona un archivo de imagen válido");
      return;
    }

    const reader = new FileReader();
    reader.onload = function (event) {
      const newProduct = {
        name: document.getElementById("nombre").value.trim(),
        description: document.getElementById("descripcion").value.trim(),
        price: parseFloat(document.getElementById("precio").value),
        image: event.target.result,
        id: Date.now().toString(),
      };

      // Create new product element
      const productElement = document.createElement("div");
      productElement.className = "product-container";
      productElement.innerHTML = `
            <img class="product" src="${newProduct.image}" alt="${
        newProduct.name
      }">
            <h3>${newProduct.name}</h3>
            <p>${newProduct.description}</p>
            <p class="price">$${newProduct.price.toFixed(2)}</p>
            <button class="buy">Comprar</button>
            <button class="delete-product" onclick="deleteProduct(this.parentElement)">
                <i class='bx bx-trash'></i>
            </button>
        `;

      // Add buy functionality
      const buyButton = productElement.querySelector(".buy");
      buyButton.addEventListener("click", () => addToCart(newProduct));

      // Add to DOM
      document.querySelector(".products").appendChild(productElement);

      // Reset form and close modal
      this.reset();
      modal.style.display = "none";
    };

    // Read the image file as data URL
    reader.readAsDataURL(imageFile);
  });

// Updated search functionality to include new products

document
  .getElementById("search-input")
  .addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();
    const products = document.querySelectorAll(".product-container");

    products.forEach((product) => {
      const name = product.querySelector("h3")?.textContent?.toLowerCase() || "";
      const description = product.querySelector("p")?.textContent?.toLowerCase() || "";
      const price = product.querySelector(".price")?.textContent?.toLowerCase() || "";

      const matches =
        name.includes(searchTerm) ||
        description.includes(searchTerm) ||
        price.includes(searchTerm);

      product.style.display = matches ? "flex" : "none";
    });
  });

function extractPrice(priceString) {
  return parseFloat(priceString.replace(/[^\d.]/g, "")) || 0;
}

// Reemplaza la función existente del checkout-btn
document.getElementById("checkout-btn").addEventListener("click", function () {
  if (cart.length === 0) {
    alert("El carrito está vacío");
    return;
  }

  // Create form to submit cart data
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "checkout.php";

  // Add cart items as hidden inputs
  cart.forEach((item, index) => {
    const nameInput = document.createElement("input");
    nameInput.type = "hidden";
    nameInput.name = `products[${index}][name]`;
    nameInput.value = item.name;

    const priceInput = document.createElement("input");
    priceInput.type = "hidden";
    priceInput.name = `products[${index}][price]`;
    priceInput.value = item.price;

    const quantityInput = document.createElement("input");
    quantityInput.type = "hidden";
    quantityInput.name = `products[${index}][quantity]`;
    quantityInput.value = item.quantity;

    const imageInput = document.createElement("input");
    imageInput.type = "hidden";
    imageInput.name = `products[${index}][image]`;
    imageInput.value = item.image;

    form.appendChild(nameInput);
    form.appendChild(priceInput);
    form.appendChild(quantityInput);
    form.appendChild(imageInput);
  });

  // Add total
  const totalInput = document.createElement("input");
  totalInput.type = "hidden";
  totalInput.name = "total";
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

  const nombre = document.getElementById("nombre").value.trim();
  const descripcion = document.getElementById("descripcion").value.trim();
  const precio = document.getElementById("precio").value;
  const imagen = document.getElementById("imagen").files[0];

  let isValid = true;
  let errorMessage = "";

  // Reset previous error styles
  const inputs = document.querySelectorAll(".inputarea");
  inputs.forEach((input) => {
    input.style.borderColor = "rgba(0, 0, 0, 0.1)";
  });

  if (nombre === "") {
    isValid = false;
    document.getElementById("nombre").style.borderColor = "#ff4444";
    errorMessage += "El nombre del producto es requerido\n";
  }

  if (descripcion === "") {
    isValid = false;
    document.getElementById("descripcion").style.borderColor = "#ff4444";
    errorMessage += "La descripción es requerida\n";
  }

  if (precio === "" || parseFloat(precio) <= 0) {
    isValid = false;
    document.getElementById("precio").style.borderColor = "#ff4444";
    errorMessage += "El precio debe ser mayor a 0\n";
  }

  if (!imagen) {
    isValid = false;
    document.getElementById("imagen").style.borderColor = "#ff4444";
    errorMessage += "La imagen es requerida\n";
  }

  if (!isValid) {
    return false;
  }

  // If validation passes, submit the form
  event.target.submit();
}

document.addEventListener("DOMContentLoaded", () => {
  const productForm = document.querySelector('form[action="add_product.php"]');
  if (productForm) {
    productForm.addEventListener("submit", validateProductForm);
  }
});

// Add user dropdown functionality
document.addEventListener("DOMContentLoaded", function () {
  const userIcon = document.getElementById("user-icon");
  const userDropdown = document.getElementById("user-dropdown");

  // Toggle dropdown when clicking user icon
  userIcon.addEventListener("click", function (e) {
    e.stopPropagation();
    userDropdown.classList.toggle("active");
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
      userDropdown.classList.remove("active");
    }
  });
});

// Add product form validation
document
  .getElementById("add-product-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    // Limpiar mensajes de error previos
    document.querySelectorAll(".error-message").forEach((elem) => {
      elem.textContent = "";
      elem.style.display = "none";
    });

    // Remover clase de error de todos los inputs
    document.querySelectorAll(".inputarea").forEach((input) => {
      input.classList.remove("error");
    });

    let isValid = true;
    const imageFile = document.getElementById("imagen").files[0];
    const nombre = document.getElementById("nombre");
    const descripcion = document.getElementById("descripcion");
    const precio = document.getElementById("precio");

    // Validar nombre
    if (!nombre.value.trim()) {
      document.getElementById("nombre-error").textContent =
        "El nombre del producto es requerido";
      document.getElementById("nombre-error").style.display = "block";
      nombre.classList.add("error");
      isValid = false;
    }

    // Validar descripción
    if (!descripcion.value.trim()) {
      document.getElementById("descripcion-error").textContent =
        "La descripción es requerida";
      document.getElementById("descripcion-error").style.display = "block";
      descripcion.classList.add("error");
      isValid = false;
    }

    // Validar precio
    if (precio.value === "" || parseFloat(precio.value) <= 0) {
      document.getElementById("precio-error").textContent =
        "Ingrese un precio válido mayor a 0";
      document.getElementById("precio-error").style.display = "block";
      precio.classList.add("error");
      isValid = false;
    }

    // Validar imagen
    if (!imageFile) {
      document.getElementById("imagen-error").textContent =
        "Seleccione una imagen";
      document.getElementById("imagen-error").style.display = "block";
      imagen.classList.add("error");
      isValid = false;
    }

    if (isValid) {
      this.submit();
    }
  });
