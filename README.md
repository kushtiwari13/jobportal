# ProJobs — PHP Job Portal

Modern, production-ready Job Portal built with PHP 8, MySQL (PDO), and Bootstrap 5. Includes public listings, SEO-friendly job pages, public job submission (review queue), and a secure admin panel with full CRUD.

## Features

- Clean, responsive UI (Bootstrap 5 + Inter font)
- Jobs listing with search (title/company, location, skills) and pagination
- Single job page with Apply, Share, and Report
- Public Post Job form (goes to approval queue)
- Admin panel: login, dashboard, add/edit/delete (soft), approve, search/sort
- Security: PDO prepared statements, CSRF tokens, session timeout, output escaping
- SEO: `/job/{id}/{slug}` via `.htaccess`

## Setup (XAMPP)

1. Create database `jobportal`.
2. Import `database.sql`.
3. Configure DB in `config/db.php` (host/user/password).
4. Create an admin account:
   - Generate a password hash using PHP:
     ```php
     <?php echo password_hash('yourpassword', PASSWORD_DEFAULT); ?>
     ```
   - Insert:
     ```sql
     INSERT INTO admin_users (email, password_hash) VALUES ('admin@example.com', 'PASTE_HASH_HERE');
     ```
5. Ensure Apache `mod_rewrite` is enabled for SEO URLs.
6. Visit `http://localhost/jobportal/` and `http://localhost/jobportal/admin/login.php`.

## Deploy (Shared Hosting / Hostinger)

- Upload all files to your hosting (public_html/jobportal or root).
- Set correct DB credentials in `config/db.php`.
- Ensure `.htaccess` is honored and `mod_rewrite` enabled.
- PHP 8.0+ recommended.

## Folder Structure

See source tree; key directories:

- `includes/` shared PHP includes (db, functions, layout)
- `admin/` admin area (auth guard, dashboard, CRUD)
- `assets/` CSS/JS/images
- `config/` database config

## Notes

- Email notifications: `post-job.php` calls `mail()`; configure SMTP on hosting for delivery.
- Soft delete: `delete-job.php` marks `is_active = 0`.
- Expired jobs auto-hidden on public listing.

## License

For personal and commercial use. No attribution required.

