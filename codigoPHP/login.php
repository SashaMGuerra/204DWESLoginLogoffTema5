<?php
/**
 * @author Isabel Martínez Guerra
 * @since 29/11/2021
 * Última modificación: 1/12/2021
 * 
 * Página de identificación de usuario.
 * 
 * Comprueba si la combinación usuario-contraseña introducidos existen en la base
 * de datos, y si no es así, lo pide de nuevo.
 */

/*
 * Si se quiere registrar, va a la página de registro.
 */
if (isset($_REQUEST['registro'])) {
    header('Location: registro.php');
    exit;
}

require_once '../core/libreriaValidacion.php'; // Librería de validación.
require_once '../config/configApp.php'; // Constantes de validación.
require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/* Información del formulario */
$aFormulario = [
    'usuario' => '',
    'password' => ''
];

/**
 * Si se ha enviado el formulario, valida la entrada.
 */
if (isset($_REQUEST['login'])) {
    // Manejador de errores. 
    $bEntradaOK = true;

    /*
     * Si el usuario y/o password no está definido, o si se ha introducido de
     * forma incorrecta, pone la entrada como incorrecta.
     */
    if (validacionFormularios::comprobarAlfaNumerico($_REQUEST['usuario'], 8, 4, OBLIGATORIO)
            || validacionFormularios::comprobarAlfaNumerico($_REQUEST['password'], 8, 4, OBLIGATORIO)) {
        $bEntradaOK = false;
    }
    /**
     * Si no existe ningún error por el momento, comprueba que el usuario y la
     * contraseña existan y sean correctos en la base de datos.
     */
    else {
        /* Recogida de información */
        $aFormulario['usuario'] = $_REQUEST['usuario'];
        $aFormulario['password'] = $_REQUEST['password'];

        try {
            // Conexión con la base de datos.
            $oDB = new PDO(HOST, USER, PASSWORD);
            $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Query de selección.
            $sSelect = <<<QUERY
                SELECT T01_FechaHoraUltimaConexion FROM T01_Usuario
                WHERE T01_CodUsuario='{$aFormulario['usuario']}' AND
                T01_Password=SHA2("{$aFormulario['usuario']}{$aFormulario['password']}", 256);
            QUERY;

            // Preparación y ejecución de la consulta.
            $oResultadoSelect = $oDB->prepare($sSelect);
            $oResultadoSelect->execute();

            /**
             * Si bien la fecha-hora de última conexión puede existir como null,
             * el resultado del select devuelve un objecto que contiene esta
             * información como atributo.
             */
            $oResultado = $oResultadoSelect->fetchObject();
        } catch (PDOException $exception) {
            /*
             * Si sucede alguna excepción, envía al usuario al login.
             */
            header('Location: login.php');
            exit;
        } finally {
            unset($oDB);
        }

        /*
         * Si el select no devuelve ningún resultado, es decir, el usuario no
         * existe, o su su contraseña no coincide con la introducida, indica
         * que la entrada es incorrecta.
         */
        if (!$oResultado) {
            $bEntradaOK = false;
        }
    }
}
/*
 * Si el formulario no ha sido enviado, pone el manejador de errores
 * a false para poder mostrar el formulario.
 */ else {
    $bEntradaOK = false;
}

/**
 * Si la entrada es correcta, crea la variable de sesión que almacena el usuario.
 * Además, se conecta a la base de datos y actualiza la tabla para indicar 
 * la última conexión.
 * Finalmente pasa al usuario a la página de programa.php
 */
if ($bEntradaOK) {
    // Añadido al registro de conexiones y última hora de conexión.
    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de actualización.
        $sUpdate = <<<QUERY
            UPDATE T01_Usuario SET T01_NumConexiones=T01_NumConexiones+1,
            T01_FechaHoraUltimaConexion = unix_timestamp(now())
            WHERE T01_CodUsuario='{$aFormulario['usuario']}';
        QUERY;

        // Preparación y ejecución de la actualización.
        $oResultadoUpdate = $oDB->prepare($sUpdate);
        $oResultadoUpdate->execute();
    } catch (PDOException $exception) {
        /*
         * Si sucede alguna excepción, envía al usuario al login.
         */
        header('Location: login.php');
        exit;
    } finally {
        unset($oDB);
    }

    /* Inicio de la sesión para almacenar el código de usuario */
    session_start();

    /*
     * Variables de sesión para el usuario.
     * 
     * Si el timestamp recogido es diferente a null, lo guarda formateado
     * porque en la ventana de detalle, si no, da error.
     */
    $_SESSION['usuarioDAW204AppLoginLogoff'] = $aFormulario['usuario'];
    if (!is_null($oResultado->T01_FechaHoraUltimaConexion)) {
        $oFecha = new DateTime();
        $oFecha->setTimestamp($oResultado->T01_FechaHoraUltimaConexion);
        $_SESSION['FechaHoraUltimaConexionAnterior'] = $oFecha->format('d/m/Y H:i:s T');
    }
    // Si no ha habido conexiones anteriores, pone la fecha de última conexión a null.
    else {
        $_SESSION['FechaHoraUltimaConexionAnterior'] = null;
    }

    // Reenvío del usuario a la página de programa.
    header('Location: programa.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login - LoginLogoutTema5</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <style>
            @media (min-width: 1350px){
                body{
                    background-image: url('../webroot/media/img/background-cat.jpg');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-attachment: fixed;
                }
            }
            @media (max-width: 1350px){
                body{
                    background-image: url('../webroot/media/img/background-cat-medium.jpg');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-attachment: fixed;
                }
            }
            @media (max-width: 900px){
                body{
                    background-image: url('../webroot/media/img/background-cat-vertical.jpg');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-attachment: fixed;
                }
            }

            header{
                background-color: rgb(75, 0, 130, 0.75); /* Indigo */
                color: paleturquoise;
                border-bottom: 3px solid teal;

            }
            footer{
                background-color: rgb(75, 0, 130, 0.75); /* Indigo */
                border-top: 3px solid teal;
            }

            form{
                text-align: center;
                padding: 10px 0 20px;
                background-color: rgb(75,0,130, 0.5);  /* Indigo */
                color: white;
                box-shadow: 0 0 30px black;
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
            input[type="text"], input[type="password"]{
                border: none;
                padding: 10px;
                border-bottom: 1px solid indigo;
                background-color: ghostwhite;
            }
            label.obligatorio:after{
                content: "*";
                color: paleturquoise;
            }

            input.button{
                background-color: paleturquoise;
            }
        </style>
    </head>
    <body>
        <header>
<?php include_once './elementoBtVolver.php'; // Botón de regreso          ?>
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
                </fieldset>
                <input class="button" type='submit' name='login' value='Iniciar sesión'/>
                <input class="button" type='submit' name='registro' value='Registrarse'/>
            </form>
        </main>
<?php include_once './elementoFooter.php'; //Footer       ?>
    </body>
</html>