<?php
$servidor = "localhost";
$usuario = "root"; 
$password = ""; 
$basededatos = "tienda_telefonos"; 


$conn = new mysqli($servidor, $usuario, $password, $basededatos);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

