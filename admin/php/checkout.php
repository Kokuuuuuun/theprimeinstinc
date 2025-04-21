<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['products'])) {
    header("Location: tienda-admin.php");
    exit();
}

$products = $_POST['products'];
$total = $_POST['total'];

// Get email directly from session instead of database query
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Prime Instinct</title>
    <link rel="stylesheet" href="tienda-admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
        }
        .checkout-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .checkout-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 1rem;
        }
        .checkout-item-details {
            flex: 1;
        }
        .checkout-total {
            margin-top: 1rem;
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            margin-top: 1rem;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .payment-methods {
            margin: 1rem 0;
        }
        .card-fields {
            display: none;
        }
        .card-fields.active {
            display: block;
        }
        .form-group input.error {
            border-color: #ff0000;
        }
        .error-message {
            color: #ff0000;
            font-size: 0.8rem;
            margin-top: 0.3rem;
            display: none;
        }
        .error-message.visible {
            display: block;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2>Finalizar Compra</h2>
        
        <div class="order-summary">
            <h3>Resumen del Pedido</h3>
            <div class="checkout-items">
                <?php foreach ($products as $product): ?>
                    <div class="checkout-item">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="checkout-item-details">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p>Cantidad: <?php echo htmlspecialchars($product['quantity']); ?></p>
                            <p>Precio: $<?php echo number_format($product['price'], 2); ?></p>
                            <p>Subtotal: $<?php echo number_format($product['price'] * $product['quantity'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="checkout-total">
                <h4>Total a pagar: $<?php echo number_format($total, 2); ?></h4>
            </div>
        </div>

        <form action="process_order.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" >
                <div id="nombre-error" class="error-message">Por favor, ingrese su nombre completo.</div>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div id="email-error" class="error-message">Por favor, ingrese un email válido.</div>
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección de Envío:</label>
                <input type="text" id="direccion" name="direccion" >
                <div id="direccion-error" class="error-message">Por favor, ingrese su dirección de envío.</div>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" placeholder="123-456-7890" maxlength="12" name="telefono">
                <div id="telefono-error" class="error-message">Por favor, ingrese un número de teléfono válido.</div>
            </div>

            <div class="payment-methods">
                <h3>Método de Pago:</h3>
                <div>
                    <input type="radio" id="tarjeta" name="metodo_pago" value="tarjeta" checked>
                    <label for="tarjeta">Tarjeta de Crédito/Débito</label>
                </div>
                <div>
                    <input type="radio" id="efectivo" name="metodo_pago" value="efectivo">
                    <label for="efectivo">Pago en Efectivo</label>
                </div>
            </div>

            <div id="card-fields" class="card-fields active">
                <div class="form-group">
                    <label for="numero_tarjeta">Número de Tarjeta:</label>
                    <input type="text" id="numero_tarjeta" name="numero_tarjeta" 
                           placeholder="1234-5678-9012-3456" maxlength="19">
                    <div id="card-error" class="error-message">Por favor, ingrese un número de tarjeta válido.</div>
                </div>
            </div>

            <?php foreach ($products as $key => $product): ?>
                <input type="hidden" name="productos[<?php echo $key; ?>][nombre]" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="productos[<?php echo $key; ?>][cantidad]" value="<?php echo htmlspecialchars($product['quantity']); ?>">
            <?php endforeach; ?>
            
            <input type="hidden" name="total" value="<?php echo $total; ?>">
            <button type="submit" class="btn-submit">Confirmar Compra</button>
        </form>
    </div>

    <script>
        document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const cardFields = document.getElementById('card-fields');
                if (this.value === 'tarjeta') {
                    cardFields.classList.add('active');
                    document.getElementById('numero_tarjeta').required = true;
                } else {
                    cardFields.classList.remove('active');
                    document.getElementById('numero_tarjeta').required = false;
                }
            });
        });

        document.getElementById('telefono').addEventListener('input', function(e) {
            // Get just the numbers
            let input = e.target.value.replace(/\D/g, '');
            
            // Limit to 10 digits
            input = input.substring(0, 10);
            
            // Format the number
            const size = input.length;
            if (size === 0) {
                e.target.value = '';
            } else if (size < 4) {
                e.target.value = input;
            } else if (size < 7) {
                e.target.value = `${input.slice(0, 3)}-${input.slice(3)}`;
            } else {
                e.target.value = `${input.slice(0, 3)}-${input.slice(3, 6)}-${input.slice(6)}`;
            }
        });

        document.getElementById('numero_tarjeta').addEventListener('input', function(e) {
            // Remove any non-digit character
            let input = e.target.value.replace(/\D/g, '');
            
            // Limit to 16 digits
            input = input.substring(0, 16);
            
            // Format the number
            let formattedValue = '';
            for (let i = 0; i < input.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += '-';
                }
                formattedValue += input[i];
            }
            
            e.target.value = formattedValue;
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            document.querySelectorAll('.error-message').forEach(el => el.classList.remove('visible'));
            
            // Validate Name
            const nombre = document.getElementById('nombre');
            if (!nombre.value.trim()) {
                nombre.classList.add('error');
                document.getElementById('nombre-error').classList.add('visible');
                isValid = false;
            }
            
            // Validate Email
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !emailRegex.test(email.value)) {
                email.classList.add('error');
                document.getElementById('email-error').classList.add('visible');
                isValid = false;
            }
            
            // Validate Address
            const direccion = document.getElementById('direccion');
            if (!direccion.value.trim()) {
                direccion.classList.add('error');
                document.getElementById('direccion-error').classList.add('visible');
                isValid = false;
            }
            
            // Validate Phone
            const telefono = document.getElementById('telefono');
            const phoneRegex = /^\d{3}-\d{3}-\d{4}$/;
            if (!telefono.value.trim() || !phoneRegex.test(telefono.value)) {
                telefono.classList.add('error');
                document.getElementById('telefono-error').classList.add('visible');
                isValid = false;
            }
            
            // Validate Card Number if payment method is card
            if (document.getElementById('tarjeta').checked) {
                const numeroTarjeta = document.getElementById('numero_tarjeta');
                const cardRegex = /^\d{4}-\d{4}-\d{4}-\d{4}$/;
                if (!numeroTarjeta.value.trim() || !cardRegex.test(numeroTarjeta.value)) {
                    numeroTarjeta.classList.add('error');
                    document.getElementById('card-error').classList.add('visible');
                    isValid = false;
                }
            }
            
            if (isValid) {
                this.submit();
            }
        });
    </script>
</body>
</html>