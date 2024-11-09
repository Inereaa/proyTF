
<?php

    if (isset($_REQUEST["boton"])) {
        // convertimos a minúscula y eliminamos los espacios
        $boton = str_replace(" ", "", strtolower($_REQUEST["boton"]));

        switch ($boton) {
            case "publicar":
                $datos = fopen("usuarios".DIRECTORY_SEPARATOR.$_SESSION["usuario"].DIRECTORY_SEPARATOR.date("U").".html", "w");
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                    $directorioDestino = 'usuarios/' . $_SESSION['usuario'] . '/';
                    $nombreImagen = basename($_FILES['imagen']['name']);
                    $rutaDestino = $directorioDestino . $nombreImagen;
            
                    // Mueve el archivo subido al directorio de destino
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                        // Agrega la imagen a la publicación
                        fputs($datos, "<img src='".$rutaDestino."' style='max-width:100%;'/><br>".PHP_EOL);
                    }
                }
                fputs($datos, "<h3>".$_REQUEST['titulo']."</h3>".PHP_EOL);
                fputs($datos, "<p>".$_REQUEST['publi']."</p>".PHP_EOL);
                fputs($datos, "<form>".PHP_EOL);
                fputs($datos, "<input type='hidden' name='archivo' value='".date("U").".html"."'/>".PHP_EOL);
                fputs($datos, "<input type='hidden' name='tutor' value='".$_SESSION["usuario"]."'/>".PHP_EOL);
                fputs($datos, "<input type='text' name='respuesta' placeholder='Tu respuesta...'/>".PHP_EOL);
                fputs($datos, "<input type='submit' value='Responder' name='boton'/>".PHP_EOL);
                fputs($datos, "</form>");
                fclose($datos);
                $registro = @fopen("registro.log", "a");
                fputs($registro, "(".date('Y-m-d H:i:s').") El usuario '" .$_SESSION["usuario"]. "' ha hecho una publicación. \n");
                fclose($registro);
                header("Location: index.php");
                exit;
            break;

            case "responder":
                $nombreArchivo = $_REQUEST['archivo'];
                $tutor = $_REQUEST['tutor'];
                $rutaArchivo = "usuarios/$tutor/$nombreArchivo";

                $datos = fopen($rutaArchivo, "a");
                fputs($datos, "<h5>@".$_SESSION['usuario'].", respondió: </h5>".PHP_EOL);
                fputs($datos, "<p>".$_REQUEST['respuesta']."</p>".PHP_EOL);
                fclose($datos);
                $registro = @fopen("registro.log", "a");
                fputs($registro, "(".date('Y-m-d H:i:s').") El usuario '" .$_SESSION['usuario']. "' ha hecho un comentario. \n");
                fclose($registro);
                header("Location: index.php");
                exit;
            break;
        }
    }

?>
