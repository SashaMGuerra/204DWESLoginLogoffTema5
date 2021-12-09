/**
 * Author:  Isabel Martínez Guerra
 * Fecha de creación: 28/11/2021

 * Script de carga inicial en tablas.
 */

/* Inserción en tablas */
USE DB204DWESLoginLogoffTema5;

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