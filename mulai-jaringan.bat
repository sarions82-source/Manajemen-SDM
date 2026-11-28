@echo off
chcp 65001 >nul
rem Menjalankan msdm agar bisa dibuka dari HP atau komputer lain di jaringan yang sama.
call "%~dp0mulai.bat" lan
