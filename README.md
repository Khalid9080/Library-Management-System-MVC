Library-Management-System/
│
├── index.php
│
├── Database/
│   └── database.php
│
├── MVC/
│   ├── Controller/
│   │   ├── AuthController.php
│   │   ├── guard.php
│   │   ├── BooksController.php
│   │   ├── RequestsController.php          
│   │   └── AdminStatsController.php
│   │
│   ├── Model/
│   │   └── User.php
│   │
│   └── View/
│       ├── Authentication/
│       │   ├── login.php
│       │   ├── register.php
│       │   └── forgot_password.php
│       │
│       ├── Dashboard/
│       │   ├── Admin/
│       │   │   ├── admin_home.php
│       │   │   ├── AdminTable.php          
│       │   │   ├── manage_users.php
│       │   │   └── transaction_history.php
│       │   │
│       │   ├── Librarian/
│       │   │   ├── LibrarianTable.php
│       │   │   ├── add_book.php
│       │   │   ├── update_book.php
│       │   │   ├── approved_buy_requests.php
│       │   │   └── buy_history.php
│       │   │
│       │   └── Member/
│       │       ├── member_home.php
│       │       ├── my_books.php
│       │       └── my_book_requests.php
│       │
│       └── Reusable_Components/
│           ├── header.php
│           ├── footer.php
│           ├── main.php
│           └── dashboard.php
│
├── Public/
│   ├── Assets/
│   │   └── (images/fonts/etc.)
│   │
│   ├── JS/
│   │   ├── register.js
│   │   ├── login.js
│   │   ├── forgot_password.js
│   │   ├── add_book.js
│   │   ├── update_book.js
│   │   ├── member.js
│   │   ├── member-requests.js
│   │   ├── librarian-approvals.js
│   │   ├── librarian-history.js
│   │   ├── member-my-books.js
│   │   ├── admin.js
│   │   ├── admin-manage-users.js
│   │   ├── admin-manage-users-actions.js
│   │   ├── admin-users.js
│   │   └── admin-transactions.js
│   │
│   └── Style/
│       ├── index.css
│       ├── login.css
│       ├── forgot_password.css
│       ├── register.css
│       ├── dashboard.css
│       ├── librarian-table.css
│       ├── librarian-forms.css
│       ├── member.css
│       ├── admin.css
│       ├── admin-manage-users.css
│       ├── librarian-approvals.css
│       ├── librarian-history.css
│       ├── member-my-books.css
│       ├── member-my-requests.css
│       └── admin-transactions.css
│
└── .env.sample
