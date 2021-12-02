<!DOCTYPE html>
<?php
/**
 * @author Isabel Martínez Guerra
 * @since 29/11/2021
 * Última modificación: 02/12/2021
 * 
 * Página de entrada de la aplicación.
 * Permite elegir su idioma.
 */

if (isset($_REQUEST['login'])) {
    var_dump($_REQUEST['listaIdiomas']);
    setcookie('idiomaPreferido', $_REQUEST['listaIdiomas']);
    /*
    header('Location: codigoPHP/login.php');
    exit;
     * 
     */
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Proyecto Login-Logoff</title>
        <link href="webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <link href="webroot/css/indexProyectoLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <header>
            <h1>Tema 5 - Login-Logout</h1>
        </header>
        <main>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <select name="listaIdiomas" id="listaIdiomas">
                    <option value="spanish" <?php echo (isset($_REQUEST['listaIdiomas']) ? ($_REQUEST['listaIdiomas'] == 'spanish' ? 'selected' : '') : '') ?>>Español</option>
                    <option value="english" <?php echo (isset($_REQUEST['listaIdiomas']) ? ($_REQUEST['listaIdiomas'] == 'english' ? 'selected' : '') : '') ?>>English</option>
                    <option value="portuguese" <?php echo (isset($_REQUEST['listaIdiomas']) ? ($_REQUEST['listaIdiomas'] == 'portuguese' ? 'selected' : '') : '') ?>>Português</option>
                </select>
                <input class="button" type='submit' name='login' value='Login'/>
            </form>
        </main>
        <?php include_once './codigoPHP/elementoFooter.php'; // Footer ?>
    </body>
</html>
