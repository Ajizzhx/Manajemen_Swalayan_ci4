@echo off
echo Initializing Swalayan CI4 Database...
echo.

rem Check if database exists already
mysql -u root -h localhost -e "USE swalayan_db;" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Database swalayan_db sudah ada.
    
    choice /c YN /m "Apakah Anda ingin menjalankan ulang setup database (akan menimpa data yang ada)?"
    if ERRORLEVEL 2 (
        echo Setup dibatalkan.
        goto skip_db_setup
    )
)

rem Run the direct PHP setup script first
php setup_database.php
echo.

:skip_db_setup
rem Run the CI4 migrations
echo Menjalankan migrasi database...
php spark migrate

rem Run the seeds
echo Menjalankan seeder...
echo Menjalankan KaryawanSeeder...
php spark db:seed KaryawanSeeder

echo Menjalankan InitialDataSeeder...
php spark db:seed InitialDataSeeder

echo.
echo Setup complete! You can now start using the application.
echo.
echo Default login credentials:
echo.
echo   Admin:
echo     Email: admin@swalayan.com
echo     Password: admin123
echo.
echo   Owner:
echo     Email: [email yang dimasukkan saat setup]
echo     Password: owner123
echo.
echo   Kasir:
echo     Email: kasir@swalayan.com
echo     Password: kasir123

pause
