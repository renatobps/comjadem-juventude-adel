# API + admin — pré-inscrições CONJADEM

Laravel 13, PHP 8.3+, MySQL (ou SQLite para testes).

## 1. Banco de dados (MySQL)

Crie o banco (exemplo):

```sql
CREATE DATABASE conjadem_inscricoes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

No arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conjadem_inscricoes
DB_USERNAME=root
DB_PASSWORD=sua_senha

ADMIN_EMAIL=admin@conjadem.local
ADMIN_PASSWORD=defina_uma_senha_forte

FRONTEND_URL=http://127.0.0.1:5500
```

`FRONTEND_URL` deve ser a origem exata da landing (protocolo + host + porta), para o CORS liberar o `fetch` do formulário.

## 2. Instalação

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## 3. Endpoints

| Método | URL | Descrição |
|--------|-----|-----------|
| POST | `/api/inscricoes` | JSON: `nome`, `idade`, `whatsapp`, `igreja_id` (id da igreja cadastrada), `lider` (`sim` ou `nao`) |
| GET | `/admin/login` | Página de login |
| GET | `/admin` | Dashboard (após login, usuário com `is_admin = 1`) |

Credenciais padrão do seed (se não definir no `.env`): **admin@conjadem.local** / **admin123456**.

## 4. Landing + admin no mesmo projeto

- Landing pública: `/`
- Login admin: `/admin/login`
- Dashboard admin: `/admin`

A landing já está em `resources/views/landing.blade.php` e usa a API do mesmo projeto via `{{ url('/api/inscricoes') }}`.
