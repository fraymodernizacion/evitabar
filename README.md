# Pase Evita - Web App de Fidelización (PHP + MySQL)

Aplicación mobile-first para clientes de Evita Bar, con módulo cliente y panel staff/admin.

## Stack
- PHP 8+
- MySQL / MariaDB
- HTML + CSS + JS vanilla
- PDO + sesiones seguras + CSRF

## Estructura
- `/public` clientes y assets
- `/admin` panel staff/admin
- `/config` configuración y conexión DB
- `/includes` auth + reglas de negocio + layouts
- `/lib/qrcode` helper QR PHP embebido
- `/lib/scanner` escaneo QR por cámara (BarcodeDetector)
- `/sql/init.sql` esquema y datos demo

## Usuarios demo (todos con contraseña `Evita123!`)
- Cliente nivel 1: `ana@paseevita.local` o DNI `27111222`
- Cliente nivel 2: `beto@paseevita.local` o DNI `30111222`
- Cliente nivel 3: `carla@paseevita.local` o DNI `32111222`
- Admin: `admin@paseevita.local` o DNI `22111000`
- Staff: `staff@paseevita.local` o DNI `25111000`

## Ejecutar local en Mac (rápido con PHP embebido)
1. Crear base e importar SQL:
```bash
mysql -u root -p < sql/init.sql
```

2. Exportar variables de entorno (ajustá usuario/password):
```bash
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_NAME=pase_evita
export DB_USER=root
export DB_PASS=tu_password
export APP_BASE_URL=http://localhost:8000
export APP_TIMEZONE=America/Argentina/Catamarca
```

3. Levantar servidor PHP desde la raíz del proyecto:
```bash
php -S localhost:8000 -t .
```

4. Abrir:
- Cliente: `http://localhost:8000/public/login.php`
- Admin: `http://localhost:8000/admin/login.php`

## Ejecutar con MAMP/XAMPP
1. Copiar el proyecto al `htdocs`.
2. Crear DB `pase_evita` e importar `sql/init.sql` desde phpMyAdmin.
3. Configurar variables de entorno en Apache o editar temporalmente `config/database.php` con credenciales locales.
4. Abrir `/public/login.php` y `/admin/login.php`.

## Deploy en Hostinger
1. Crear base MySQL en Hostinger.
2. Importar `sql/hostinger_import.sql` desde phpMyAdmin. Este archivo ya viene preparado para Hostinger y no incluye `CREATE DATABASE` ni `USE`.
3. Subir todos los archivos por File Manager o FTP.
4. Configurar document root:
- Opción A: raíz del dominio al proyecto completo.
- Opción B: mantener root en `public_html` y subir respetando carpetas `public/` y `admin/`.
5. Definir variables de entorno (`DB_*`, `APP_BASE_URL`, `APP_TIMEZONE`) o ajustar `config/database.php` y `config/app.php`.
6. Probar login cliente/admin y escaneo QR.

## Cómo cambiar niveles y mantenimiento
Editar tabla `settings`:
- `level_2_min`, `level_3_min` para umbrales de ascenso.
- `maintenance_period_months` para período de control.
- `maintain_level_2`, `maintain_level_3` para visitas mínimas por período.
- `maintenance_warning_days` para el aviso anticipado antes de perder el nivel.
- `visit_block_minutes` para bloqueo anti-duplicados.

Ejemplo:
```sql
UPDATE settings SET setting_value='5' WHERE setting_key='level_2_min';
UPDATE settings SET setting_value='9' WHERE setting_key='level_3_min';
```

## Cómo cambiar beneficios
- Desde panel: `/admin/benefits.php` (crear/editar/activar/desactivar/ordenar).
- O por SQL en tabla `benefits`.
- Los beneficios demo están pensados a partir del menú de Eva Bar e incluyen café, cumpleaños, postres, empanadas, sándwiches y una cortesía de vino, con nombres temáticos como `Dulce Federal`, `Empanada Santa Evita`, `Sándwich Cabecita Negra` y `Vino Tinto Nacional`.

## Migración de auditoría de nivel
Si la base ya estaba cargada, corré `sql/migrate_level_events.sql` en phpMyAdmin para crear la tabla `level_events` y activar el aviso anticipado de revisión.

## Nota sobre QR
La generación se resuelve con helper PHP embebido (`lib/qrcode/EmbeddedQr.php`) que usa un endpoint de render para obtener PNG y embebe el resultado como `data:image`.
Si querés QR 100% offline, podés reemplazar esa clase por una librería local completa en `lib/qrcode/`.

## Seguridad básica implementada
- Password hashing: `password_hash` / `password_verify`
- Consultas preparadas (PDO)
- CSRF en formularios POST
- Sesiones con regeneración de ID y timeout por inactividad
- Sanitización y validaciones server-side
