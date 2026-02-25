<?php
include 'conexion.php';
$producto = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM productos WHERE id = $id";
    $resultado = $conn->query($query);
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
    } else {
        die("Producto no encontrado.");
    }
}

if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $year_s = $_POST['year_s'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen']['name'];
        $ruta = 'uploads/' . $imagen;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        $sql = "UPDATE productos SET marca='$marca', modelo='$modelo', year_s='$year_s', precio='$precio', descripcion='$descripcion', imagen='$imagen' WHERE id=$id";
    } else {
        $sql = "UPDATE productos SET marca='$marca', modelo='$modelo', year_s='$year_s', precio='$precio', descripcion='$descripcion' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: admin.php"); 
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto | Galaxy Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Editar Producto</h1>
        <p>Modifica los detalles del teléfono seleccionado</p>
    </header>

    <div class="nav-menu">
        <a href="index.php">Catálogo</a>
        <a href="admin.php">Volver a Administración</a>
    </div>

    <main class="container">
        
        <section class="form-container">
            <?php if($producto): ?>
            <form action="editar.php?id=<?php echo $producto['id']; ?>" method="POST" enctype="multipart/form-data" id="form-valida" novalidate>
                
                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

                <div class="form-group">
                    <label>Marca:</label>
                    <input type="text" name="marca" value="<?php echo htmlspecialchars($producto['marca']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Modelo:</label>
                    <input type="text" name="modelo" value="<?php echo htmlspecialchars($producto['modelo']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Año de lanzamiento:</label>
                    <input type="number" name="year_s" value="<?php echo htmlspecialchars($producto['year_s']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Precio ($):</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="4" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Imagen Actual (Opcional):</label><br>
                    <?php if(!empty($producto['imagen'])): ?>
                        <img src="uploads/<?php echo $producto['imagen']; ?>" width="100" style="border-radius: 12px; margin-bottom: 10px; border: 1px solid #eee;">
                    <?php endif; ?>
                    <input type="file" name="imagen" accept="image/*">
                    <small style="color: #666; display: block; margin-top: 5px;">Sube un archivo solo si deseas reemplazar la imagen actual.</small>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="actualizar" class="btn" style="flex: 1;">Actualizar Datos</button>
                    <a href="admin.php" class="btn btn-outline" style="flex: 1; text-align: center; box-sizing: border-box;">Cancelar</a>
                </div>
            </form>
            <?php else: ?>
                <p style="text-align: center; color: red;">No se cargaron los datos del producto.</p>
            <?php endif; ?>
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