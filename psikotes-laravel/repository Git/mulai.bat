@echo off
chcp 65001 >nul
title msdm - Psikotes (Laravel)
cd /d "%~dp0"
set HOSTADDR=127.0.0.1
if /i "%~1"=="lan" set HOSTADDR=0.0.0.0
where php >nul 2>nul
if errorlevel 1 (
  echo PHP belum terpasang. Pasang Laragon atau XAMPP dengan PHP 8.2 atau lebih baru, lalu coba lagi.
  echo Lihat PANDUAN-LARAVEL.md untuk langkah lengkapnya.
  pause
  exit /b 1
)
if not exist vendor (
  where composer >nul 2>nul
  if errorlevel 1 (
    echo Composer belum terpasang. Unduh dari https://getcomposer.org lalu jalankan file ini lagi.
    pause
    exit /b 1
  )
  echo Memasang kebutuhan pertama kali, mohon tunggu...
  call composer install --no-dev --optimize-autoloader
  if errorlevel 1 (
    echo Pemasangan gagal. Periksa koneksi internet lalu coba lagi.
    pause
    exit /b 1
  )
)
if not exist .env copy .env.example .env >nul
start "" /b cmd /c "ping -n 5 127.0.0.1 >nul & start http://localhost:8000"
php artisan serve --host=%HOSTADDR% --port=8000
echo.
echo Server berhenti. Tekan tombol apa saja untuk menutup.
pause >nul
