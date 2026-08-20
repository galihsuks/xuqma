# Galih Base App - CodeIgniter Backend

Backend ini adalah fondasi API dan public web untuk `galih-base-app`.

Project CodeIgniter ini menangani:
- API untuk admin app React di `/api/*`
- halaman public yang SEO-friendly seperti `/`, `/about`, dan `/articles`
- autentikasi web login + session CI4 + JWT cookie untuk app React
- role, menu, menu control, role access control
- logging backend dan logging frontend

## Stack

- CodeIgniter 4
- PHP 8.1+
- MySQL / MariaDB
- `firebase/php-jwt`

## Arsitektur Singkat

Project ini sengaja dipisah per tanggung jawab:

- `CodeIgniter`
  untuk backend API dan public pages yang SEO-friendly
- `React`
  untuk admin panel yang stateful dan interaktif

Konsep route utamanya:

- public pages:
  - `/`
  - `/about`
  - `/articles`
  - `/articles/{slug}`
- admin API:
  - `/api/auth/*`
  - `/api/access/*`
  - `/api/user/*`
  - `/api/role/*`
  - `/api/parameter/*`
  - `/api/menu/*`
  - `/api/menu-control/*`
  - `/api/role-menu-control/*`
  - `/api/log/*`

## Fitur Utama Backend

- login web di `/login`
- logout API untuk app React
- auth filter dengan cookie JWT atau bearer token
- response helper terpusat di `BaseController`
- validation message yang human-friendly dan berbahasa Inggris
- signature check untuk API unprotected tertentu
- app support parameter via table `app_support`
- log API untuk FE dan BE
- menu access per role
- access control per menu berdasarkan code:
  - `C` = Create
  - `R` = Read
  - `U` = Update
  - `D` = Delete
  - `AC` = Access Control

## Struktur Data Penting

Tabel utama yang dipakai:

- `app_users`
- `app_roles`
- `app_user_roles`
- `app_menus`
- `app_menu_controls`
- `app_role_menu_controls`
- `app_logs`
- `app_support`
- `app_tokens`

## Setup

## 1. Install dependency

```bash
composer install
```

## 2. Siapkan environment

Pastikan file `.env` sudah sesuai.

Key yang penting untuk project ini:

```env
CI_ENVIRONMENT = development
app.name = 'Base App Galih'
app.baseURL = 'http://localhost:8080'

database.default.hostname = localhost
database.default.database = base_app_galih
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

JWT_SECRET=change-this-secret
API_SECRET=change-this-secret
BASE_URL_FRONTEND=http://localhost:5173
ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000,http://localhost:3003
```

Catatan:
- ganti `JWT_SECRET` dan `API_SECRET` untuk environment asli
- `ALLOWED_ORIGINS` dipakai oleh filter CORS

## 3. Jalankan migration

```bash
php spark migrate
```

## Seeder

Seeder yang tersedia:

- `RoleSeeder`
  membuat role default:
  - `SA` = Super Admin
  - `A` = Admin
  - `U` = User
  - `V` = Viewer
- `BaseAppMenuSeeder`
  membuat menu dasar yang dipakai admin React
- `SuperAdminSeeder`
  membuat akun super admin + assign role + grant seluruh access control menu
- `BaseAppSeeder`
  menjalankan semuanya sekaligus

## Jalankan seeder lengkap

```bash
php spark db:seed BaseAppSeeder
```

## Jalankan seeder satu per satu

```bash
php spark db:seed RoleSeeder
php spark db:seed BaseAppMenuSeeder
php spark db:seed SuperAdminSeeder
```

## Default Super Admin

Seeder `SuperAdminSeeder` membuat akun berikut:

- username: `superadmin`
- email: `superadmin@local.test`
- password: `SuperAdmin123!`

## Menjalankan Server

```bash
php spark serve
```

Default:

- backend/public site:
  [http://localhost:8080](http://localhost:8080)

## Public Pages

Halaman public yang sudah disiapkan:

- `/`
- `/about`
- `/articles`
- `/articles/{slug}`

Tujuan halaman ini adalah:
- SEO-friendly
- server-rendered
- cocok untuk company profile, landing page, article, dan content page

## API Ringkas

## Auth

- `POST /api/auth/logout`
- `GET /api/auth/me`

## Access

- `GET /api/access/menu`
- `GET /api/access/control/{menu_id}`

## Dropdown

- `GET /api/dropdown/role?keywords=...`
- `GET /api/dropdown/user?keywords=...`

## Master Data

- `GET|POST /api/user`
- `GET|PUT|DELETE /api/user/{id}`

- `GET|POST /api/role`
- `GET|PUT|DELETE /api/role/{id}`

- `GET|POST /api/parameter`
- `GET|PUT|DELETE /api/parameter/{id}`

- `GET|POST /api/menu`
- `GET|PUT|DELETE /api/menu/{id}`

- `GET /api/menu-control/{menu_id}`
- `POST /api/menu-control`
- `PUT|DELETE /api/menu-control/{id}`

- `GET /api/role-menu-control/{role_id}`
- `POST /api/role-menu-control`

## Logs

- `POST /api/log`
- `GET /api/log`
- `DELETE /api/log`

## Catatan Access Control

Alur access control yang dipakai admin React:

1. user login via halaman CodeIgniter `/login`
2. backend membuat session CI4 dan cookie `auth_token`
3. frontend hit `/api/auth/me` untuk bootstrap user + role
4. frontend hit `/api/access/menu`
5. frontend hit `/api/access/control/{menu_id}` sesuai halaman aktif
6. frontend hide/show action berdasarkan code access

Jadi backend ini memang sudah disiapkan untuk pola menu-based access control.

## Catatan Development

- beberapa API invalidate data menu/access setelah perubahan role atau menu
- untuk API unprotected tertentu, signature check bisa di-skip lewat `app_support`
  dengan key:
  - `skip_check_signature`
- API log sengaja tidak ikut membuat response log normal, kecuali saat error `500`

## Testing Minimal Setelah Setup

Setelah migrate dan seed:

1. login dengan akun super admin
2. cek cookie `auth_token` dan `ci_session` setelah login
3. cek `GET /api/auth/me`
4. cek `GET /api/access/menu`
5. cek `GET /api/access/control/{menu_id}`
6. pastikan super admin bisa akses:
   - menu
   - role
   - user
   - parameter
   - log

## Saran Penggunaan

Project ini cocok dijadikan base app ketika kamu ingin:

- backend MVC + API tetap di satu project
- public page SEO-friendly
- admin panel terpisah di React
- deployment ringan tanpa harus menjalankan Node process untuk admin hasil build
