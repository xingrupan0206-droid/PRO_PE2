# Database setup

The schema and dashboard views are managed by Laravel migrations. The tables and data from `database/migrations/Tanarts.sql` have been mapped to Laravel naming conventions, while `users` remains compatible with Laravel's session authentication.

For local MySQL/MariaDB development:

1. Create a database named `Tandarts` using `utf8mb4` and `utf8mb4_unicode_ci`.
2. Set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.
3. Run `php artisan migrate --seed`.

The local `.env` and `.env.example` in this project are configured for the common WAMP defaults (`127.0.0.1:3306`, `root`, empty password). Update `.env` if your MySQL credentials differ. The database server must be running before migrations or the website can access it.

The development seeder creates all sample data from the SQL file. All sample accounts use password `wachtwoord`; do not use or seed these demo credentials in production.

Open `/login` to sign in. Successful logins go to the protected `/dashboard`; the dashboard includes a logout action.

`php artisan migrate:fresh --seed` drops existing tables before rebuilding them. Use it only when you intend to delete the database contents.
