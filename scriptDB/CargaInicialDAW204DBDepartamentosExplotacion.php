<!DOCTYPE html>
<!--
    Autor: Isabel Martínez Guerra
    Fecha: 29/11/2021
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>IMG - LoginLogoffTema5 - Carga inicial DB</title>
    </head>
    <body>
        <h1>Script de carga inicial de tablas</h1>
        <?php
        /**
         * @author Isabel Martínez Guerra
         * @since 28/11/2021
         * 
         * Fichero de inserción en las tablas del proyecto Login Logoff Tema 5.
         */
        
        require_once '../config/configDB.php'; // Fichero de configuración de la base de datos.

        $sInstrucciones = <<<QUERY
            /* Inserción en tablas */
            USE dbs4868794;

            -- Inserción de usuarios no administradores.
            INSERT INTO T01_Usuario(T01_CodUsuario, T01_Password, T01_DescUsuario, T01_FechaHoraUltimaConexion) VALUES
                ('albertoF',SHA2('albertoFpaso',256),'AlbertoF', UNIX_TIMESTAMP()),
                ('outmane',SHA2('outmanepaso',256),'Outmane', UNIX_TIMESTAMP()),
                ('rodrigo',SHA2('rodrigopaso',256),'Rodrigo', UNIX_TIMESTAMP()),
                ('isabel',SHA2('isabelpaso',256),'Isabel', UNIX_TIMESTAMP()),
                ('david',SHA2('davidpaso',256),'David', UNIX_TIMESTAMP()),
                ('aroa',SHA2('aroapaso',256),'Aroa', UNIX_TIMESTAMP()),
                ('johanna',SHA2('johannapaso',256),'Johanna', UNIX_TIMESTAMP()),
                ('oscar',SHA2('oscarpaso',256),'Oscar', UNIX_TIMESTAMP()),
                ('sonia',SHA2('soniapaso',256),'Sonia', UNIX_TIMESTAMP()),
                ('heraclio',SHA2('heracliopaso',256),'Heraclio', UNIX_TIMESTAMP()),
                ('amor',SHA2('amorpaso',256),'Amor', UNIX_TIMESTAMP()),
                ('antonio',SHA2('antoniopaso',256),'Antonio', UNIX_TIMESTAMP()),
                ('albertoB',SHA2('albertoBpaso',256),'AlbertoB', UNIX_TIMESTAMP());

            -- Inserción de usuarios administradores.
            INSERT INTO T01_Usuario(T01_CodUsuario, T01_Password, T01_DescUsuario, T01_FechaHoraUltimaConexion, T01_Perfil) VALUES
                ('admin',SHA2('adminpaso',256),'Admin', UNIX_TIMESTAMP(), 'administrador');

            INSERT INTO T02_Departamento (T02_CodDepartamento, T02_DescDepartamento, T02_FechaCreacionDepartamento, T02_VolumenDeNegocio) VALUES
                ('INF','Departamento de Informatica', UNIX_TIMESTAMP(),1.5),
                ('BIO','Departamento de Biologia', UNIX_TIMESTAMP(),2.5),
                ('ING','Departamento de Inglés', UNIX_TIMESTAMP(),3.5),
                ('LEN','Departamento de Lengua', UNIX_TIMESTAMP(),4.5),
                ('MUS','Departamento de Musica', UNIX_TIMESTAMP(),1.5);
        QUERY;

        try {
            // Conexión con la base de datos.
            $oDB = new PDO(HOST, USER, PASSWORD);

            // Mostrado de las excepciones.
            $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ejecución de la inserción del contenido de las tablas.
            $oDB->exec($sInstrucciones);

            echo '<div>Carga inicial de la tabla realizada con éxito.</div>';
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
        ?>
    </body>
</html>