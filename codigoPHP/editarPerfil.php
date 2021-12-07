<?php
/**
 * @author Isabel Martínez Guerra
 * @since 07/12/2021
 * Última modificación: 07/12/2021
 * 
 * Página de edición del perfil del usuario.
 */
/*
 * Continuación de la sesión.
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
    header('Location: programa.php');
    exit;
}

require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/*
 * Si se ha seleccionado Eliminar cuenta, elimina la cuenta, cierra sesión y
 * regresa a la página de login.
 */
if (isset($_REQUEST['eliminarCuenta'])) {
    // Eliminación del usuario.
    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de eliminación.
        $sDelete = <<<QUERY
            DELETE FROM T01_Usuario
            WHERE T01_CodUsuario='{$_SESSION['usuarioDAW204AppLoginLogoff']}';
        QUERY;

        // Preparación y ejecución de la actualización.
        $oResultadoDelete = $oDB->prepare($sDelete);
        $oResultadoDelete->execute();
    } catch (PDOException $exception) {
        /*
         * Si sucede alguna excepción, envía al usuario al login.
         */
        header('Location: login.php');
        exit;
    } finally {
        unset($oDB);
    }
    
    // Destrucción de la sesión.
    session_unset();
    session_destroy();
   
    // Regreso a la página de login.
    header('Location: login.php');
    exit;
}

// Si se ha seleccionado cambiar la contraseña, va a la ventana.
if (isset($_REQUEST['cambiarPassword'])) {
    header('Location: cambiarPassword.php');
    exit;
}

require_once '../core/libreriaValidacion.php'; // Librería de validación.
require_once '../config/configApp.php'; // Constantes de validación.
require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/* Información del formulario */
$aFormulario = [
    'usuario' => $_SESSION['usuarioDAW204AppLoginLogoff'],
    'descripcion' => ''
];

/* Array de errores */
$aErrores = [
    'descripcion' => ''
];


/* Recopilación de información sobre el usuario */
try {
    // Conexión con la base de datos.
    $oDB = new PDO(HOST, USER, PASSWORD);
    $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query de selección.
    $sSelect = <<<QUERY
        SELECT T01_DescUsuario FROM T01_Usuario
        WHERE T01_CodUsuario='{$aFormulario['usuario']}';
    QUERY;

    // Preparación y ejecución de la consulta.
    $oResultadoSelect = $oDB->prepare($sSelect);
    $oResultadoSelect->execute();

    $oResultado = $oResultadoSelect->fetchObject();
    $aFormulario['descripcion'] = $oResultado->T01_DescUsuario;
} catch (PDOException $exception) {
    /*
     * Si sucede alguna excepción, envía al usuario al login.
     */
    header('Location: login.php');
    exit;
} finally {
    unset($oDB);
}

/**
 * Si se ha enviado el formulario, valida la entrada.
 */
if (isset($_REQUEST['editarPerfil'])) {
    // Manejador de errores. 
    $bEntradaOK = true;

    // Si la descripción no cumple con lo especificado, mostrará el error.
    $aErrores['descripcion'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['descripcion'], 255, 3, OBLIGATORIO);

    // Recorrido del array de errores. Si encuentra alguno pone el manejador a false.
    foreach ($aErrores as $sCampo => $sError) {
        if ($sError != null) {
            $_REQUEST[$sCampo] = ''; //Limpieza del campo.
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
 * Si la entrada es correcta, modifica la descripción.
 */
if ($bEntradaOK) {
    /* Recogida de información */
    $aFormulario['descripcion'] = $_REQUEST['descripcion'];

    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de actualización.
        $sUpdate = <<<QUERY
            UPDATE T01_Usuario SET T01_DescUsuario = "{$aFormulario['descripcion']}"
            WHERE T01_CodUsuario = "{$aFormulario['usuario']}";
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
            input[disabled]{
                background-color: paleturquoise;
                color: teal;
                text-align: center;
                border-bottom: 1px solid teal;

            }
            
            
            input.button.password{
                color: teal;
                border: 2px solid teal;
                background-color: paleturquoise;
            }
            input.button.password:hover{
                color: indigo;
                border: 2px solid indigo;
                background-color: ghostwhite;
            }
            
            /*
            label.obligatorio:after{
                content: "*";
                color: teal;
            }
            */
            .error{
                color: indigo;
                font-size: small;
            }
        </style>
    </head>
    <body>
        <header>
            <a class="volver" href="programa.php"><img class="normal" src="../webroot/media/img/left-arrow-indigo.png" alt="volver"><img class="hover" src="../webroot/media/img/left-arrow-teal.png" alt="volver"></a>        
            <h1>Creación de usuario</h1>
        </header>
        <main>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post'>
                <fieldset>
                    <ul>
                        <li><label for='usuario' >Nombre de usuario</label></li>
                        <li><input type='text' name='usuario' id='usuario' value="<?php echo $aFormulario['usuario'] ?>" disabled/></li>
                    </ul>
                    <ul>
                        <li><label class="obligatorio" for='descripcion' >Nombre y apellidos</label></li>
                        <li><input class="obligatorio" type='text' name='descripcion' id='descripcion' value="<?php echo $aFormulario['descripcion'] ?? '' ?>"/></li>
                        <li><?php echo $aErrores['descripcion'] ?></li>
                    </ul>
                    <ul>
                        <li><input class="button password" type='submit' name='cambiarPassword' value='Cambiar contraseña'/></li>
                        <li>
                        <?php
                        if(isset($_REQUEST['passwordCambiada'])){
                            if($_REQUEST['passwordCambiada']=='yes'){
                                echo 'La contraseña se ha cambiado.';
                            }
                            else if($_REQUEST['passwordCambiada']=='no'){
                                echo 'La contraseña no se ha cambiado.';
                            }
                        }
                        ?>
                        </li>
                    </ul>
                </fieldset>
                <input class="button" type='submit' name='cancelar' value='Cancelar'/>
                <input class="button" type='submit' name='editarPerfil' value='Efectuar cambios'/>
                <input class="button" type='submit' name='eliminarCuenta' value='Eliminar cuenta'/>
            </form>
        </main>
<?php include_once './elementoFooter.php'; //Footer         ?>
    </body>
</html>