<!-- @format -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../php/login-admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../src/css/usuario-admin.css" />
    <link rel="icon" href="../img/black-logo - copia.ico" />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <title>Usuarios</title>
  </head>
  <body>
    <?php
    include("conexion.php");

    // Verificar si la columna es_admin existe
    $check_column = "SHOW COLUMNS FROM usuario LIKE 'es_admin'";
    $column_result = mysqli_query($connection, $check_column);

    // Si la columna no existe, crearla
    if(mysqli_num_rows($column_result) == 0) {
        $add_column = "ALTER TABLE usuario ADD COLUMN es_admin TINYINT(1) DEFAULT 0";
        mysqli_query($connection, $add_column);
    }

    $sql = "select * from usuario";
    $resultado = mysqli_query($connection,$sql);
?>
    <header>
      <nav class="menu">
        <div class="logo">
          <a href="../index.php">
            <img src="../img/logo.png" class="logo" alt="Logo" />
          </a>
        </div>
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <div class="links">
          <a class="link" href="../index.php">Inicio</a>
          <a class="link" href="../index.php/#destino">Acerca</a>
          <a class="link" href="tienda-admin.php">Tienda</a>
          <a class="link" href="opiniones-admin.php">Opiniones</a>
          <a class="link" href="contacto-admin.php">Contacto</a>
          <a class="selected-link" href="usuario-admin.php">Usuarios</a>
          <a class="link" href="dashboard.php">Dashboard</a>
        </div>
       <div class="user">
          <div class="user-icon" id="user-icon">
              <i class='bx bxs-user-circle' ></i>
          </div>
          <div class="user-dropdown" id="user-dropdown">
              <div class="user-info">
                  <span class="username"><?php echo $_SESSION['username']; ?></span>
                  <span class="email"><?php echo $_SESSION['email']; ?></span>
              </div>
              <a href="php/logout.php" class="logout-btn">Cerrar sesión</a>
          </div>
      </div>
      </nav>
    </header>
    <main>
      <div class="filters">
        <div class="search-filter">
          <i class="bx bx-search search-icon"></i>
          <input
            type="text"
            id="search-input"
            placeholder="Buscar usuarios..."
          />
        </div>
          <button id="add-user-btn" class="admin-btn">
          <i class="bx bx-plus"></i> Añadir Usuario
           </button>
      </div>

      </div>

      <div class="users-table">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Email</th>
              <th>Contraseña</th>
              <th>Administrador</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
                while($fila = mysqli_fetch_assoc($resultado)){

            ?>
            <tr>
              <td><?php echo $fila['id'] ?></td>
              <td><?php echo $fila['nombre'] ?></td>
              <td><?php echo $fila['correo'] ?></td>
              <td><?php echo $fila['contraseña'] ?></td>
              <td><?php echo isset($fila['es_admin']) && $fila['es_admin'] ? 'Sí' : 'No' ?></td>
              <td class="actions">
                <?php echo "<a href='editar.php?id=".$fila['id']."'><button class='action-btn edit'>
                    <i class='bx bx-edit'></i>
                </button></a>"; ?>

                <?php echo "<a href='delete_user.php?id=".$fila['id']."'><button class='action-btn delete'>
                    <i class='bx bx-trash'></i>
                </button></a>"; ?>
              </td>
            </tr>
              <?php
                }
           ?>
          </tbody>
        </table>
         <?php
        mysqli_close($connection);
    ?>
      </div>
    </main>
    <div id="editModal" class="modal">
      <div class="modal-content">
          <span class="close">&times;</span>
          <h2>Editar Usuario</h2>
          <form id="editForm" method="POST">
              <input type="hidden" id="edit-id" name="id">
              <div class="form-group">
                  <label for="edit-nombre">Nombre:</label>
                  <input type="text" id="edit-nombre" name="nombre" required>
                  <div class="error-message" id="nombre-error">El nombre es requerido</div>
              </div>
              <div class="form-group">
                  <label for="edit-correo">Correo:</label>
                  <input type="email" id="edit-correo" name="correo" required>
                  <div class="error-message" id="correo-error">El correo es requerido</div>
              </div>
              <div class="form-group">
                  <label for="edit-password">Contraseña: (Dejar en blanco para mantener la actual)</label>
                  <input type="password" id="edit-password" name="password">
                  <div class="error-message" id="password-error">La contraseña debe tener al menos 6 caracteres</div>
              </div>
              <div class="form-group checkbox-group">
                  <label for="edit-es-admin">
                      <input type="checkbox" id="edit-es-admin" name="es_admin">
                      Es administrador
                  </label>
              </div>
              <button type="submit" class="btn-submit">Actualizar Usuario</button>
          </form>
      </div>
    </div>
    <div id="addModal" class="modal">
      <div class="modal-content">
          <span class="close" id="closeAddModal">&times;</span>
          <h2>Añadir Nuevo Usuario</h2>
          <form id="addForm">
              <div class="form-group">
                  <label for="add-nombre">Nombre:</label>
                  <input type="text" id="add-nombre" name="nombre">
                  <div class="error-message" id="add-nombre-error">El nombre es requerido</div>
              </div>
              <div class="form-group">
                  <label for="add-correo">Email:</label>
                  <input type="email" id="add-correo" name="correo">
                  <div class="error-message" id="add-correo-error">El correo es requerido</div>
              </div>
              <div class="form-group">
                  <label for="add-password">Contraseña:</label>
                  <input type="password" id="add-password" name="contraseña">
                  <div class="error-message" id="add-password-error">La contraseña es requerida</div>
              </div>
              <div class="form-group checkbox-group">
                  <label for="add-es-admin">
                      <input type="checkbox" id="add-es-admin" name="es_admin">
                      Es administrador
                  </label>
              </div>
              <button type="submit" class="btn-submit">Añadir Usuario</button>
          </form>
      </div>
    </div>
  </body>
  <script src="../src/js/usuario-admin.js"></script>
</html>
