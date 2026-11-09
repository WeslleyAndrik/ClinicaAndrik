@echo off
chcp 65001 > nul
echo ==================================================
echo    CLÍNICA ANDRIK - INICIALIZAÇÃO DO SISTEMA
echo ==================================================

if not exist .env (
    copy .env.example .env > nul
)

if not exist database\database.sqlite (
    type nul > database\database.sqlite
)

if exist "%LOCALAPPDATA%\Programs\php\php.exe" (
    set "PATH=%LOCALAPPDATA%\Programs\php;%PATH%"
)

where php > nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo Preparando banco de dados local SQLite...
    php artisan migrate:fresh --seed --force
    echo.
    echo Servidor iniciado em: http://localhost:8000
    echo Credenciais padrao: admin@clinicaandrik.com.br / password
    echo ==================================================
    php artisan serve --host=127.0.0.1 --port=8000
) else (
    echo PHP local nao encontrado no PATH. Tentando iniciar via Docker...
    docker-compose up -d --build
    timeout /t 10 /nobreak > nul
    docker-compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader
    docker-compose exec -T app php artisan key:generate --force
    docker-compose exec -T app php artisan migrate:fresh --seed --force
    echo Sistema disponivel em: http://localhost:8000
    pause
)
