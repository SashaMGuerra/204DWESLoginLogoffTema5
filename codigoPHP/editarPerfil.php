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
    header('Location: programa.php?perfilEditado=no');
    exit;
}

require_once '../config/configDB.php'; // Constantes de conexión a la base de datos.

/*
 * Si se ha confirmado Eliminar cuenta, elimina la cuenta, cierra sesión y
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
        header('Location: programa.php?perfilEliminado=no');
        exit;
    } finally {
        unset($oDB);
    }

    // Destrucción de la sesión.
    session_unset();
    session_destroy();

    // Regreso a la página de login.
    header('Location: login.php?perfilEliminado=yes');
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
    'descripcion' => '',
    'numConexiones' => '',
    'fechaHoraUltimaConexion' => '',
    'perfil' => '',
    'imagenUsuario' => ''
];

/* Array de errores */
$aErrores = [
    'descripcion' => '',
    'imagenUsuario' => ''
];


/* Recopilación de información sobre el usuario */
try {
    // Conexión con la base de datos.
    $oDB = new PDO(HOST, USER, PASSWORD);
    $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query de selección.
    $sSelect = <<<QUERY
        SELECT * FROM T01_Usuario
        WHERE T01_CodUsuario='{$aFormulario['usuario']}';
    QUERY;

    // Preparación y ejecución de la consulta.
    $oResultadoSelect = $oDB->prepare($sSelect);
    $oResultadoSelect->execute();

    $oResultado = $oResultadoSelect->fetchObject();
    $aFormulario['descripcion'] = $oResultado->T01_DescUsuario;
    $aFormulario['numConexiones'] = $oResultado->T01_NumConexiones;
    $aFormulario['fechaHoraUltimaConexion'] = $_SESSION['FechaHoraUltimaConexionAnterior'];
    $aFormulario['perfil'] = $oResultado->T01_Perfil;
    $aFormulario['imagenUsuario'] = $oResultado->T01_ImagenUsuario;
} catch (PDOException $exception) {
    /*
     * Si sucede alguna excepción, envía al usuario al login.
     */
    header('Location: programa.php?perfilEditado=no');
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
    /*
     * El nombre de la imagen no puede contener caracteres diferentes a letras o
     * números.
     * La imagen debe ser menor a 2MG (tamaño máximo por defecto).
     */
    $aErrores['imagenUsuario'] = validacionFormularios::validarNombreArchivo($_FILES['imagenUsuario']['name'],['jpg', 'jpeg', 'png'], 255, 3);
    
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

    /*
     * Si se desea eliminar la imagen de usuario, lo rellena en el formulario
     * como null.
     * Si se ha subido una imagen, toma el archivo y lo codifica en base64 para 
     * mostrarse como imagen.
     */
    if (isset($_REQUEST['eliminarImagenUsuario'])) {
        $aFormulario['imagenUsuario'] = null;
    } else if ($_FILES['imagenUsuario']['name'] != '') {
        // Guarda la imagen codificada en base64 para poder mostrarse con <img>.
        $aFormulario['imagenUsuario'] = base64_encode(file_get_contents($_FILES['imagenUsuario']['tmp_name']));
    }


    try {
        // Conexión con la base de datos.
        $oDB = new PDO(HOST, USER, PASSWORD);
        $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query de actualización.
        $sUpdate = <<<QUERY
            UPDATE T01_Usuario SET T01_DescUsuario = "{$aFormulario['descripcion']}",
            T01_ImagenUsuario = '{$aFormulario['imagenUsuario']}'
            WHERE T01_CodUsuario = "{$aFormulario['usuario']}";
        QUERY;

        // Preparación y ejecución de la actualización.
        $oResultadoUpdate = $oDB->prepare($sUpdate);
        $oResultadoUpdate->execute();
    } catch (PDOException $exception) {
        /*
         * Si sucede alguna excepción, envía al usuario al login.
         */
        header('Location: programa.php?perfilEditado=no');
        exit;
    } finally {
        unset($oDB);
    }

    // Reenvío del usuario a la página de programa.
    header('Location: programa.php?perfilEditado=yes');
    exit;
}

include_once '../config/idioma.php'; // Array de traducción de la web.
?>
<!DOCTYPE html>
<html lang="<?php echo $_COOKIE['idiomaPreferido'] ?>">
    <head>
        <meta charset="UTF-8">
        <title>Editar perfil - LoginLogoutTema5</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/commonLoginLogoffTema5.css" rel="stylesheet" type="text/css"/>
        <style>
            form{
                text-align: center;
                padding: 10px 0 20px;
                position: relative;
            }
            fieldset{
                border: none;
            }

            /* Cuando existe fieldset.confirmacionEliminarCuenta, todos los fieldset
            hermanos que no son ese desaparecen.*/
            fieldset.confirmacionEliminarCuenta ~ fieldset:not([class='confirmacionEliminarCuenta']){
                display: none;
            }
            fieldset.confirmacionEliminarCuenta{
                max-width: 450px;
                position: absolute;
                margin: 0 auto;
                left: 0; right: 0;
                top: 25vh;
                background-color: ghostwhite;
                border: 3px solid indigo;
            }
            fieldset.confirmacionEliminarCuenta div{
                font-size: large;
                margin-bottom: 5px;
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
                width: 200px;
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
                color: ghostwhite;
                border: 2px solid ghostwhite;
                background-color: indigo;
            }
            input.button.password:hover{
                color: indigo;
                border: 2px solid indigo;
                background-color: ghostwhite;
            }

            input[type="checkbox"] + label{
                font-size: small;
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
            <button class="volver" form="mainForm" type="submit" name="cancelar" value="Volver">
                <img class="normal" src="../webroot/media/img/left-arrow-indigo.png" alt="volver"><img class="hover" src="../webroot/media/img/left-arrow-teal.png" alt="volver">
            </button>     
            <h1><?php echo $aIdiomaHeader[$_COOKIE['idiomaPreferido']]['editarPerfil'] ?></h1>
        </header>
        <main>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post' id="mainForm" enctype="multipart/form-data">
                <?php
                /**
                 * Si se ha pedido eliminar cuenta, muestra la confirmación de envío del
                 * formulario.
                 */
                if (isset($_REQUEST['confirmacionEliminarCuenta'])) {
                    ?>
                    <fieldset class="confirmacionEliminarCuenta">
                        <div>¿Está seguro de borrar la cuenta?</div>
                        <input class="button" type='submit' name='cancelarEliminarCuenta' value='Cancelar'/>
                        <input class="button" type='submit' name='eliminarCuenta' value='Aceptar'/>
                    </fieldset>
                    <?php
                }
                ?>
                <fieldset>
                    <ul>
                        <li><label for='usuario' >Nombre de usuario</label></li>
                        <li><input type='text' name='usuario' id='usuario' value="<?php echo $aFormulario['usuario'] ?>" disabled/></li>
                    </ul>
                    <ul>
                        <li><label class="obligatorio" for='descripcion' >Nombre y apellidos</label></li>
                        <li><input class="obligatorio" type='text' name='descripcion' id='descripcion' value="<?php echo $aFormulario['descripcion'] ?? '' ?>"/></li>
                        <li class="error"><?php echo $aErrores['descripcion'] ?></li>
                    </ul>
                    <ul>
                        <li><label for='numConexiones'>Número de conexiones</label></li>
                        <li><input type='text' name='numConexiones' id='numConexiones' value="<?php echo $aFormulario['numConexiones'] ?>" disabled/></li>
                    </ul>
                    <ul>
                        <li><label for='fechaHoraUltimaConexion'>Fecha-hora de última conexión</label></li>
                        <li><input type='text' name='fechaHoraUltimaConexion' id='fechaHoraUltimaConexion' value="<?php echo $aFormulario['fechaHoraUltimaConexion'] ?>" disabled/></li>
                    </ul>
                    <ul>
                        <li><label for='perfil'>Perfil de usuario</label></li>
                        <li><input type='text' name='perfil' id='perfil' value="<?php echo $aFormulario['perfil'] ?>" disabled/></li>
                    </ul>
                    <ul>
                        <li><label for='imagenUsuario' >Imagen de usuario</label></li>
                        <?php
                        // Si el usuario tiene imagen de usuario, la muestra.
                        if ($aFormulario['imagenUsuario']) {
                            ?><li>
                                <img src="data:image/jpg;base64, <?php echo $aFormulario['imagenUsuario'] ?>" width="300px" alt="imagen de usuario">
                            </li>
                            <li>
                                <input type="checkbox" name="eliminarImagenUsuario" id="eliminarImagenUsuario" onclick="ocultarSubidaImagen(this)">
                                <label for="eliminarImagenUsuario">¿Eliminar imagen de usuario?</label>
                            </li>
                            <?php }
                        ?>
                        <li><input type='file' name='imagenUsuario' id='imagenUsuario' accept=".jpg,.jpeg,.png"/></li>
                        <li class="error"><?php echo $aErrores['imagenUsuario'] ?></li>
                    </ul>
                    <ul>
                        <li>Contraseña</li>
                        <li><input class="button password" type='submit' name='cambiarPassword' value='Cambiar contraseña'/></li>
                        <li class="info">
                        <?php
                        if (isset($_REQUEST['passwordCambiada'])) {
                            if ($_REQUEST['passwordCambiada'] == 'yes') {
                                echo 'La contraseña se ha cambiado.';
                            } else if ($_REQUEST['passwordCambiada'] == 'no') {
                                echo 'La contraseña no se ha cambiado.';
                            }
                        }
                        ?>
                        </li>
                    </ul>
                </fieldset>
                <fieldset>
                    <input class="button" type='submit' name='cancelar' value='Cancelar'/>
                    <input class="button" type='submit' name='editarPerfil' value='Efectuar cambios'/>
                    <input class="button" type='submit' name='confirmacionEliminarCuenta' value='Eliminar cuenta'/>
                </fieldset>
            </form>
        </main>
<?php include_once './elementoFooter.php'; //Footer          ?>
        <script>
            function ocultarSubidaImagen(checkbox) {
                if (checkbox.checked) {
                    document.getElementById('imagenUsuario').style.display = 'none';
                } else {
                    document.getElementById('imagenUsuario').style.display = 'initial';
                }
            }
        </script>
    </body>
</html>