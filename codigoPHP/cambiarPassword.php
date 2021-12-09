<?php
/**
 * @author Isabel Martínez Guerra
 * @since 07/12/2021
 * Última modificación: 07/12/2021
 * 
 * Página de edición del perfil del usuario.
 */
/*
 * Recuperación de la sesión.
 * Si no se ha hecho login (la variable de sesión del usuario no está definida),
 * devuelve al usuario a la página para hacerlo.
 */
session_start();
if (!isset($_SESSION['usuarioDAW204AppLoginLogoff'])) {
    header('Location: login.php');
    exit;
}

// Si se ha seleccionado Cancelar, regresa a la página de programa.
if (isset($_REQUEST['cancelar'])) {
    header('Location: editarPerfil.php?passwordCambiada=no');
    exit;
}

require_once '../core/libreriaValidacion.php'; // Librería de validación.
require_once '../config/configApp.php'; // Constantes de validación.
require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/* Información del formulario */
$aFormulario = [
    'passwordNueva' => ''
];

/* Array de errores */
$aErrores = [
    'passwordActual' => '',
    'passwordNueva' => '',
    'passwordRepeticion' => ''
];

/**
 * Si se ha enviado el formulario, valida la entrada.
 */
if (isset($_REQUEST['cambiarPassword'])) {
    // Manejador de errores. 
    $bEntradaOK = true;

    /*
     * Comprobación si la contraseña actual introducida concuerda con la existente
     * en la base de datos.
     */
    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de selección.
        $sSelect = <<<QUERY
            SELECT T01_CodUsuario FROM T01_Usuario
            WHERE T01_CodUsuario='{$_SESSION['usuarioDAW204AppLoginLogoff']}' AND
            T01_Password=SHA2("{$_SESSION['usuarioDAW204AppLoginLogoff']}{$_REQUEST['passwordActual']}", 256);
        QUERY;

        // Preparación y ejecución de la consulta.
        $oResultadoSelect = $oDB->prepare($sSelect);
        $oResultadoSelect->execute();
        $oResultado = $oResultadoSelect->fetchObject();
    } catch (PDOException $exception) {
        /*
         * Si sucede alguna excepción, envía al usuario al login.
         */
        header('Location: editarPerfil.php?passwordCambiada=no');
        exit;
    } finally {
        unset($oDB);
    }    

    /*
     * Si el select no devuelve ningún resultado, es decir, la contraseña no
     * coincide con la introducida, crea el mensaje de error.
     */
    if (!$oResultado) {
        $aErrores['passwordActual'] = 'Contraseña incorrecta.';
    }
    /*
     * Si la contraseña coincide con la introducida, valida la nueva y comprueba
     * si la introducida repetida es igual.
     */
    else{
        // Si la descripción no cumple con lo especificado, mostrará el error.
        $aErrores['passwordNueva'] = validacionFormularios::validarPassword($_REQUEST['passwordNueva'], 8, 4, 1);
   
        // Si la nueva contraseña no coincide con la repetida, añade el error.
        if($_REQUEST['passwordNueva']!=$_REQUEST['passwordRepeticion']){
            $aErrores['passwordRepeticion'] = 'La contraseña repetida no coincide.';
        }
        
        /**
         * Recorrido del array de errores. Si existe alguno, pone el manejador
         * de errores a false.
         */
        foreach ($aErrores as $sCampo => $sError) {
            if ($sError != null) {
                $_REQUEST[$sCampo] = ''; //Limpieza del campo.
                $bEntradaOK = false;
            }
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
 * Si la entrada es correcta, modifica la contraseña.
 */
if ($bEntradaOK) {
    /* Recogida de información */
    $aFormulario['passwordNueva'] = $_REQUEST['passwordNueva'];
    
    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de actualización.
        $sUpdate = <<<QUERY
            UPDATE T01_Usuario SET T01_Password = SHA2("{$_SESSION['usuarioDAW204AppLoginLogoff']}{$aFormulario['passwordNueva']}", 256)
            WHERE T01_CodUsuario = "{$_SESSION['usuarioDAW204AppLoginLogoff']}";
        QUERY;

        // Preparación y ejecución de la actualización.
        $oResultadoUpdate = $oDB->prepare($sUpdate);
        $oResultadoUpdate->execute();
    } catch (PDOException $exception) {
        /*
         * Si sucede alguna excepción, envía al usuario a editar perfil.
         */
        header('Location: editarPerfil.php?passwordCambiada=no');
        exit;
    } finally {
        unset($oDB);
    }

    // Reenvío del usuario a la página de editar el perfil.
    header('Location: editarPerfil.php?passwordCambiada=yes');
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cambio de contraseña - LoginLogoutTema5</title>
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
            input[disabled]{
                background-color: paleturquoise;
                color: teal;
                text-align: center;
                border-bottom: 1px solid teal;

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
            <a class="volver" href="editarPerfil.php"><img class="normal" src="../webroot/media/img/left-arrow-indigo.png" alt="volver"><img class="hover" src="../webroot/media/img/left-arrow-teal.png" alt="volver"></a>        
            <h1>Cambio de contraseña</h1>
        </header>
        <main>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post'>
                <fieldset>
                    <ul>
                        <li><label class='obligatorio' for='passwordActual' >Contraseña actual</label></li>
                        <li><input class='obligatorio' type='password' name='passwordActual' id='passwordActual'/></li>
                        <li class="error"><?php echo $aErrores['passwordActual'] ?></li>
                    </ul>
                    <ul>
                        <li><label class='obligatorio' for='passwordNueva' >Nueva contraseña</label></li>
                        <li><input class='obligatorio' type='password' name='passwordNueva' id='passwordNueva'/></li>
                        <li class="error"><?php echo $aErrores['passwordNueva'] ?></li>
                    </ul>
                    <ul>
                        <li><label class='obligatorio' for='passwordRepeticion' >Repita la contraseña</label></li>
                        <li><input class='obligatorio' type='password' name='passwordRepeticion' id='passwordRepeticion'/></li>
                        <li class="error"><?php echo $aErrores['passwordRepeticion'] ?></li>
                    </ul>
                </fieldset>
                <input class="button" type='submit' name='cancelar' value='Cancelar'/>
                <input class="button" type='submit' name='cambiarPassword' value='Cambiar contraseña'/>
            </form>
        </main>
        <?php include_once './elementoFooter.php'; //Footer          ?>
    </body>
</html>