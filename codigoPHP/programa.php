<!DOCTYPE html>
<?php
/**
 * @author Isabel Martínez Guerra
 * @since 30/11/2021
 * 
 * Página principal.
 * Para acceder a ella se necesita haber hecho login.
 */

/*
 * Comienzo de la sesión.
 * Si no se ha hecho login (la variable de sesión del usuario no está definida),
 * devuelve al usuario a la página para hacerlo.
 */
session_start();
if (!isset($_SESSION['usuarioDAW204AppLoginLogoff'])) {
    header('Location: login.php');
}

/**
 * Si se selecciona cerrar sesión, se cierra y destruye, y vuelve a la página de login.
 */
if (isset($_REQUEST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

/*
 * Si se pide acceder a la ventana de detalle, accede a ella.
 */
if (isset($_REQUEST['detalle'])) {
    header("Location: detalle.php");
    exit;
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Página principal - LoginLogoutTema5</title>
        <link href="../webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <style>
            
        </style>
    </head>
    <body>
        <header>
            <?php include_once './elementoBtVolver.php'; // Botón de regreso  ?>
            <h1>Proyecto Login-Logout</h1>
        </header>
        <main>
            <div>Bienvenido <?php echo $_SESSION['usuarioDAW204AppLoginLogoff'] ?></div>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <input class="button" type="submit" name="detalle" value="detalle"/>
                <input class="button" type="submit" name="logout" value="logout"/>
            </form>
        </main>
<?php include_once './elementoFooter.php'; //Footer  ?>
    </body>
</html>
