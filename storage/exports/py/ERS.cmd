@echo off
echo %DATE% %TIME%
echo *******************************************************************************
echo ********************** Actualizando aplicativo Movil ***********************
echo *******************************************************************************

CD C:\laragon\www\web\storage\exports\py
python exec.py
pause