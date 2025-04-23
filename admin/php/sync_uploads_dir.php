<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../php/login-admin.php");
    exit();
}

include("conexion.php");

// Definir directorios
$source_dir = "../uploads/";
$target_dir = "../../uploads/";

// Resultado de operaciones
$results = [
    'directories' => [
        'source' => [
            'path' => $source_dir,
            'exists' => file_exists($source_dir),
            'created' => false,
            'error' => null
        ],
        'target' => [
            'path' => $target_dir,
            'exists' => file_exists($target_dir),
            'created' => false,
            'error' => null
        ]
    ],
    'files' => [
        'total' => 0,
        'copied' => 0,
        'errors' => 0,
        'skipped' => 0,
        'details' => []
    ],
    'database' => [
        'total' => 0,
        'updated' => 0,
        'errors' => 0,
        'details' => []
    ]
];

// Crear el directorio fuente si no existe
if (!$results['directories']['source']['exists']) {
    if (mkdir($source_dir, 0755, true)) {
        $results['directories']['source']['created'] = true;
        $results['directories']['source']['exists'] = true;
    } else {
        $results['directories']['source']['error'] = "No se pudo crear el directorio";
    }
}

// Crear el directorio destino si no existe
if (!$results['directories']['target']['exists']) {
    if (mkdir($target_dir, 0755, true)) {
        $results['directories']['target']['created'] = true;
        $results['directories']['target']['exists'] = true;
    } else {
        $results['directories']['target']['error'] = "No se pudo crear el directorio";
    }
}

// Sincronizar archivos entre directorios
if ($results['directories']['source']['exists'] && $results['directories']['target']['exists']) {
    // Obtener lista de archivos del directorio fuente
    $files = scandir($source_dir);

    // Filtrar directorios especiales
    $files = array_filter($files, function($file) {
        return $file != "." && $file != "..";
    });

    $results['files']['total'] = count($files);

    // Copiar archivos al directorio destino
    foreach ($files as $file) {
        $source_file = $source_dir . $file;
        $target_file = $target_dir . $file;
        $file_result = [
            'name' => $file,
            'source' => $source_file,
            'target' => $target_file,
            'status' => 'pending'
        ];

        // Verificar si el archivo ya existe en el destino
        if (file_exists($target_file)) {
            $file_result['status'] = 'skipped';
            $results['files']['skipped']++;
        } else {
            // Copiar el archivo
            if (copy($source_file, $target_file)) {
                $file_result['status'] = 'copied';
                $results['files']['copied']++;
            } else {
                $file_result['status'] = 'error';
                $file_result['error'] = "Error al copiar el archivo";
                $results['files']['errors']++;
            }
        }

        $results['files']['details'][] = $file_result;
    }
}

// Actualizar las rutas de las imágenes en la base de datos
$sql = "SELECT id, img FROM productos";
$result = mysqli_query($connection, $sql);

if ($result) {
    $results['database']['total'] = mysqli_num_rows($result);

    while ($row = mysqli_fetch_assoc($result)) {
        $img_path = $row['img'];
        $id = $row['id'];
        $db_result = [
            'id' => $id,
            'old_path' => $img_path,
            'status' => 'pending'
        ];

        // Obtener solo el nombre del archivo de la ruta
        $file_name = basename($img_path);

        // Crear nueva ruta que apunte al directorio de uploads
        $new_path = "uploads/" . $file_name;
        $db_result['new_path'] = $new_path;

        // Actualizar la base de datos
        $update_sql = "UPDATE productos SET img = ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $update_sql);
        mysqli_stmt_bind_param($stmt, "si", $new_path, $id);

        if (mysqli_stmt_execute($stmt)) {
            $db_result['status'] = 'updated';
            $results['database']['updated']++;
        } else {
            $db_result['status'] = 'error';
            $db_result['error'] = mysqli_error($connection);
            $results['database']['errors']++;
        }

        mysqli_stmt_close($stmt);
        $results['database']['details'][] = $db_result;
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($connection);

// Función para mostrar los resultados de manera más legible
function formatStatus($status) {
    $colors = [
        'copied' => 'green',
        'skipped' => 'blue',
        'error' => 'red',
        'pending' => 'orange',
        'updated' => 'green'
    ];

    $texts = [
        'copied' => 'Copiado',
        'skipped' => 'Omitido (ya existe)',
        'error' => 'Error',
        'pending' => 'Pendiente',
        'updated' => 'Actualizado'
    ];

    return '<span style="color: ' . $colors[$status] . ';">' . $texts[$status] . '</span>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sincronización de Directorios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .warning {
            color: orange;
        }
        .info {
            color: blue;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .back-btn:hover {
            background-color: #0069d9;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
        }
        .summary-value {
            font-size: 1.8em;
            font-weight: bold;
            display: block;
        }
    </style>
</head>
<body>
    <h1>Sincronización de Directorios de Uploads</h1>

    <!-- Sección de Directorios -->
    <div class="section">
        <h2>Estado de los Directorios</h2>

        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Ruta</th>
                    <th>Existe</th>
                    <th>Creado</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (['source', 'target'] as $dir_type): ?>
                    <tr>
                        <td><?php echo $dir_type === 'source' ? 'Fuente (Admin)' : 'Destino (Principal)'; ?></td>
                        <td><?php echo htmlspecialchars($results['directories'][$dir_type]['path']); ?></td>
                        <td><?php echo $results['directories'][$dir_type]['exists'] ? 'Sí' : 'No'; ?></td>
                        <td><?php echo $results['directories'][$dir_type]['created'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <?php if ($results['directories'][$dir_type]['error']): ?>
                                <span class="error"><?php echo htmlspecialchars($results['directories'][$dir_type]['error']); ?></span>
                            <?php elseif ($results['directories'][$dir_type]['exists']): ?>
                                <span class="success">OK</span>
                            <?php else: ?>
                                <span class="error">No disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Resumen de archivos -->
    <div class="section">
        <h2>Sincronización de Archivos</h2>

        <div class="summary">
            <div class="summary-item">
                <span class="summary-value"><?php echo $results['files']['total']; ?></span>
                <span>Total de archivos</span>
            </div>
            <div class="summary-item">
                <span class="summary-value success"><?php echo $results['files']['copied']; ?></span>
                <span>Copiados</span>
            </div>
            <div class="summary-item">
                <span class="summary-value info"><?php echo $results['files']['skipped']; ?></span>
                <span>Omitidos</span>
            </div>
            <div class="summary-item">
                <span class="summary-value error"><?php echo $results['files']['errors']; ?></span>
                <span>Errores</span>
            </div>
        </div>

        <?php if ($results['files']['total'] > 0): ?>
            <h3>Detalles de Archivos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['files']['details'] as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['name']); ?></td>
                            <td><?php echo htmlspecialchars($file['source']); ?></td>
                            <td><?php echo htmlspecialchars($file['target']); ?></td>
                            <td><?php echo formatStatus($file['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay archivos para sincronizar.</p>
        <?php endif; ?>
    </div>

    <!-- Base de datos -->
    <div class="section">
        <h2>Actualización de Base de Datos</h2>

        <div class="summary">
            <div class="summary-item">
                <span class="summary-value"><?php echo $results['database']['total']; ?></span>
                <span>Total de registros</span>
            </div>
            <div class="summary-item">
                <span class="summary-value success"><?php echo $results['database']['updated']; ?></span>
                <span>Actualizados</span>
            </div>
            <div class="summary-item">
                <span class="summary-value error"><?php echo $results['database']['errors']; ?></span>
                <span>Errores</span>
            </div>
        </div>

        <?php if ($results['database']['total'] > 0): ?>
            <h3>Detalles de Actualizaciones</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Producto</th>
                        <th>Ruta Anterior</th>
                        <th>Nueva Ruta</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['database']['details'] as $db_item): ?>
                        <tr>
                            <td><?php echo $db_item['id']; ?></td>
                            <td><?php echo htmlspecialchars($db_item['old_path']); ?></td>
                            <td><?php echo htmlspecialchars($db_item['new_path']); ?></td>
                            <td>
                                <?php echo formatStatus($db_item['status']); ?>
                                <?php if (isset($db_item['error'])): ?>
                                    <div class="error"><?php echo htmlspecialchars($db_item['error']); ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay registros para actualizar.</p>
        <?php endif; ?>
    </div>

    <a href="tienda-admin.php" class="back-btn">Volver a la Tienda</a>
</body>
</html>
