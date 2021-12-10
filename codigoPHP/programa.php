<?php
/**
 * @author Isabel Martínez Guerra
 * @since 30/11/2021
 * Última modificación: 1/12/2021
 * 
 * Página principal.
 * Para acceder a ella se necesita haber hecho login.
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

// Si se selecciona cerrar sesión, se cierra y destruye, y vuelve a la página de login.
if (isset($_REQUEST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Si se decide editar perfil, accede a la página.
if (isset($_REQUEST['editarPerfil'])) {
    header("Location: editarPerfil.php");
    exit;
}

// Si se pide acceder a la ventana de detalle, accede a ella.
if (isset($_REQUEST['detalle'])) {
    header("Location: detalle.php");
    exit;
}

require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.
/**
 * Conexión a la base de datos para recoger el número de conexiones del usuario.
 */
try {
    // Conexión con la base de datos.
    $oDB = new PDO(HOST, USER, PASSWORD);
    $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query de selección.
    $sSelect = <<<QUERY
        SELECT T01_DescUsuario, T01_NumConexiones FROM T01_Usuario
        WHERE T01_CodUsuario='{$_SESSION['usuarioDAW204AppLoginLogoff']}';
    QUERY;

    // Preparación y ejecución de la consulta.
    $oResultadoSelect = $oDB->prepare($sSelect);
    $oResultadoSelect->execute();

    $oResultado = $oResultadoSelect->fetchObject();
} catch (PDOException $exception) {
    /*
     * Mostrado del código de error y su mensaje.
     */
    echo '<div>Se han encontrado errores:</div><ul>';
    echo '<li>' . $exception->getCode() . ' : ' . $exception->getMessage() . '</li>';
    echo '</ul>';
} finally {
    unset($oDB);
}

include_once './idioma.php'; // Array de traducción de la web.
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Página principal - LoginLogoutTema5</title>
        <link href="../webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <style>
            form{
                text-align: center;
                margin: 10px;
            }
            div.bienvenida{
                text-align: justify;
            }
            span.user{
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <header>
            <button class="volver" form="mainForm" type="submit" name="logout" value="Volver">
                <img class="normal" src="../webroot/media/img/left-arrow-indigo.png" alt="volver"><img class="hover" src="../webroot/media/img/left-arrow-teal.png" alt="volver">
            </button>  
            <h1><?php echo $aIdiomaHeader[$_COOKIE['idiomaPreferido']]['programa'] ?></h1>
        </header>
        <main>
            <div class="bienvenida">Bienvenido <span class="user"><?php echo $oResultado->T01_DescUsuario ?></span>, esta es la <?php echo $oResultado->T01_NumConexiones ?>ª vez que se conecta<?php
                if (!is_null($_SESSION['FechaHoraUltimaConexionAnterior'])) {
                    ?> y su última conexión fue <?php
                    echo $_SESSION['FechaHoraUltimaConexionAnterior'];
                }
                ?>.</div>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="mainForm">
                <input class="button" type="submit" name="detalle" value="Detalle"/>
                <input class="button" type="submit" name="editarPerfil" value="Editar perfil"/>
                <input class="button" type="submit" name="logout" value="Cerrar sesión"/>
            </form>
            <div class="info">
                <?php
                if (isset($_REQUEST['perfilEditado'])) {
                    if ($_REQUEST['perfilEditado'] == 'yes') {
                        echo 'Perfil editado con éxito.';
                    } else if ($_REQUEST['perfilEditado'] == 'no') {
                        echo 'No se ha editado el perfil.';
                    }
                }
                ?>
            </div>

        </main>
<?php include_once './elementoFooter.php'; //Footer    ?>
    </body>
</html>
