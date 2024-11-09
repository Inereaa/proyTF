
<?php

    // se comprueba si el usuario está autenticado
    if (!isset($_SESSION['usuario'])) {
        echo "Debes iniciar sesión para ver los muros.";
        exit;
    }
?>

<form>
    <input type='submit' value='Cerrar sesión' name='accion'/><br><br>
    <a href="usuarios/<?php echo htmlspecialchars($_SESSION['usuario']); ?>/muro.php">
    <input type='button' value='Mi muro'/>
</a>

</form>

<?php
    $directorios = './usuarios/';
    $gente = array_diff(scandir($directorios), array('..', '.'));
    echo "<div>";
    echo "<h3>Muros:</h3>";
    echo "<ul>";

    // genera un enlace para cada usuario
    foreach ($gente as $usr) {
        if (is_dir($directorios . $usr)) {
            echo "<li><a href='usuarios/$usr/muro.php?usuario=" . htmlspecialchars($usr) . "'>$usr</a></li>";
        }
    }

    echo "</ul>";
    echo "</div>";
?>
