@echo off

rem CORE CPU
set NAME=sabot2
php -f ..\scripts\preprocess.php %NAME%.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
..\scripts\macro11 -ysl 32 -yus -m ..\scripts\sysmac.sml -l _%NAME%.lst _%NAME%.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f ..\scripts\lst2bin.php _%NAME%.lst _%NAME%.bin bin 1000
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f scripts\makeovls.php _%NAME%.bin
if %ERRORLEVEL% NEQ 0 ( exit /b )
rem --- clean ---
del _%NAME%.mac
del _%NAME%.bin
rem del _%NAME%.lst
move /y _%NAME%.bin.ov0 release\sabot2.bin >NUL
move /y _%NAME%.bin.ov1 release\sabot2.ov1 >NUL
move /y _%NAME%.bin.ov2 release\sabot2.ov2 >NUL

rem -- put to disk --
..\scripts\bkdecmd d ./release/andos.img sabot2 >NUL
..\scripts\bkdecmd d ./release/andos.img sabot2.ov1 >NUL
..\scripts\bkdecmd d ./release/andos.img sabot2.ov2 >NUL
..\scripts\bkdecmd d ./release/andos.img sabot2.ov6 >NUL
..\scripts\bkdecmd a ./release/andos.img ./release/sabot2.bin >NUL
..\scripts\bkdecmd a ./release/andos.img ./release/sabot2.ov1 >NUL
..\scripts\bkdecmd a ./release/andos.img ./release/sabot2.ov2 >NUL
..\scripts\bkdecmd a ./release/andos.img ./release/sabot2.ov6 >NUL

rem -- run bkemu --
echo.
start ..\..\bkemu\BK_x64.exe /C BK-0011M_FDD