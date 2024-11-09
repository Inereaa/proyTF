
<?php
// recuperamos o creamos la sesión
session_start();

// establecemos una variable 'ruta' con la dirección donde están las vistas
$ruta = "vistas".DIRECTORY_SEPARATOR;

// incluimos las funciones
require_once("modelo".DIRECTORY_SEPARATOR."funciones.php");
// incluimos los controladores
require("controladores".DIRECTORY_SEPARATOR."controladorUsuarios.php");
require("controladores".DIRECTORY_SEPARATOR."controladorMuro.php");

// dependiendo de si el usuario tiene la sesión iniciada o no, se establece la vista correspondiente
if (isset($_GET['usuario_seleccionado'])) {
    // si hay un usuario seleccionado en la URL, se usa ese usuario
    $vista = "usuarios".DIRECTORY_SEPARATOR.$_GET['usuario_seleccionado'].DIRECTORY_SEPARATOR."muro.php";
} elseif (isset($_SESSION["usuario"])) {
    // si no hay usuario seleccionado, se muestra el muro del usuario que ha iniciado sesión
    $vista = "usuarios".DIRECTORY_SEPARATOR.$_SESSION["usuario"].DIRECTORY_SEPARATOR."muro.php";
} else {
    $vista = $ruta."identificacion.php";
}

?>

<!-- se muestra la página con la vista correspondiente -->
<html>
    <head>
        <title> My wall </title>
    </head>
    <body>
        <?php require $vista ?>
    </body>
</html>