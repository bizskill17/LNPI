# LNPI Backend (PHP + MySQL)

This backend is designed for Hostinger shared hosting.

## Structure

- `public/` is the web root (document root).
- `public/api/` contains API endpoints consumed by the React frontend.

## Setup

1) Create MySQL database and user in Hostinger.
2) Import schema: `sql/schema.sql`.
3) Copy `config.example.php` to `config.php` and fill credentials.
4) Point your domain/subdomain document root to `backend/public`.

