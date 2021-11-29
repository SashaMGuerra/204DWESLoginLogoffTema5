/**
 * Author:  Isabel Martínez Guerra
 * Fecha de creación: 28/11/2021

 * Script de eliminación de la base de datos y usuario.
 */

/* Eliminación de la base de datos */
DROP DATABASE IF EXISTS DB204DWESLoginLogoffTema5;

/* Eliminación del usuario */
DROP USER IF EXISTS 'User204DWESLoginLogoffTema5'@'%';
