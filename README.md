# Catedral Cristiana — Plataforma de Discipulado

Aplicación web de discipulado y gestión ministerial construida con **Laravel 11** (`PHP ^8.2`, `MySQL`/`MariaDB`).

Incluye: login con recuperación de contraseña, Google OAuth, miembros, grupos de conexión, relacionamiento de discipulado, informes, calendario, oración, biblioteca de libros (PDF), reportes y configuración de la iglesia (logo, favicon, fondo del login).

---

## Publicación en un Shared Hosting **sin acceso a terminal** (vía FTP)

Esta guía está pensada para hostings compartidos **básicos** donde **no hay SSH ni terminal** (ni siquiera el Terminal de cPanel). En ese escenario usaremos solo herramientas que todos los hostings ofrecen:

- **FTP / FTPS / SFTP** (FileZilla, WinSCP, o el *File Manager* de cPanel).
- **phpMyAdmin** (para la base de datos).
- **Soportes** como *Zip/Unzip* del File Manager.
- Opcionalmente, **chromeless web installer** que incluimos al final (apéndice A) para ejecutar los comandos de Laravel desde el navegador.

> 💡 **La regla de oro del FTP-only:** todo lo que requiere ejecutar `php artisan ...` **se hace en tu PC de antemano**, y al servidor solo se suben archivos ya preparados.

### 1. Requisitos del hosting

- **PHP 8.2 o superior** (recomendado 8.3/8.4 en el *Select PHP Version* de cPanel).
- Extensiones PHP activas:
  `mbstring`, `openssl`, `pdo_mysql`, `mysqli`, `gd` o `imagick`, `fileinfo`, `curl`, `zip`, `intl`, `ctype`, `json`, `tokenizer`, `xml`, `bcmath`.
- **MySQL 5.7+ o MariaDB 10+** con **phpMyAdmin**.
- **Certificado SSL** (Let's Encrypt desde cPanel).
- **Node.js** solo en tu PC para compilar los assets.

### 2. Preparar todo localmente (en tu PC)

Todo lo que consume un comando se hace acá:

```bash
# 1) Dependencias PHP (genera la carpeta vendor/ que subiremos)
composer install --no-dev --optimize-autoloader

# 2) Assets del frontend (genera public/build)
npm install
npm run build

# 3) Clave de aplicación: genera una APP_KEY para copiar al .env del servidor
php artisan key:generate --show
```

`php artisan key:generate --show` imprime algo como `base64:AbC1...==`. **Guarda ese texto**: va en el `.env` del servidor (paso 4). No hace falta correrlo allá.

> **Importante (vendor):** la carpeta `vendor/` se compila para la versión de PHP que la genera. Verificá con `php -v` que tu PHP local tenga la **misma versión mayor.menor** que la del hosting (p. ej. 8.3 → 8.3). Si difiere mucho, instalá las dependencias con la misma versión (p. ej. `brew`/`XAMPP` con PHP 8.3) antes de subir.

### 3. Base de datos: generarla localmente y subirla como dump

Sin terminal no podés correr `php artisan migrate`. La solución es **armar la base en tu PC y copiar el SQL**.

1. Creá una base local limpia, p. ej. `catedral_prod`, y apuntá tu `.env` local:
   ```env
   DB_CONNECTION=mysql
   DB_DATABASE=catedral_prod
   DB_USERNAME=root
   DB_PASSWORD=
   ```
2. Ejecutá migraciones + seeders **localmente**:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```
   Esto crea permisos, roles, la configuración de la iglesia y el **admin** `admin@catedralcristiana.com` / `Admin123!`.
3. (Opcional) Si querés dejar los libros de la biblioteca ya en la base, antes del dump corré:
   ```bash
   php artisan db:seed --class=BibliotecaSeeder
   ```
   (Descarga los PDFs desde internet; los archivos igual los subís por FTP — paso 7.)
4. Exportá la base a un `.sql`:
   ```bash
   mysqldump -u root catedral_prod > catedral.sql
   ```
   Si no tenés `mysqldump`, exportala desde **phpMyAdmin** (en tu máquina local o a través de Laravel Herd): *Exportar → SQL → Descargar*.
5. En el hosting (paso 5) esa `catedral.sql` se importa con **Import** de phpMyAdmin.

> ⚠️ Verificá antes de exportar que el dump tenga `utf8mb4` / `utf8mb4_unicode_ci` (es el estándar de Laravel).
> ⚠️ **Cambiá la contraseña del admin en el primer login.** El sistema solo muestra "Configurar administrador" si la tabla de usuarios está vacía.

### 4. Crear el `.env` del servidor

Preparalo en tu PC usando como base el `.env.example` y completá los valores de producción. El **`APP_KEY` debe ser el que generaste en el paso 2**:

```env
APP_NAME="Catedral Cristiana"
APP_ENV=production
APP_KEY=base64:AbC1...==        # ← el que generaste localmente (paso 2)
APP_DEBUG=false
APP_URL=https://www.tudominio.com

APP_LOCALE=es
LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usuario_catedral     # la que creés en el hosting (paso 5)
DB_USERNAME=usuario
DB_PASSWORD=TU_CLAVE_DB

SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mail.tudominio.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@tudominio.com
MAIL_PASSWORD=TU_CLAVE_MAIL
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://www.tudominio.com/auth/google/callback

IMGBB_API_KEY=

INSTALL_TOKEN=               # opcional, solo para el instalador web (apéndice A)
```

No hay forma de correr `key:generate` en el hosting sin terminal; por eso el `APP_KEY` se calcula en tu PC.

### 5. Crear base de datos y usuario en el hosting

1. cPanel (**MySQL® Databases**): creá la base (p. ej. `usuario_catedral`) y un usuario con **ALL PRIVILEGES**.
2. phpMyAdmin: seleccioná la base y usá **Import** para subir el `catedral.sql` del paso 3 (opción *Compatible mode*, marca *partial import* si el archivo es grande y el hosting corta).
3. Anotá el nombre real (cPanel lo prefija: `usuario_catedral`), el usuario y la clave → van en el `.env` del paso 4.

> No importes el SQL **antes** de tener la base creada; seleccioná la base correcta en la pestaña *Import*.

### 6. Subir los archivos por FTP

Con **FileZilla** (o el *File Manager* de cPanel) subí **todo el contenido del proyecto** a `public_html/`:

```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/            ← el Document Root se apuntará a esta carpeta (paso 8)
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/            ← la que compilaste en tu PC (paso 2)
├── .env               ← el que armaste en el paso 4
├── .htaccess
├── artisan
└── composer.json
```

**Qué subir / qué NO:**

| Subí ✅ | NO subas ❌ |
|---|---|
| `vendor/` local ya instalado | `node_modules/` |
| `public/build/` (de `npm run build`) | tu `.env` de desarrollo |
| `public/.htaccess` | `storage/framework/cache/*`, `sessions/*`, `views/*` (se regeneran) |
| `.env` de producción (paso 4) | `storage/logs/*` locales |
| `catedral.sql` (temporal, lo borrás después) | archivos de test/`tests/` si querés ahorrar |

**Consejo para muchos archivos:** subí un `.zip` completo (sin `node_modules`, sin `.env` de desarrollo) y descomprimilo con el *File Manager → Extract* de cPanel. Es mucho más rápido y confiable que subir archivo por archivo.

### 7. Permisos y archivos de la biblioteca

**Permisos (con FTP):** en FileZilla clic derecho sobre la carpeta → **File permissions (chmod)**:

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
# y dentro de storage/app/public:
chmod -R 775 storage/app/public
```

Si tu FTP no permite chmod, usá el **File Manager de cPanel → Permisos** (sed 755/775/777).

**Biblioteca de libros:** los PDFs deben estar físicamente en `storage/app/public/biblioteca/`. Si el paso 3 no incluyó los libros, **subí por FTP** la carpeta `storage/app/public/biblioteca` con todos los PDFs (suelen ser `uuid.pdf`). Los registros de `biblioteca_items` ya contienen su `file_path`.

**storage:link:** la app guarda el logo/favicon/fondo como *data URI* en la base y los libros se descargan a través de una **ruta protegida** (`/biblioteca/{material}/descargar`), por lo que el enlace simbólico de `public/storage` **no es necesario** para que funcione.

### 8. Apuntar el "Document Root" a `public/`

Por seguridad Laravel solo expone la carpeta `public/`:

#### Opción A (recomendada — cPanel)
1. *Domains* → tu dominio → **Manage**.
2. En **Document Root**, cambiá `public_html` por `public_html/public`.
3. Guardá. El `public/.htaccess` que ya trae Laravel se encarga del resto.

#### Opción B (si no podés cambiar el Document Root)
Creá un `.htaccess` en la raíz (`public_html/.htaccess`):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

> ⚠️ En la Opción B los archivos de Laravel quedan visibles en el webroot. Es menos seguro: usala solo si el hosting no permite cambiar el Document Root.

### 9. HTTPS / SSL

1. Activá el certificado en cPanel (**SSL/TLS Status → Run AutoSSL** o **Let's Encrypt**).
2. `APP_URL` debe quedar con `https://`.
3. Forzá la redirección `http → https` en `public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 10. Tareas programadas, colas y caché (sin terminal)

- **Colas:** los mails (recuperación de contraseña) se envían **sincrónicamente** → **no se necesita worker**.
- **Cron:** no hay tareas programadas importantes en la app. Si querés activar `schedule:run`, deberás pedirle al soporte del hosting un **Cron Job** de una línea:
  ```cron
  * * * * * cd /home/usuario/public_html && php artisan schedule:run >> /dev/null 2>&1
  ```
- **Caché de configuración/rutas/vistas:** es una optimización, **no obligatoria**. La app funciona sin terminal igual, solo un poco más lenta. Opciones:
  - Pedir una vez al soporte: `php artisan config:cache` (y `route:cache`, `view:cache`).
  - O usar el **instalador web** del apéndice A con el comando `optimize`.

### 11. Google OAuth (opcional)

En la [Google Cloud Console](https://console.cloud.google.com/) creá las credenciales OAuth y ponelas en `.env`:

- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI` = `https://www.tudominio.com/auth/google/callback`
- *Authorized JavaScript origins*: `https://www.tudominio.com`
- *Authorized redirect URIs*: `https://www.tudominio.com/auth/google/callback`

### 12. Checklist post-publicación

1. [ ] `https://www.tudominio.com/login` carga el login.
2. [ ] Iniciás sesión con `admin@catedralcristiana.com` / `Admin123!` y **cambiás la contraseña**.
3. [ ] **Configuración → Ajustes**: nombre, logo, favicon y fondo del login (panel izquierdo en desktop / detrás del login en mobile).
4. [ ] Probás subir una imagen (se guardan como base64 en la BD; no subas gigantes, comprimí a ~1280px).
5. [ ] **"¿Olvidaste tu contraseña?"** funciona (requiere SMTP).
6. [ ] Descargás un libro de la biblioteca (permiso `Biblioteca.view`).
7. [ ] Login con **Google**.
8. [ ] Revisás `storage/logs/laravel.log` ante errores (descargándolo por FTP).

### 13. Solución de problemas comunes

| Problema | Causa probable | Solución |
|---|---|---|
| Error 500 en todo | Permisos o `APP_KEY` incorrecto | Chmod a `storage`/`bootstrap/cache` (paso 7); `APP_KEY` debe ser `base64:...` válido |
| *"Unable to locate file in Vite manifest"* | Falta `public/build` | Subir `public/build` (ejecutar `npm run build` en tu PC) |
| Página en blanco / 403 | Document Root mal apuntado | Apuntarlo a `public/` (paso 8) |
| Error de conexión a la BD | `.env` con credenciales reales del hosting | Revisar `DB_*` contra phpMyAdmin y el prefijo `usuario_` |
| `MissingAppKeyException` | `APP_KEY` no generado | Generarlo local y pegarlo (paso 2) |
| No se guardan imágenes grandes | Límites de upload del hosting | Aumentarlos en **MultiPHP INI Editor** (cPanel) o `php.ini` del hosting: `upload_max_filesize` y `post_max_size` |
| Login con Google falla | Redirect URI distinta | Ver paso 11; debe coincidir exactamente |
| El mail de recuperación no llega | SMTP del `.env` o puerto bloqueado | Probar 587/TLS o 465/SSL según el hosting |

### 14. Apéndice A — Instalador web sin terminal (opcional)

Si no querés importar el SQL por phpMyAdmin (paso 3) y preferís que Laravel ejecute `migrate`/`db:seed` desde el navegador, usá este script. **Es una puerta de entrada: borralo apenas termines.**

1. Agregá al `.env` del servidor una variable secreta, p. ej.:
   ```env
   INSTALL_TOKEN=una-clave-larga-y-secreta
   ```
2. Creá este archivo en tu PC como `public/instalar.php`:

   ```php
   <?php
   // instalar.php — ejecuta comandos de Artisan sin terminal. ¡BORRAR después de usarlo!
   use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
   use Illuminate\Support\Facades\Artisan;

   require __DIR__.'/../vendor/autoload.php';

   $app = require __DIR__.'/../bootstrap/app.php';
   $app->make(ConsoleKernel::class)->bootstrap();

   $secret = env('INSTALL_TOKEN', '');

   if ($secret === '' || ! isset($_GET['token']) || ! hash_equals($secret, $_GET['token'])) {
       http_response_code(403);
       exit('Acceso denegado');
   }

   function run(string $cmd): void
   {
       echo "<pre style='background:#111;color:#0f0;padding:10px'>$ php artisan {$cmd}</pre>";
       Artisan::call($cmd);
       echo '<pre>'.htmlspecialchars(Artisan::output()).'</pre>';
       flush();
   }

   set_time_limit(300);

   run('migrate --force');
   run('db:seed --force');
   run('storage:link');
   echo '<p><strong>Instalación finalizada. ¡BORRÁ instalar.php y quita INSTALL_TOKEN del .env!</strong></p>';
   ```

3. Subilo por FTP a `public_html/public/instalar.php` y visitá:
   ```
   https://www.tudominio.com/instalar.php?token=una-clave-larga-y-secreta
   ```
4. Esperá que termine (puede tardar).
5. **Inmediatamente: borrá `instalar.php` por FTP** y sacá `INSTALL_TOKEN` del `.env`.

> En este caso la base `usuario_catedral` debe existir en el hosting pero **vacía** (creada en el paso 5, sin importar SQL).
> El script también deja el `storage:link` creado por si alguna vez lo necesitás.

### 15. Apéndice B — Actualizaciones futuras (sin terminal)

Para una actualización futura del código repetí el ciclo FTP:

```bash
npm run build                      # 1) recompilar assets localmente
# 2) subir por FTP solo lo que cambió:
#    - app/, config/, database/, resources/, routes/ (si tocaste código)
#    - public/build/ (si tocaste frontend)
#    - vendor/ (si tocaron dependencias)
# 3) si hay migraciones nuevas, volvé a exportar el SQL local (paso 3) e importarlo, o:
#    - reusá instalar.php (apéndice A) cambiando el paso 2 por: run('migrate --force');
```

---

## Desarrollo local

Requisitos: PHP 8.2+, Composer, Node y un servidor local (Laravel Herd, Valet, Sail, XAMPP…).

```bash
composer install
npm install
copy .env.example .env     # configurar la BD
php artisan key:generate
php artisan migrate --seed
npm run build              # o: npm run dev
```

---

## Tests

```bash
php artisan test
```