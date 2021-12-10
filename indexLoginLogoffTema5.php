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
 * Si se quiere pasar a la página de login, crea la cookie para el idioma preferido
 * —por defecto en español— y pasa a la página.
 */
if (isset($_REQUEST['login'])) {
    /*
     * Validación de los posibles idiomas que puede tomar la página. Si el idioma
     * está entre los existentes, crea la cookie y manda al usuario al login.
     */
    if (!validacionFormularios::validarElementoEnLista($_REQUEST['listaIdiomas'], ['ES', 'EN', 'PT'])) {
        // Cookie con tiempo de expiración de 7 días.
        setcookie('idiomaPreferido', $_REQUEST['listaIdiomas'], time() + 604800);
        header('Location: ../LoginLogoffTema5/codigoPHP/login.php');
        exit;
    }
}

include_once './codigoPHP/idioma.php'; // Array de traducción de la web.
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Proyecto Login-Logoff</title>
        <link href="webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <link href="webroot/css/indexProyectoLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header>
            <h1><?php echo $aIdiomaHeader[$_COOKIE['idiomaPreferido']]['programa'] ?></h1>
        </header>
        <main>
            <!--
            <h2>Scripts</h2>
            <a href="scriptDB/CreaDAW204DBDepartamentosExplotacion.php">Creación</a>
            <a href="scriptDB/CargaInicialDAW204DBDepartamentosExplotacion.php">Carga</a>
            <a href="scriptDB/BorraDAW204DBDepartamentosExplotacion.php">Borrado</a>
            -->
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <input class="button" type='submit' name='login' value='Login'/>
                <div>
                    <select class="idioma" name="listaIdiomas" id="listaIdiomas">
                        <option value="ES" <?php echo (isset($_COOKIE['idiomaPreferido']) ? ($_COOKIE['idiomaPreferido'] == 'ES' ? 'selected' : '') : '') ?>>Español</option>
                        <option value="EN" <?php echo (isset($_COOKIE['idiomaPreferido']) ? ($_COOKIE['idiomaPreferido'] == 'EN' ? 'selected' : '') : '') ?>>English</option>
                        <option value="PT" <?php echo (isset($_COOKIE['idiomaPreferido']) ? ($_COOKIE['idiomaPreferido'] == 'PT' ? 'selected' : '') : '') ?>>Português</option>
                    </select>
                </div>
            </form>
        </main>
        <?php include_once './codigoPHP/elementoFooter.php'; // Footer   ?>
    </body>
</html>
