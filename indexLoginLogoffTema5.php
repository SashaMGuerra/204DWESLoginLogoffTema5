<?php
/**
 * @author Isabel Martínez Guerra
 * @since 29/11/2021
 * Última modificación: 02/12/2021
 * 
 * Página de entrada de la aplicación.
 * Permite elegir su idioma.
 */
require_once './core/libreriaValidacion.php'; // Librería de validación.


/**
 * Si no se ha creado aún la cookie de idioma preferido, la crea en español por
 * defecto.
 * Recarga la página.
 */
if (!isset($_COOKIE['idiomaPreferido'])) {
    setcookie('idiomaPreferido', 'ES', time() + 604800);
    header('Location: indexLoginLogoffTema5.php');
    exit;
}

/**
 * Si se ha elegido un idioma, y el idioma elegido está entre los existentes en
 * la lista de idiomas, modifica la cookie y recarga la página.
 */
if (isset($_REQUEST['idioma']) && !validacionFormularios::validarElementoEnLista($_REQUEST['idioma'], ['ES', 'EN', 'PT'])) {
    setcookie('idiomaPreferido', $_REQUEST['idioma'], time() + 604800);
    header('Location: indexLoginLogoffTema5.php');
    exit;
}

/**
 * Si se quiere pasar a la página de login, crea la cookie para el idioma preferido
 * —por defecto en español— y pasa a la página.
 */
if (isset($_REQUEST['login'])) {
    header('Location: codigoPHP/login.php');
    exit;
}

include_once './codigoPHP/idioma.php'; // Array de traducción de la web.
?>
<!DOCTYPE html>
<html lang="<?php echo $_COOKIE['idiomaPreferido'] ?>">
    <head>
        <meta charset="UTF-8">
        <title>Proyecto Login-Logoff</title>
        <link href="webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <link href="webroot/css/indexProyectoLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header>
            <h1><?php echo $aIdiomaHeader[$_COOKIE['idiomaPreferido']]['programa'] ?></h1>
            <div class="idiomas">
                <button class="idioma <?php echo ($_COOKIE['idiomaPreferido'] == 'ES' ? 'selected' : '') ?>" type="submit" name="idioma" value="ES" form="mainForm">ES</button>
                <button class="idioma <?php echo ($_COOKIE['idiomaPreferido'] == 'EN' ? 'selected' : '') ?>" type="submit" name="idioma" value="EN" form="mainForm">EN</button>
                <button class="idioma <?php echo ($_COOKIE['idiomaPreferido'] == 'PT' ? 'selected' : '') ?>" type="submit" name="idioma" value="PT" form="mainForm">PT</button>
            </div>
        </header>
        <main>
            <!--
            <h2>Scripts</h2>
            <a href="scriptDB/CreaDAW204DBDepartamentosExplotacion.php">Creación</a>
            <a href="scriptDB/CargaInicialDAW204DBDepartamentosExplotacion.php">Carga</a>
            <a href="scriptDB/BorraDAW204DBDepartamentosExplotacion.php">Borrado</a>
            -->
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="mainForm">
                <input class="button" type='submit' name='login' value='Login'/>
            </form>
        </main>
<?php include_once './codigoPHP/elementoFooter.php'; // Footer    ?>
    </body>
</html>
