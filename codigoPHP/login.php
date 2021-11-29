<!DOCTYPE html>
<?php
/**
 * @author Isabel Martínez Guerra
 * @since 29/11/2021
 * 
 * Página de identificación de usuario.
 * 
 * Comprueba si la combinación usuario-contraseña introducidos existen en la base
 * de datos, y si no es así, lo pide de nuevo.
 */
require_once '../core/libreriaValidacion.php'; // Librería de validación.
require_once '../config/configApp.php'; // Constantes de validación.
require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/* Información del formulario */
$aFormulario = [
    'usuario' => '',
    'password' => ''
];

// Variable de error. Si al enviar el formulario existe alguno, lo muestra.
$sError = '';

if (isset($_REQUEST['submit'])) {
    // Manejador de errores. 
    $bEntradaOK = true;

    /*
     * Validación de entrada.
     */
    if (validacionFormularios::comprobarAlfaNumerico($_REQUEST['usuario'], 8, 4, OBLIGATORIO) || validacionFormularios::comprobarAlfaNumerico($_REQUEST['password'], 8, 4, OBLIGATORIO)) {
        $sError = "Debes introducir un nombre de usuario y una contraseña válidos";
        $bEntradaOK = false;
    }
}

/*
 * Si el formulario no ha sido enviado, pone el manejador de errores
 * a false para poder mostrar el formulario.
 */ else {
    $bEntradaOK = false;
}

/**
 * Si la entrada es correcta, se conecta a la base de datos y comprueba que
 * el usuario y contraseña sean correctos.
 */
if ($bEntradaOK) {
    /*Recogida de información*/
    $aFormulario['usuario'] = $_REQUEST['usuario'];
    $aFormulario['password'] = $_REQUEST['password'];
    
    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de selección.
        $sSelect = "SELECT T01_Password FROM T01_Usuario WHERE T01_CodUsuario='{$aFormulario['usuario']}'";

        // Preparación y ejecución de la consulta.
        $oResultadoSelect = $oDB->prepare($sSelect);
        $oResultadoSelect->execute();

        /*
         * Si el select no devuelve ningún resultado, es decir, el usuario no
         * existe, o si su contraseña no coincide con la introducida, indica
         * que la entrada es incorrecta.
         * 
         * Dado que la contraseña está cifrada en la base de datos, se utiliza
         * el comando hash para codificar la introducida y comprobar si son
         * la misma.
         */
        $oResultado = $oResultadoSelect->fetchObject();
        if (!$oResultado || $oResultado->T01_Password != hash('sha256', ($aFormulario['usuario'] . $aFormulario['password']))) {
            $sError = "Debes introducir un nombre de usuario y una contraseña válidos";
            $bEntradaOK = false;
        }
        /**
         * Si todo es correcto, actualiza la tabla para indicar la última conexión.
         */ else {
            /**
             * Si usuario y contraseña son correctos, añade la conexión al registro
             * de conexiones, y añade la última hora de conexión.
             */
            $oDateTime = new DateTime();

            // Query de actualización.
            $sUpdate = <<<QUERY
                    UPDATE T01_Usuario SET T01_NumConexiones=T01_NumConexiones+1,
                    T01_FechaHoraUltimaConexion = '{$oDateTime->format("y-m-d h:i:s")}'
                    WHERE T01_CodUsuario='{$aFormulario['usuario']}'
            QUERY;

            // Preparación y ejecución de la actualización.
            $oResultadoUpdate = $oDB->prepare($sUpdate);
            $oResultadoUpdate->execute();
        }
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
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login - LoginLogoutTema5</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <style>
            form{
                text-align: center;
                max-width: 500px;
                margin: auto;
            }
            fieldset{
                border: none;
            }
            ul{
                list-style: none;
                margin: 0;
                padding: 0;
                margin-bottom: 20px;
            }
            li{
                margin-bottom: 10px;
            }
            label.obligatorio:after{
                content: "*";
                color: red;
            }
            div.error{
                color: red;
                font-size: smaller;
            }

            input[type="text"], input[type="password"]{
                border: none;
                padding: 10px;
                border-bottom: 1px solid indigo;
            }
            input[type="submit"]{
                border: 2px solid indigo;
                background-color: ghostwhite;
                color: indigo;
                text-transform: uppercase;
                font-weight: bold;
                padding: 5px 10px;
                cursor: pointer;
            }
            input[type="submit"]:hover{
                border: 2px solid teal;
                background-color: paleturquoise;
                color: teal;
            }
        </style>
    </head>
    <body>
        <header>
            <?php include_once './elementoBtVolver.php'; // Botón de regreso       ?>
            <h1>Acceso a la aplicación</h1>
        </header>
        <main>
            <form action='login.php' method='post'>
                <fieldset>
                    <ul>
                        <li><label class="obligatorio" for='usuario' >Usuario</label></li>
                        <li><input class="obligatorio" type='text' name='usuario' id='usuario'/></li>
                    </ul>
                    <ul>
                        <li><label class="obligatorio" for='password' >Contraseña</label></li>
                        <li><input class="obligatorio" type='password' name='password' id='password'/></li>
                    </ul>
                    <div class="error"><?php echo $sError ?? ''; ?></div>
                </fieldset>
                <input type='submit' name='submit' value='Entrar'/>
            </form>
        </main>
            <?php include_once './elementoFooter.php'; //Footer    ?>
    </body>
</html>