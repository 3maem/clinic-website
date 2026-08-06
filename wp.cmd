@echo off
set PHP=%~dp0tools\php\php.exe
"%PHP%" "%~dp0tools\wp-cli\wp-cli.phar" --path="%~dp0site" %*
