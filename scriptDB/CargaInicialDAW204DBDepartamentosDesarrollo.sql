/**
 * Author:  Isabel Martínez Guerra
 * Fecha de creación: 28/11/2021

 * Script de carga inicial en tablas.
 */

/* Inserción en tablas */
USE DB204DWESLoginLogoffTema5;

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
    ('admin','paso','Admin','administrador');

INSERT INTO T02_Departamento (T02_CodDepartamento, T02_DescDepartamento, T02_VolumenDeNegocio) VALUES
    ('INF','Departamento de Informatica',1.5),
    ('BIO','Departamento de Biologia',2.5),
    ('ING','Departamento de Inglés',3.5),
    ('LEN','Departamento de Lengua',4.5),
    ('MUS','Departamento de Musica',1.5);