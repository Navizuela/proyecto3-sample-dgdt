@echo off
chcp 65001 > nul
cd /d "%~dp0wwwadmsistema"
call "%~dp0wwwadmsistema\iniciar_servidor.bat"

