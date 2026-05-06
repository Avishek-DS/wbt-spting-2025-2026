# Game Management System (Compact MVC)

A minimal PHP + MySQL Game Management System, built with procedural mysqli + prepared statements. Admins manage games and levels. Players register, log in, and manage their own score records.

## Project Structure (MVC, flat)

```
game_management/
|-- config.php            # DB connection + auto-seed of default admin
|-- models.php            # M - all DB functions
|-- controllers.php       # C - login, register, admin, player
|-- index.php             # Front controller (router + AJAX + logout)
|-- style.css             # All styling
|-- game_management.sql   # DB schema
`-- views/                # V
    |-- login.php
    |-- register.php
    |-- admin.php         # Games + levels CRUD + AJAX search
    `-- player.php        # Player score CRUD + AJAX search
```

## Install (XAMPP)

1. Copy folder into `C:\xampp\htdocs\` so it becomes `htdocs\WBT\`.
2. Start Apache + MySQL in XAMPP.
3. Open `http://localhost/phpmyadmin`, import `game_management.sql`, and confirm the database name is `game_management`.
4. Open `http://localhost/WBT/`.
5. Login with the default admin: `admin / admin123`.

## Default Credentials

| Role   | Username | Password |
| ------ | -------- | -------- |
| Admin  | admin    | admin123 |
| Player | register one yourself | |

## How CRUD Works on a Single Page

- Top of each dashboard page has a form.
- The same form switches into edit mode when an Edit row link is clicked.
- Tables include Edit / Delete actions per row.
- Search boxes use AJAX through `index.php?page=ajax&type=...`.
