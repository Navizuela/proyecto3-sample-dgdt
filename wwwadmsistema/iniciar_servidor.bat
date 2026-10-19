@echo off
chcp 65001 > nul
title Servidor Aprendizaje Zend MVC - DGDT

echo ========================================================
echo    INICIANDO SERVIDOR LOCAL (ZEND FRAMEWORK 1.x)
echo ========================================================
echo.
echo Directorio: %~dp0
echo Servidor:   http://127.0.0.1:8080
echo.
echo Enlaces directos:
echo  1. Solicitudes: http://127.0.0.1:8080/correo-institucional/solicitud
echo  2. Autorizar:   http://127.0.0.1:8080/correo-institucional/autorizar
echo.
echo [INFO] Abriendo el navegador automáticamente...
echo [INFO] Para detener el servidor, presiona Ctrl + C en esta ventana.
echo ========================================================
echo.

:: Abre el navegador automáticamente a los 2 segundos
start "" cmd /c "timeout /t 2 >nul & start http://127.0.0.1:8080/correo-institucional/solicitud"

:: Verifica ruta de PHP (prioriza C:\php\php.exe y fallback al PATH global)
if exist "C:\php\php.exe" (
    "C:\php\php.exe" -S 127.0.0.1:8080 -t "%~dp0public" "%~dp0router.php"
) else (
    php -S 127.0.0.1:8080 -t "%~dp0public" "%~dp0router.php"
)

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] No se pudo iniciar el servidor.
    echo Verifica si el puerto 8080 ya está en uso o si PHP está instalado.
    pause
)

