@echo off
echo ====================================================================
echo                SWALAYAN CI4 INSTALLATION WIZARD
echo ====================================================================
echo.

REM Check if PHP is available
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo PHP is not installed or not in your PATH.
    echo Please install PHP 8.1 or higher and make sure it's in your PATH.
    echo Installation cannot continue.
    pause
    exit /b 1
)

REM Check if we're in the right directory
if not exist "composer.json" (
    echo Error: This script must be run from the root directory of the Swalayan CI4 application.
    echo Please navigate to the correct directory and try again.
    pause
    exit /b 1
)

echo Step 1: Installing dependencies and configuring the application...
echo.
php install.php
if %errorlevel% neq 0 (
    echo.
    echo Installation failed during setup phase.
    echo Check for error messages above for more information.
    pause
    exit /b 1
)

echo.
echo Step 2: Checking environment file for syntax errors...
echo.
php check-env.php
if %errorlevel% neq 0 (
    echo.
    echo Failed to verify environment file.
    echo Check for error messages above for more information.
    pause
    exit /b 1
)

echo.
echo Step 3: Setting up the database...
echo.
php setup-database.php
if %errorlevel% neq 0 (
    echo.
    echo Installation failed during database setup.
    echo Check for error messages above for more information.
    pause
    exit /b 1
)

echo.
echo ====================================================================
echo                INSTALLATION COMPLETED SUCCESSFULLY
echo ====================================================================
echo.
echo To start the application, run:
echo php spark serve
echo.
echo Then open your browser and navigate to: http://localhost:8080
echo.
echo INFORMASI LOGIN:
echo 1. PEMILIK (OWNER)
echo    - Email: [email yang Anda masukkan saat setup]
echo    - Password: owner123
echo    - Anda akan menerima kode OTP melalui email untuk verifikasi login
echo.
echo 2. ADMINISTRATOR
echo    - Email: admin@swalayan.com
echo    - Password: admin123
echo.
echo 3. KASIR
echo    - Email: kasir@swalayan.com
echo    - Password: kasir123
echo.
pause
