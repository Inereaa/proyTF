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
            $directorios = './usuarios/';
            $gente = array_diff(scandir($directorios), array('..', '.'));
            foreach ($gente as $usr) {
                if ($usr == $_SESSION['usuario']) {
                    echo "<li><a href='index.php?usuario_seleccionado=$usr'>MI MURO</a></li>";
                } else {
                    echo "<li><a href='index.php?usuario_seleccionado=$usr'>$usr</a></li>";
                }
            }
        ?>
        </ul>
    </div>

    <div class="content">
    <?php
        // Determina el usuario cuyo muro se debe mostrar
        $usuarioMuro = isset($_GET['usuario_seleccionado']) ? $_GET['usuario_seleccionado'] : (isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null);

        // Muestra el formulario de publicación solo si el usuario actual es el mismo que ha iniciado sesión
        if (isset($_SESSION['usuario']) && $_SESSION['usuario'] == $usuarioMuro) {
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
        if ($usuarioMuro) {
            $ficheros = scandir('usuarios/' . $usuarioMuro);
            foreach ($ficheros as $archivo) {
                if (pathinfo($archivo, PATHINFO_EXTENSION) !== 'php') {
                    $rutaArchivo = 'usuarios/' . $usuarioMuro . '/' . $archivo;
                    $contenido = @file_get_contents($rutaArchivo);
                    $contenido = @mb_convert_encoding($contenido, 'UTF-8', 'auto');
                    echo $contenido . '<br>';
                }
            }
        } else {
            echo "Usuario no encontrado.";
        }
    ?>
    </div>
</body>
</html>