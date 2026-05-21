# Portalia-Web 🌟

A web platform where students, professionals, and self-taught creators share projects, build digital portfolios, and inspire each other. Users can upload files of any type (images, videos, audio, PDFs, and text), browse other people's work, and save their favorites.

> Developed as a final project for the Web Programming course at Universidad de Guadalajara — CUCEA.

---

## Technologies

- **PHP** — server-side logic and routing
- **MySQL** — relational database (via WampServer locally)
- **JavaScript** — dynamic project card rendering and file preview
- **CSS3** — custom design system with CSS variables
- **Google Fonts** — Plus Jakarta Sans + Syne

---

## Features

- User registration and login with session management
- Editable personal profile (name, age, nationality, social links)
- Project upload supporting images, videos, audio, PDFs, and plain text (max 30 MB)
- Live file preview before submission
- Home feed showing the 9 most recent projects
- Search by creation date, category, or project name
- Favorites system — add and remove projects
- Public profile view for other users
- Admin account with the ability to delete any user or project
- Centralized database credentials via `config.php` (excluded from version control)
- Ads-free interface

---

## Project Structure

```
portalia/
│
├── config.php              # DB credentials — NOT committed (see .gitignore)
├── config.example.php      # Safe credentials template — commit this one
│
├── index.php               # Landing / welcome page
├── loginP.php              # Login and registration form
├── exists.php              # Auth handler (login / register logic)
├── createDB.php            # DB and table initialization (runs on first load)
├── regisData.php           # Profile data entry form (new users)
├── saveData.php            # Saves new profile data to DB
│
├── home.php                # Main feed — recent projects
├── search.php              # Search form (date / category / name)
├── searchResult.php        # Search results page
├── favorites.php           # User's saved favorites
│
├── profile.php             # Own profile — editable
├── othersProfile.php       # Public view of another user's profile
├── modifyData.php          # Saves profile edits to DB
│
├── project.php             # Single project detail view
├── newProject.php          # New project creation form
├── saveProject.php         # Handles file upload and project DB insertion
│
├── options.php             # Central action router (handles all POST actions)
├── destroy.php             # Session logout handler
│
├── style.css               # Global stylesheet
│
├── Images/                 # UI assets (logo, welcome images)
└── Projects/               # User-uploaded project files — NOT committed (see .gitignore)
```

---

## Database Schema

```
USERS (USER_ID, USER_NAME, PASSW)
    │
    └── PROFILEDATA (PROFILE_ID, USER_ID, FIRST_NAME, MIDDLE_NAME,
                     fLAST_NAME, mLAST_NAME, AGE, CONTINENT,
                     EMAIL, FACEBOOK, INSTAGRAM, X, WHATSAPP)
            │
            └── PROJECTS (PROJECT_ID, PROFILE_ID, pNAME, pDATE,
                           CATEGORY_ID, pDESCRIPTION, pCOMMENTS, URL_FILE)
                    │
                    └── FAVORITES (FAVORITE_ID, USER_ID, PROJECT_ID)

CONTINENTS (CONTINENT_ID, CONTINENT)
CATEGORIES (CATEGORY_ID, CATEGORY)
```

All foreign keys use `ON UPDATE CASCADE`. Deletes are restricted for categories and continents, and cascade for users and projects.

---

## Running the Project

### Prerequisites

- [WampServer](https://www.wampserver.com/) (or any local PHP + MySQL stack)
- PHP 7.4 or later
- MySQL 5.7 or later

### Setup

**1. Clone the repository**
```bash
git clone https://github.com/your-username/portalia.git
```

**2. Copy the credentials template and fill in your values**
```bash
cp config.example.php config.php
```

Then open `config.php` and set your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'dbPortalia');
```

**3. Place the project in your WampServer `www/` directory**
```
C:/wamp64/www/portalia/
```

**4. Start WampServer and open the app in your browser**
```
http://localhost/portalia/index.php
```

The database and all tables are created automatically on the first visit to `index.php`. A default admin account is also seeded:

| Field    | Value        |
|----------|--------------|
| Username | `ADMIN`      |
| Password | `SOYADMIN123`|

> ⚠️ Change the admin password before deploying to any shared or public environment.

---

## .gitignore Recommendation

```
config.php
Projects/

```

> The `Projects/` folder contains user-uploaded files and should not be committed. Keep it local only.

---

## Demo


https://github.com/user-attachments/assets/a46e20c7-a82e-4346-b627-b95949038a4d




