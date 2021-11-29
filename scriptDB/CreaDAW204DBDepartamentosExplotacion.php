<!DOCTYPE html>
<!--
    Autor: Isabel Martínez Guerra
    Fecha: 29/11/2021
-->
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>IMG - LoginLogoffTema5 - Creación DB</title>
    </head>
    <body>
        <h1>Script de creación de tablas</h1>
        <?php
        /**
         * @author Isabel Martínez Guerra
         * @since 28/11/2021
         * 
         * Fichero de creación de las tablas del proyecto Login Logoff Tema 5.
         */
        
        require_once '../config/configDB.php'; // Fichero de configuración de la base de datos.

        /**
         * Creación de las tablas.
         */
        $sInstrucciones = <<<QUERY
            /* Uso de la base de datos */
            USE dbs4868794;

            /* Creación de las tablas */
            CREATE TABLE IF NOT EXISTS T01_Usuario(
                T01_CodUsuario VARCHAR(8) PRIMARY KEY,
                T01_Password VARCHAR(64) NOT NULL, -- 64 porque el largo máximo es de 8 caracteres, más su codificación en SHA2.
                T01_DescUsuario VARCHAR(255) NOT NULL, -- Contiene nombre y apellidos del usuario.
                T01_FechaHoraUltimaConexion DATETIME NOT NULL DEFAULT NOW(),
                T01_NumConexiones INT DEFAULT 1 NOT NULL,
                T01_Perfil ENUM('administrador', 'usuario') DEFAULT 'usuario',
                T01_ImagenUsuario MEDIUMBLOB NULL
            ) ENGINE=INNODB;

            CREATE TABLE IF NOT EXISTS T02_Departamento(
                T02_CodDepartamento VARCHAR(3) PRIMARY KEY,
                T02_DescDepartamento VARCHAR(255) NOT NULL,
                T02_FechaCreacionDepartamento DATETIME NOT NULL DEFAULT NOW(),
                T02_VolumenDeNegocio FLOAT NOT NULL,
                T02_FechaBajaDepartamento DATETIME NULL
            ) ENGINE=INNODB;
        QUERY;

        try {
            // Conexión con la base de datos.
            $oDB = new PDO(HOST, USER, PASSWORD);

            // Mostrado de las excepciones.
            $oDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ejecución de la creación de las tablas.
            $oDB->exec($sInstrucciones);
            
            echo '<div>Query realizado.</div>';
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
