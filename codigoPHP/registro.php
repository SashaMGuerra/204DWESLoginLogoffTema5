<?php
/**
 * @author Isabel Martínez Guerra
 * @since 07/12/2021
 * Última modificación: 07/12/2021
 * 
 * Página de registro de usuario.
 */

require_once '../core/libreriaValidacion.php'; // Librería de validación.
require_once '../config/configApp.php'; // Constantes de validación.
require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/* Información del formulario */
$aFormulario = [
    'usuario' => '',
    'descripcion' => '',
    'password' => ''
];

/* Almacén para error de usuario ya existente */
$sError = '';

/**
 * Si se ha enviado el formulario, valida la entrada.
 */
if (isset($_REQUEST['registro'])) {
    // Manejador de errores. 
    $bEntradaOK = true;

    /*
     * Si el usuario, descripción o password no están definidos, o si se ha introducido de
     * forma incorrecta, pone la entrada como incorrecta.
     */
    if (validacionFormularios::comprobarAlfaNumerico($_REQUEST['usuario'], 8, 4, OBLIGATORIO) ||
        validacionFormularios::comprobarAlfaNumerico($_REQUEST['descripcion'], 255, 3, OBLIGATORIO) ||
            validacionFormularios::comprobarAlfaNumerico($_REQUEST['password'], 8, 4, OBLIGATORIO)) {
        $bEntradaOK = false;
        $sError = 'Tanto el usuario como la contraseña deben tener entre 8 y 4 caracteres.<br>Nombre y apellidos deben tener mínimo 3 caracteres.';
    }
    /**
     * Si no existe ningún error por el momento, comprueba que el usuario no exista
     * ya en la base de datos.
     */
    else {
        try {
            // Conexión con la base de datos.
            $oDB = new PDO(HOST, USER, PASSWORD);
            $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Query de selección.
            $sSelect = <<<QUERY
                SELECT T01_CodUsuario FROM T01_Usuario
                WHERE T01_CodUsuario='{$_REQUEST['usuario']}';
            QUERY;

            // Preparación y ejecución de la consulta.
            $oResultadoSelect = $oDB->prepare($sSelect);
            $oResultadoSelect->execute();

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
         * Si el select devuelve algún resultado, es decir, el usuario existe,
         * indica que la entrada es incorrecta.
         */
        if ($oResultado) {
            $bEntradaOK = false;
            $sError = 'El usuario ya existe.';
        }
    }
}
/*
 * Si el formulario no ha sido enviado, pone el manejador de errores
 * a false para poder mostrar el formulario.
 */
else {
    $bEntradaOK = false;
}

/**
 * Si la entrada es correcta, añade el usuario y contraseña a la base de datos.
 * 
 * Además, crea una sesión para almacenar el usuario recién creado, y pasa al
 * usuario a la página de programa.
 */
if ($bEntradaOK) {
    /* Recogida de información */
    $aFormulario['usuario'] = $_REQUEST['usuario'];
    $aFormulario['descripcion'] = $_REQUEST['descripcion'];
    $aFormulario['password'] = $_REQUEST['password'];
    
    // Añadido al registro de conexiones y última hora de conexión.
    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de actualización.
        $sInsert = <<<QUERY
            INSERT INTO T01_Usuario(T01_CodUsuario, T01_Password, T01_DescUsuario, T01_FechaHoraUltimaConexion) VALUES
            ("{$aFormulario['usuario']}", SHA2("{$aFormulario['usuario']}{$aFormulario['password']}", 256), "{$aFormulario['descripcion']}", UNIX_TIMESTAMP());
        QUERY;

        // Preparación y ejecución de la actualización.
        $oResultadoInsert = $oDB->prepare($sInsert);
        $oResultadoInsert->execute();
        
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
     */
    $_SESSION['usuarioDAW204AppLoginLogoff'] = $aFormulario['usuario'];
    // Ya que no ha habido conexiones anteriores, pone la fecha de última conexión a null.
    $_SESSION['FechaHoraUltimaConexionAnterior'] = null;

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
            form{
                text-align: center;
                padding: 10px 0 20px;
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
                color: teal;
            }
            
            .error{
                color: indigo;
                font-size: small;
            }
        </style>
    </head>
    <body>
        <header>
            <?php include_once './elementoBtVolver.php'; // Botón de regreso ?>
            <h1>Creación de usuario</h1>
        </header>
        <main>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post'>
                <fieldset>
                    <ul>
                        <li><label class="obligatorio" for='usuario' >Usuario</label></li>
                        <li><input class="obligatorio" type='text' name='usuario' id='usuario'/></li>
                    </ul>
                    <ul>
                        <li><label class="obligatorio" for='descripcion' >Nombre y apellidos</label></li>
                        <li><input class="obligatorio" type='text' name='descripcion' id='descripcion' value="<?php echo $_REQUEST['descripcion']??'' ?>"/></li>
                    </ul>
                    <ul>
                        <li><label class="obligatorio" for='password' >Contraseña</label></li>
                        <li><input class="obligatorio" type='password' name='password' id='password'/></li>
                    </ul>
                    <div class="error"><?php echo $sError; ?></div>
                </fieldset>
                <input class="button" type='submit' name='registro' value='Registrarse'/>
            </form>
        </main>
        <?php include_once './elementoFooter.php'; //Footer       ?>
    </body>
</html>