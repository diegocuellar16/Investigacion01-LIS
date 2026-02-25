<?php
$servidor = "localhost";
$usuario = "root"; // Tu usuario de XAMPP/WAMP
$password = ""; // Normalmente en XAMPP va vacío
$basededatos = "tienda_telefonos"; // ¡Asegúrate de que esta línea exista!

// Crear la conexión
$conn = new mysqli($servidor, $usuario, $password, $basededatos);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
