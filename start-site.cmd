@echo off
rem تشغيل موقع المستوصف محليًا ثم فتحه في المتصفح
start "clinic-php-server" /min "%~dp0tools\php\php.exe" -S localhost:8080 -t "%~dp0site" "%~dp0router.php"
timeout /t 2 /nobreak >nul
start http://localhost:8080/
echo الموقع يعمل على http://localhost:8080 — لوحة التحكم: http://localhost:8080/wp-admin
