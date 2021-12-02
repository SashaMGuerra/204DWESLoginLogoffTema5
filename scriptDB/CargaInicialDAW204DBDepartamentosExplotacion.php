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
            INSERT INTO T01_Usuario(T01_CodUsuario, T01_Password, T01_DescUsuario) VALUES
                ('albertoF',SHA2('albertoFpaso',256),'AlbertoF'),
                ('outmane',SHA2('outmanepaso',256),'Outmane'),
                ('rodrigo',SHA2('rodrigopaso',256),'Rodrigo'),
                ('isabel',SHA2('isabelpaso',256),'Isabel'),
                ('david',SHA2('davidpaso',256),'David'),
                ('aroa',SHA2('aroapaso',256),'Aroa'),
                ('johanna',SHA2('johannapaso',256),'Johanna'),
                ('oscar',SHA2('oscarpaso',256),'Oscar'),
                ('sonia',SHA2('soniapaso',256),'Sonia'),
                ('heraclio',SHA2('heracliopaso',256),'Heraclio'),
                ('amor',SHA2('amorpaso',256),'Amor'),
                ('antonio',SHA2('antoniopaso',256),'Antonio'),
                ('albertoB',SHA2('albertoBpaso',256),'AlbertoB');

            -- Inserción de usuarios administradores.
            INSERT INTO T01_Usuario(T01_CodUsuario, T01_Password, T01_DescUsuario, T01_Perfil) VALUES
                ('admin',SHA2('adminpaso',256),'Admin','administrador');

            INSERT INTO T02_Departamento (T02_CodDepartamento, T02_DescDepartamento, T02_VolumenDeNegocio) VALUES
                ('INF','Departamento de Informatica',1.5),
                ('BIO','Departamento de Biologia',2.5),
                ('ING','Departamento de Inglés',3.5),
                ('LEN','Departamento de Lengua',4.5),
                ('MUS','Departamento de Musica',1.5);
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