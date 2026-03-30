# TaskWeave Marketplace

TaskWeave is a lightweight freelance marketplace built with PHP, HTML, CSS, and MySQL. It demonstrates a full flow for clients and freelancers: login, projects, bids, chat, payments, and profiles.

## Features
- Role-based accounts (client, freelancer, admin)
- Project listings with detailed views and bids
- In-platform chat threads per project
- Escrow-style payment tracking
- Editable user profiles with skills and bios

## Tech Stack
- PHP 8+
- MySQL 5.7+ / MariaDB
- HTML5 + CSS3

## Setup
1. Create a MySQL database and import the seed data:

```bash
mysql -u root -p < database.sql
```

2. (Recommended) create a dedicated DB user:

```sql
CREATE USER 'taskweave_user'@'localhost' IDENTIFIED BY 'Taskweave#2026!';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER
ON freelance_marketplace.* TO 'taskweave_user'@'localhost';
FLUSH PRIVILEGES;
```

3. Update database credentials in `includes/config.php`.
4. Start the PHP server from the project root:

```bash
php -S localhost:8000
```

5. Open `http://localhost:8000` in your browser.

## Demo Accounts
- Client: `client@taskweave.test` / `Password123!`
- Freelancer: `freelancer@taskweave.test` / `Password123!`
- Admin: `admin@taskweave.test` / `Password123!`

## Registration
Users can create new accounts at `register.php` (role: client or freelancer) and then sign in at `login.php`.

## Project Structure
- `assets/css/style.css`: global styles
- `assets/img/`: local image assets
- `includes/`: config, DB connection, shared layout, auth helpers
- `index.php`: landing page
- `projects.php`, `project.php`: project listings and bid flow
- `chat.php`: messaging
- `payments.php`: payment tracking
- `profile.php`: profile management

## Notes
This project is a functional demo. For production use, add CSRF protection, form validation, file uploads, and a proper payment gateway integration.

## Image Credits
Images are sourced from Unsplash and downloaded into `assets/img/`.
