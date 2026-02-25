<?php
// Incluimos la conexión a la base de datos
include 'conexion.php';

// Hacemos la consulta para traer todos los productos del inventario
$query = "SELECT * FROM productos ORDER BY id DESC";
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | Galaxy Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Smart Phone Legacy</h1>
        <p>Descubre la última tecnología en smartphones</p>
    </header>

    <div class="nav-menu">
        <a href="index.php">Catálogo</a>
        <a href="admin.php">Administración</a>
    </div>

    <main class="container">
        <div class="catalogo-grid">
            
            <?php
            // Comprobamos si hay productos registrados en la base de datos
            if ($resultado->num_rows > 0) {
                
                // Bucle para imprimir cada producto como una "tarjeta"
                while($fila = $resultado->fetch_assoc()) {
                    
                    // Validamos si tiene imagen, si no, mostramos una por defecto
                    $imagen = !empty($fila['imagen']) ? 'uploads/' . htmlspecialchars($fila['imagen']) : 'uploads/default.jpg';
                    
                    echo '<article class="producto-card">';
                    
                    // Imagen del producto
                    echo '    <img src="' . $imagen . '" alt="' . htmlspecialchars($fila['modelo']) . '">';
                    
                    // Información del producto
                    echo '    <div>';
                    echo '        <h3>' . htmlspecialchars($fila['marca']) . ' ' . htmlspecialchars($fila['modelo']) . '</h3>';
                    echo '        <p class="desc">' . htmlspecialchars($fila['descripcion']) . '</p>';
                    echo '        <div class="precio">$' . number_format($fila['precio'], 2) . '</div>';
                    
                    // Botón de compra (estilo píldora)
                    echo '        <a href="#" class="btn btn-comprar">Comprar ahora</a>';
                    echo '    </div>';
                    
                    echo '</article>';
                }
            } else {
                // Mensaje en caso de que el inventario esté vacío
                echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">';
                echo '    <h2>No hay productos disponibles</h2>';
                echo '    <p>Dirígete a la sección de administración para agregar nuevos teléfonos al catálogo.</p>';
                echo '</div>';
            }
            ?>

        </div>
    </main>

</body>
</html>