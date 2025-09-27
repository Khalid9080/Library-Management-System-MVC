
# 📚 Libraria — Role-Based Library Management System (LMS)

Modern, database-backed LMS with **role-based dashboards** for **Admin / Librarian / Member**. Supports adding & updating books, member buy-requests, librarian approvals, and admin-level metrics & transaction history — all tied to a single MySQL database.

> **Tech**: PHP (procedural MVC), MySQL, Vanilla JS, HTML5, CSS3  
> **Live pages**: `index.php?page=dashboard|register|login|...` via simple query-param routing

---

## 🚀 Features

### 🔑 Authentication & Roles
- Role-based login and registration (Admin / Librarian / Member).
- Secure password hashing and reset password functionality.
- Session-based authentication.

### 👤 Admin
- Add, update, view, and delete **Members** or **Librarians**.
- View total **Members**, **Books**, and **Sales**.
- Manage users (update username/phone by email & role).
- Transaction history (view all buy approvals made by librarians).
- CRUD operations on members/librarians.

### 📖 Librarian
- Add, update, view, and delete **Books**.
- Approve or reject **Book Buy Requests** from members.
- Maintain **Buy History** with member and book details.
- Dashboard cards for:
  - Add new books
  - Update book info
  - Total books added

### 👥 Member
- Search and view **catalog of books** (fetched in real-time from database).
- Request to **buy books** (single or multiple).
- View **My Book Requests** (pending approvals).
- View approved books in **My Books** after librarian approval.

### 📊 Dashboard Overview
- **Admin**: Total Members, Total Books, Total Sales.
- **Librarian**: Total Books, All Books table, Approve Requests, Buy History.
- **Member**: Catalog with live search, My Books, My Book Requests.

---

## 🛠️ Tech Stack

- **Backend**: PHP (MVC architecture)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS)
- **Styling**: Responsive CSS with reusable components
- **Version Control**: Git & GitHub

---

## 🗂️ Project Structure (Markdown list)

- `index.php`
- `Database/`
  - `database.php`
- `MVC/`
  - `Controller/`
    - `AuthController.php`
    - `guard.php`
    - `BooksController.php`
    - `RequestsController.php` _(UPDATED)_
    - `AdminStatsController.php`
  - `Model/`
    - `User.php`
  - `View/`
    - `Authentication/`
      - `login.php`
      - `register.php`
      - `forgot_password.php`
    - `Dashboard/`
      - `Admin/`
        - `admin_home.php`
        - `AdminTable.php` _(UPDATED)_
        - `manage_users.php`
        - `transaction_history.php`
      - `Librarian/`
        - `LibrarianTable.php`
        - `add_book.php`
        - `update_book.php`
        - `approved_buy_requests.php`
        - `buy_history.php`
      - `Member/`
        - `member_home.php`
        - `my_books.php`
        - `my_book_requests.php`
    - `Reusable_Components/`
      - `header.php`
      - `footer.php`
      - `main.php`
      - `dashboard.php`
- `Public/`
  - `Assets/` _(images/fonts/etc.)_
  - `JS/`
    - `register.js`
    - `login.js`
    - `forgot_password.js`
    - `add_book.js`
    - `update_book.js`
    - `member.js`
    - `member-requests.js`
    - `librarian-approvals.js`
    - `librarian-history.js`
    - `member-my-books.js`
    - `admin.js`
    - `admin-manage-users.js`
    - `admin-manage-users-actions.js`
    - `admin-users.js`
    - `admin-transactions.js`
  - `Style/`
    - `index.css`
    - `login.css`
    - `forgot_password.css`
    - `register.css`
    - `dashboard.css`
    - `librarian-table.css`
    - `librarian-forms.css`
    - `member.css`
    - `admin.css`
    - `admin-manage-users.css`
    - `librarian-approvals.css`
    - `librarian-history.css`
    - `member-my-books.css`
    - `member-my-requests.css`
    - `admin-transactions.css`
- `.env.sample`

---
## 🧩 Roles & Capabilities

| Capability | Admin | Librarian | Member |
|---|:--:|:--:|:--:|
| Register / Login / Reset | ✅ | ✅ | ✅ |
| Add / Update / Delete Books | ❌ | ✅ | ❌ |
| Search Catalog | ❌ | ✅ (view) | ✅ |
| Send Buy Request | ❌ | ❌ | ✅ |
| Approve / Reject Requests | ❌ | ✅ | ❌ |
| View My Book Requests | ❌ | ❌ | ✅ |
| View My Books (post-approval) | ❌ | ❌ | ✅ |
| Manage Users (update username/phone) | ✅ | ❌ | ❌ |
| Users Directory (delete user) | ✅ | ❌ | ❌ |
| Admin Metrics (Totals & Transactions) | ✅ | View own history | View own |

---
## 🗄️ Database Schema

### Table: `roles`
| role_id | role_name   |
|---------|-------------|
| 1       | Admin       |
| 2       | Librarian   |
| 3       | Member      |

### Table: `users`
| id  | username | email         | phone   | password_hash | role_id | created_at |
|-----|----------|---------------|---------|---------------|---------|------------|
| 1   | JohnDoe  | john@ex.com   | 1234567 | ************* | 1       | 2025-01-01 |

**users**
- `id` (INT, PK, AUTO_INCREMENT)
- `username` (VARCHAR)
- `email` (VARCHAR, UNIQUE)
- `phone` (VARCHAR)
- `password_hash` (VARCHAR)
- `role_id` (FK → roles.role_id)
- `created_at` (TIMESTAMP)

### Notes
- All authentication and role-based access control rely on the **users** and **roles** tables.
- Additional tables for `books`, `buy_requests`, and `transactions` should be implemented for full functionality.

---

## ⚙️ Installation & Setup

### Prerequisites
- PHP 8+
- MySQL 5.7+ or MariaDB
- Apache / Nginx (or XAMPP/LAMP/MAMP/WAMP for local setup)
- Git

### Steps
1. **Clone Repository**
```bash
git clone https://github.com/yourusername/Library-Management-System.git
cd Library-Management-System

