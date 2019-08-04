@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/bin/console
php -d memory_limit=-1 "%BIN_TARGET%" %*