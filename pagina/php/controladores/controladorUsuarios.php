
<?php

if (isset($_REQUEST["accion"])) {
    // convertimos a minúscula y eliminamos los espacios
    $accion = str_replace(" ", "", strtolower($_REQUEST["accion"]));
    // recogemos los campos
    if (isset($_REQUEST["usuario"])) $usuario = $_REQUEST["usuario"];
    if (isset($_REQUEST["clave"])) $clave = $_REQUEST["clave"];
    switch ($accion) {
        case "acceder":
            // preguntamos si existe el usuario con esa clave en el fichero de usuarios
            $ok = acceder($usuario, $clave);
            if ($ok == true) {
                // creamos la variable de sesión 'usuario y guardamos el nombre de usuario y creamos 
                $_SESSION["usuario"] = $usuario;
                // redirigimos hacia el muro del usuario
                $vista = ".".DIRECTORY_SEPARATOR."usuarios".DIRECTORY_SEPARATOR.$_SESSION["usuario"]."muro.php";
            }
            break;
            
        case "registrarme":
            $ok = registrar($usuario, $clave);
            if ($ok) {
                $mensaje = "Usuario registrado";
            } else {
                $mensaje = "Error: el usuario no se ha podido registrar";
            }
            break;

        case "cerrarsesión":
            $registro = @fopen("registro.log", "a");
            fputs($registro, "(".date('Y-m-d H:i:s').") El usuario '" .$_SESSION["usuario"]. "' ha cerrado sesión. \n");
            fclose($registro);
            // elimino las variables de sesión
            session_unset();
            // destruyo la sesión
            session_destroy();
            // y nos redirigimos a la página de inicio de sesión
            header("Location: index.php");
            break;
    }
}

?>
