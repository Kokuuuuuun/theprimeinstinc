<?php
// Establecer código de estado HTTP 503 (Service Unavailable)
http_response_code(503);
header('Retry-After: 300'); // Sugerir reintento después de 5 minutos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15;url=/">
    <title>Error de conexión - <?php echo $_ENV['APP_NAME'] ?? 'Prime Instinct'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }
        .error-card {
            max-width: 600px;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card card">
            <div class="card-body text-center">
                <h1 class="display-1 text-danger mb-4">⚠️</h1>
                <h2 class="card-title mb-4">Problemas técnicos temporales</h2>
                <p class="card-text lead mb-4">
                    Estamos experimentando dificultades técnicas. Nuestro equipo ha sido notificado y está trabajando para resolver el problema.
                </p>
                
                <div class="alert alert-warning mb-4">
                    <h3 class="h5">¿Qué puedes hacer?</h3>
                    <ul class="text-start list-unstyled">
                        <li>• Intentar recargar la página en unos minutos</li>
                        <li>• Verificar tu conexión a Internet</li>
                        <li>• Contactar a soporte técnico</li>
                    </ul>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <a href="/" class="btn btn-primary">
                        Volver al inicio
                    </a>
                    <a href="mailto:<?php echo $_ENV['SUPPORT_EMAIL'] ?? 'support@primeinstinct.com'; ?>" class="btn btn-outline-secondary">
                        Contactar soporte
                    </a>
                </div>

                <p class="text-muted mt-4 small">
                    Redireccionando automáticamente en 15 segundos...
                </p>
            </div>
        </div>
    </div>
</body>
</html>
