
# 📚 Libraria — Role-Based Library Management System (LMS)

Modern, database-backed LMS with **role-based dashboards** for **Admin / Librarian / Member**. Supports adding & updating books, member buy-requests, librarian approvals, and admin-level metrics & transaction history — all tied to a single MySQL database.

> **Tech**: PHP (procedural MVC), MySQL, Vanilla JS, HTML5, CSS3  
> **Live pages**: `index.php?page=dashboard|register|login|...` via simple query-param routing

---

## ✨ Features

- **Auth & Roles**
  - Register / Login / Logout / Reset password
  - Roles: **Admin**, **Librarian**, **Member** (single `users` table + `roles` table)
  - Passwords stored as `password_hash()` (bcrypt by default)

- **Dashboards**
  - **Admin**: Total Members, Total Books, Total Sales; Manage Users (update username/phone only), Users directory (delete); Transaction History
  - **Librarian**: Add/Update/Delete books, **Approve/Reject** member buy-requests, Buy History (approved requests + totals)
  - **Member**: Catalog (search, real-time from DB), select single/multi books → **Order Buy Request**; **My Book Requests**; **My Books** (after approval)

- **Buy Request Workflow**
  1. Member adds one or more books to a buy request (cart-style)
  2. Librarian sees **Approve / Reject** cards per request (with requester + books + prices)
  3. On **Approve**:
     - Books appear in **Member → My Books**
     - Entry appears in **Librarian → Buy History**
     - **Admin → Transaction History** & **Total Sales** update
  4. On **Reject**:
     - Request card disappears for librarian; request marked rejected for member

- **Consistent Data Model**
  - One source of truth for users/roles. Any admin edits (username/phone) reflect across app.

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
