
<?php

// función 'existe' que comprobará si el usuario existe o no ya
function existe($usuario) {
    // damos por hecho que el usuario no va a existir
    $ok = NULL;
    
    // cargamos en un array asociativo el fichero de usuarios
    $usuarios = parse_ini_file("usuarios.ini");
    
    // preguntamos si existe la clave $usuario dentro del array
    if (isset($usuarios[$usuario])) {
        // si existe guardamos su contraseña para devolverla
        $ok = $usuarios[$usuario];
    }
    return $ok;
}

// función 'existe' que meterá usuario + clave en el archivo 'usuarios.ini'
function grabar($usuario, $clave) {
    // pensamos que algo puede fallar
    $ok = false;
    
    // abrimos el fichero en modo de añadir "a+" Ver los diferentes modos de apertura: 
    // https://www.php.net/manual/es/function.fopen.php
    // y obtenemos un descriptor de fichero a través del cual realizar las operaciones de lectura, escritura, cierre, etc
    $f = fopen("usuarios.ini", "a+");
    
    // si se ha podido abrir, entonces
    if ($f != NULL) {
        // grabamos la línea
        $ok = fwrite($f, "$usuario=$clave".PHP_EOL); // 'ok' tomará el valor false si no se ha podido grabar
        // cerramos el fichero 
        fclose($f);
    }
    return $ok;
}

/* 
 * función 'registrar' que comprueba que el usuario no existe para después añadirlo 
 * al fichero de usuarios y crear su directorio de trabajo.
 * devuelve true si se ha podido registrar y false en caso contrario.
*/
function registrar($usuario, $clave) {
    // pensamos que no se va a poder registrar
    $ok = false;
    
    // preguntamos si existe, devuelve NULL si el usuario no existe
    if (existe($usuario) == NULL) {
        $ok = grabar($usuario, $clave);

        // si se ha podido grabar 
        if ($ok != false) {
            // creamos la carpeta con el nombre de usuario (se crea en el mismo directorio del script)
            $ok = mkdir("usuarios".DIRECTORY_SEPARATOR.$usuario); // 'ok' debería acabar siendo TRUE
            // creo el fichero "muro.php"
            @file_put_contents("usuarios".DIRECTORY_SEPARATOR.$usuario.DIRECTORY_SEPARATOR."muro.php", " ");
            $file = @fopen("usuarios".DIRECTORY_SEPARATOR.$usuario.DIRECTORY_SEPARATOR."muro.php", "w");


            // CÓDIGO PARA CREAR LOS MUROS
            fputs($file, <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>My Wall</title>
                <style>
                    body {
                        display: flex;
                    }
                    .sidebar {
                        width: 200px;
                        background-color: #f4f4f4;
                        padding: 15px;
                        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                    }
                    .content {
                        flex-grow: 1; /* para que ocupe todo el espacio restante */
                        padding: 15px;
                    }
                    ul {
                        list-style-type: none;
                        padding: 0;
                    }
                    li {
                        margin: 10px 0;
                    }
                </style>
            </head>
            <body>
                <div class="sidebar">
                    <h3>Todos los muros:</h3>
                    <ul>
                    <?php
                        // Se muestra la lista de muros de otros usuarios
                        \$directorios = './usuarios/';
                        \$gente = array_diff(scandir(\$directorios), array('..', '.'));
                        foreach (\$gente as \$usr) {
                            if (\$usr == \$_SESSION['usuario']) {
                                echo "<li><a href='index.php?usuario_seleccionado=\$usr'>MI MURO</a></li>";
                            } else {
                                echo "<li><a href='index.php?usuario_seleccionado=\$usr'>\$usr</a></li>";
                            }
                        }
                    ?>
                    </ul>
                </div>

                <div class="content">
                <?php
                    // Determina el usuario cuyo muro se debe mostrar
                    \$usuarioMuro = isset(\$_GET['usuario_seleccionado']) ? \$_GET['usuario_seleccionado'] : (isset(\$_SESSION['usuario']) ? \$_SESSION['usuario'] : null);

                    // Muestra el formulario de publicación solo si el usuario actual es el mismo que ha iniciado sesión
                    if (isset(\$_SESSION['usuario']) && \$_SESSION['usuario'] == \$usuarioMuro) {
                        ?>
                        <form enctype='multipart/form-data' method="POST">
                            <input type="submit" value="Cerrar sesión" name="accion"/><br><br>
                            <input type="text" name="titulo" placeholder="Encabezado"/><br><br>
                            <textarea name="publi" rows="5" cols="50" placeholder="¿Qué quieres postear?"></textarea><br><br>
                            <input type='file' name='imagen' accept='image/*'/>
                            <input type="submit" value="Publicar" name="boton"/><br><br>
                        </form>
                        <?php
                    }
                    
                    // Mostrar todas las publicaciones del usuario actual o seleccionado
                    if (\$usuarioMuro) {
                        \$ficheros = scandir('usuarios/' . \$usuarioMuro);
                        foreach (\$ficheros as \$archivo) {
                            if (pathinfo(\$archivo, PATHINFO_EXTENSION) !== 'php') {
                                \$rutaArchivo = 'usuarios/' . \$usuarioMuro . '/' . \$archivo;
                                \$contenido = @file_get_contents(\$rutaArchivo);
                                \$contenido = @mb_convert_encoding(\$contenido, 'UTF-8', 'auto');
                                echo \$contenido . '<br>';
                            }
                        }
                    } else {
                        echo "Usuario no encontrado.";
                    }
                ?>
                </div>
            </body>
            </html>
            HTML
            );
        }
        $registro = @fopen("registro.log", "a");
        fputs($registro, "(".date('Y-m-d H:i:s').") El usuario '" .$usuario. "' se ha registrado. \n");
        fclose($registro);
    }
    return $ok;
}

// función 'acceder' que devuelve TRUE si el usuario existe y coincide su contraseña o FALSE en caso contrario.
function acceder($usuario, $clave) {
    // damos por hecho que el usuario no existe
    $ok = false;
    
    // llamamos a la función existe para recoger la contraseña del usuario si existe o NULL en caso contrario
    $clave_usuario = existe($usuario);
    
    // si es distinto de NULL comparamos con la clave introducida en el formulario
    if ($clave_usuario != NULL && $clave_usuario == $clave) {
        // si coinciden, devolveremos TRUE para indicar el acceso autorizado 
        $ok = true;
    }
    $registro = @fopen("registro.log", "a");
    fputs($registro, "(".date('Y-m-d H:i:s').") El usuario '" .$usuario. "' ha iniciado sesión. \n");
    fclose($registro);
    return $ok;
}

?>
