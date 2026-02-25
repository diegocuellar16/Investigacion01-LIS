<?php
include 'conexion.php';

// --- LÓGICA PARA AGREGAR PRODUCTO ---
if (isset($_POST['agregar'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $year_s = $_POST['year_s'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['name'];
        $ruta = 'uploads/' . $imagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
    }

    $sql = "INSERT INTO productos (marca, modelo, year_s, precio, descripcion, imagen) 
            VALUES ('$marca', '$modelo', '$year_s', '$precio', '$descripcion', '$imagen')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: admin.php"); 
        exit();
    }
}

// --- LÓGICA PARA ELIMINAR PRODUCTO ---
if (isset($_GET['eliminar'])) {
    $id_eliminar = $_GET['eliminar'];
    $sql_eliminar = "DELETE FROM productos WHERE id = $id_eliminar";
    if ($conn->query($sql_eliminar) === TRUE) {
        header("Location: admin.php");
        exit();
    }
}

$query = "SELECT * FROM productos ORDER BY id DESC";
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración | Galaxy Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Panel de Administración</h1>
        <p>Gestiona el inventario de la tienda</p>
    </header>

    <div class="nav-menu">
        <a href="index.php">Catálogo</a>
        <a href="admin.php">Administración</a>
    </div>

    <main class="container">
        
        <section class="form-container">
            <h2>Agregar Nuevo Teléfono</h2>
            <form action="admin.php" method="POST" enctype="multipart/form-data" id="form-valida" novalidate>
                <div class="form-group">
                    <label>Marca:</label>
                    <input type="text" name="marca" placeholder="Ej: Samsung" required>
                </div>
                <div class="form-group">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" placeholder="Ej: Galaxy S24 Ultra" required>
                </div>
                <div class="form-group">
                    <label>Año de lanzamiento:</label>
                    <input type="number" name="year_s" placeholder="Ej: 2024" required>
                </div>
                <div class="form-group">
                    <label>Precio ($):</label>
                    <input type="number" step="0.01" name="precio" placeholder="Ej: 1199.99" required>
                </div>
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="3" placeholder="Características principales..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Imagen del Producto (Opcional):</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>
                <button type="submit" name="agregar" class="btn">Guardar Producto</button>
            </form>
        </section>

        <section>
            <h2 style="margin-bottom: 20px; text-align: left; color:#1a73e8;">Inventario Actual</h2>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Año</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($resultado->num_rows > 0) {
                            while($fila = $resultado->fetch_assoc()): 
                        ?>
                        <tr>
                            <td>
                                <?php $img = !empty($fila['imagen']) ? 'uploads/'.$fila['imagen'] : 'uploads/default.jpg'; ?>
                                <img src="<?php echo $img; ?>" width="50" height="50" alt="tel">
                            </td>
                            <td><?php echo htmlspecialchars($fila['marca']); ?></td>
                            <td><?php echo htmlspecialchars($fila['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['year_s']); ?></td>
                            <td>$<?php echo number_format($fila['precio'], 2); ?></td>
                            <td>
                                <a href="editar.php?id=<?php echo $fila['id']; ?>" class="btn btn-sm btn-outline">Editar</a>
                                <a href="admin.php?eliminar=<?php echo $fila['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>No hay productos registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <div id="mensaje-toast" class="toast"></div>

   <script>
        /* =========================================================
           1. BLOQUEO EN TIEMPO REAL (LETRAS Y NÚMEROS NEGATIVOS)
           ========================================================= */
        let inputAnio = document.querySelector('input[name="year_s"]');
        let inputPrecio = document.querySelector('input[name="precio"]');

        function bloquearInvalidos(evento) {
            // 1. Bloqueamos las flechas de Arriba y Abajo del teclado
            if (evento.key === 'ArrowUp' || evento.key === 'ArrowDown') {
                evento.preventDefault(); // Evita que el número suba o baje
            }
            // 2. Bloqueamos letras (permitiendo Backspace, Tab, etc.)
            else if (evento.key.length === 1 && evento.key.match(/[a-zA-ZñÑ]/)) {
                evento.preventDefault(); 
                mostrarMensajeFlotante('No puede escribir letras en el año y en el precio.', '#dc3545');
            }
            // 3. Bloqueamos el signo de menos (-)
            else if (evento.key === '-') {
                evento.preventDefault(); 
                mostrarMensajeFlotante('No se permiten números negativos.', '#dc3545');
            }
        }

        // Le asignamos esta función a los campos numéricos
        if (inputAnio) inputAnio.addEventListener('keydown', bloquearInvalidos);
        if (inputPrecio) inputPrecio.addEventListener('keydown', bloquearInvalidos);


        /* =========================================================
           2. VALIDACIÓN AL PRESIONAR "GUARDAR / ACTUALIZAR"
           ========================================================= */
        document.getElementById('form-valida').addEventListener('submit', function(evento) {
            let valido = true;
            let mensajeAlerta = 'Por favor, completa todos los campos requeridos para continuar.';
            
            // A. Validar que no haya campos requeridos vacíos
            let camposRequeridos = this.querySelectorAll('[required]');
            camposRequeridos.forEach(function(campo) {
                if (campo.value.trim() === '') {
                    valido = false;
                    campo.style.borderColor = '#dc3545'; // Borde rojo
                } else {
                    campo.style.borderColor = '#dce1e6'; // Borde normal
                }
            });

            // B. Validar usando Expresiones Regulares (Regex) y valores positivos
            if (valido) {
                let marca = document.querySelector('input[name="marca"]');
                let anio = document.querySelector('input[name="year_s"]');
                let precio = document.querySelector('input[name="precio"]');

                let regexLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
                let regexAnio = /^\d{4}$/;
                let regexPrecio = /^\d+(\.\d{1,2})?$/;

                if (!regexLetras.test(marca.value.trim())) {
                    valido = false;
                    marca.style.borderColor = '#dc3545';
                    mensajeAlerta = 'Error: La marca solo debe contener letras.';
                } 
                else if (anio.value.trim() !== '' && (!regexAnio.test(anio.value.trim()) || parseFloat(anio.value) < 0)) {
                    valido = false;
                    anio.style.borderColor = '#dc3545';
                    mensajeAlerta = 'Error: El año debe ser un número positivo de 4 dígitos.';
                }
                else if (!regexPrecio.test(precio.value.trim()) || parseFloat(precio.value) < 0) {
                    valido = false;
                    precio.style.borderColor = '#dc3545';
                    mensajeAlerta = 'Error: Ingrese un precio válido mayor a 0.';
                }
            }

            // Si se detectó un error, se cancela el envío y se muestra el Toast
            if (!valido) {
                evento.preventDefault(); 
                mostrarMensajeFlotante(mensajeAlerta, '#dc3545');
            }
        });

        /* =========================================================
           3. FUNCIÓN PARA MOSTRAR EL MENSAJE FLOTANTE (TOAST)
           ========================================================= */
        function mostrarMensajeFlotante(mensaje, color) {
            let toast = document.getElementById("mensaje-toast");
            toast.innerText = mensaje;
            toast.style.backgroundColor = color;
            toast.classList.add("show");
            
            setTimeout(function() { 
                toast.classList.remove("show"); 
            }, 3000);
        }
    </script>
</body>
</html>