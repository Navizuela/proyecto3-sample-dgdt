@echo off
title Servidor Zend MVC - Proyecto de Aprendizaje DGDT
cls
echo ================================================================
echo    SISTEMA DE GESTION DE CORREOS - PROYECTO DE APRENDIZAJE
echo    Framework: Zend Framework 1.12.20 (PHP 7.4.33)
echo ================================================================
echo.
echo Iniciando servidor web en: http://localhost:8080
echo.
echo Presiona Ctrl + C para detener el servidor en cualquier momento.
echo.

start http://localhost:8080/solicitud

C:\php\php.exe -S 0.0.0.0:8080 -t public router.php
pause

