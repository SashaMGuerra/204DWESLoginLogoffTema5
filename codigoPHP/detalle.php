<?php
/**
 * @author Isabel Martínez Guerra
 * @since 30/11/2021
 * 
 * Ventana de detalle.
 * Muestra el contenido de las variables superglobales y de phpinfo().
 */
/*
 * Recuperación de la sesión.
 * Si no se ha hecho login (la variable de sesión del usuario no está definida),
 * devuelve al usuario a la página para hacerlo.
 */
session_start();
if (!isset($_SESSION['usuarioDAW204AppLoginLogoff'])) {
    header('Location: login.php');
}

include_once './idioma.php'; // Array de traducción de la web.
?>
<!DOCTYPE html>
<html lang="<?php echo $_COOKIE['idiomaPreferido'] ?>">
    <head>
        <meta charset="UTF-8">
        <title>Ventana de detalle - LoginLogoutTema5</title>
        <link href="../webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <style>
            h2{
                text-align: center;
            }
            table{
                margin: auto;
                table-layout: fixed;
            }
            td{
                overflow-wrap: break-word;
            }
        </style>
    </head>
    <body>
        <header>
            <a class="volver" href="programa.php"><img class="normal" src="../webroot/media/img/left-arrow-indigo.png" alt="volver"><img class="hover" src="../webroot/media/img/left-arrow-teal.png" alt="volver"></a>        
            <h1><?php echo $aIdiomaHeader[$_COOKIE['idiomaPreferido']]['detalle'] ?></h1>
        </header>
        <main>
            <h2>$_SESSION</h2>
            <table>
                <?php
                foreach ($_SESSION as $key => $value) {
                    echo '<tr>';
                    echo "<td>$key</td>";
                    echo "<td>$value</td>";
                    echo '</tr>';
                }
                ?>
            </table>
            <h2>$_COOKIE</h2>
            <table>
                <?php
                foreach ($_COOKIE as $key => $value) {
                    echo '<tr>';
                    echo "<td>$key</td>";
                    echo "<td>$value</td>";
                    echo '</tr>';
                }
                ?>
            </table>
            <h2>$_SERVER</h2>
            <table>
                <?php
                foreach ($_SERVER as $key => $value) {
                    echo '<tr>';
                    echo "<td>$key</td>";
                    echo "<td>$value</td>";
                    echo '</tr>';
                }
                ?>
            </table>
            <h2>$_REQUEST</h2>
            <table>
                <?php
                foreach ($_REQUEST as $key => $value) {
                    echo '<tr>';
                    echo "<td>$key</td>";
                    echo "<td>$value</td>";
                    echo '</tr>';
                }
                ?>
            </table>
            <h2>$_FILES</h2>
            <table>
                <?php
                foreach ($_FILES as $key => $value) {
                    echo '<tr>';
                    echo "<td>$key</td>";
                    echo "<td>$value</td>";
                    echo '</tr>';
                }
                ?>
            </table>
            <h2>$_ENV</h2>
            <table>
                <?php
                foreach ($_ENV as $key => $value) {
                    echo '<tr>';
                    echo "<td>$key</td>";
                    echo "<td>$value</td>";
                    echo '</tr>';
                }
                ?>
            </table>
            <hr>
            <?php
            phpinfo();
            ?>
        </main>
        <footer>
            <a style="background-color: transparent" target="_blank" href="https://github.com/SashaMGuerra/204DWESLoginLogoffTema5"><img src="../webroot/media/img/github_logo_white.png" alt="repositorio"></a>
            <div>© 2021-2022 Isabel Martínez Guerra — IES Los Sauces (Benavente, Zamora) — Modificado el 24/11/2021.</div>
        </footer>
    </body>
</html>
