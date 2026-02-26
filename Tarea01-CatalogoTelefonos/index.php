<?php

include 'conexion.php';


$query = "SELECT * FROM productos ORDER BY id DESC";
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo | Smart Phone Legacy</title>
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
           
            if ($resultado->num_rows > 0) {
                
               
                while($fila = $resultado->fetch_assoc()) {
                    
                   
                    $imagen = !empty($fila['imagen']) ? 'uploads/' . htmlspecialchars($fila['imagen']) : 'uploads/default.jpg';
                    
                    echo '<article class="producto-card">';
                    
                    
                    echo '    <img src="' . $imagen . '" alt="' . htmlspecialchars($fila['modelo']) . '">';
                    
                   
                    echo '    <div>';
                    echo '        <h3>' . htmlspecialchars($fila['marca']) . ' ' . htmlspecialchars($fila['modelo']) . '</h3>';
                    echo '        <p class="desc">' . htmlspecialchars($fila['descripcion']) . '</p>';
                    echo '        <div class="precio">$' . number_format($fila['precio'], 2) . '</div>';
                    
                    
                    echo '        <a href="#" class="btn btn-comprar">Comprar ahora</a>';
                    echo '    </div>';
                    
                    echo '</article>';
                }
            } else {
                
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

