# Project Name

## Introduction

This is a **Library Management Website** built with pure PHP using the **MVC (Model – View – Controller)** architecture. The project helps manage library books, such as viewing book lists and book details, and can be expanded with borrow and return features in the future. The main purpose of this project is learning and practicing MVC structure and clean, maintainable code organization.

## Project Purpose

This project provides hands-on experience in building a real-world web application. Students will:

* Develop a dynamic, database-driven full-stack web application
* Implement full CRUD operations (Create, Read, Update, Delete)
* Apply the MVC architectural pattern using pure PHP
* Work in teams using Scrum methodology
* Deliver professional documentation and presentations

## Main Features

### User (Reader)

* Login / Register account
* Password reset
* View book list
* View book details
* Send borrow requests
* Borrow confirmation
* View borrowed books list
* Return books
* Track borrow status
* Renew borrowed books
* Manage user profile
* View personal borrowing history

### Admin

* Manage books (Create, Read, Update, Delete)
* Manage book categories
* View borrowing list
* Search and sort books
* Manage overdue books
* Manage reader accounts
* View reader details
* Block / unblock readers

### Reports & Statistics

* Borrowing reports
* Popular books reports
* Inventory reports

## Team Members

All members work as **full-stack developers**. Each member participates in both frontend and backend tasks, including building the user interface, writing PHP logic, working with the database, and testing features.

* Bùi Tiến Lạc
* Lê Minh Nhật
* Hồ Thị Bèng

## Technologies Used

* Programming languages: PHP, HTML, CSS, JavaScript
* Database: MySQL (or similar)
* Frontend: HTML, CSS, JavaScript
* Backend: PHP

## Project Structure (MVC)

```
project-root/
├── controllers/
│   └── admin/
│       ├── AdminBookController.php
│       ├── AdminCategoryController.php
│       ├── AdminImportBookController.php
│       ├── AdminUserController.php
│       ├── BorrowingController.php
│       └── DashboardController.php
│
├── auth/
│   ├── forgot-password.php
│   ├── login.php
│   ├── register.php
│   └── reset-password.php
│
├── books/
│   └── detail.php
│
├── cart/
│   ├── borrow_form.php
│   └── cart_list.php
│
├── layouts/
│   ├── header.php
│   └── footer.php
│
├── index.php
├── profile.php
├── blog/
│
├── database/
│   └── schema.sql
│
├── public/
│   ├── css/
│   ├── images/
│   ├── js/
│   ├── .gitkeep
│   └── .htaccess
│
├── admin.php
├── index.php
├── .gitignore
├── composer.phar
└── README.md

## Installation Guide

1. Clone the repository:

```bash
git clone https://github.com/VIP-lacland/library-management-system.git
```

2. Copy the project into your server folder (htdocs if using XAMPP).
3. Import the database `.sql` file into MySQL.
4. Configure database settings in the config file.
5. Run the project in the browser: `http://localhost/library-management-system`
