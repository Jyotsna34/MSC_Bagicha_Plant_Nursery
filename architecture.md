# Technical Architecture

# Overview

Frontend communicates with PHP REST APIs.

PHP handles business logic.

MySQL stores application data.

Images are stored inside uploads/.

---

# Tech Stack

Frontend

- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- Axios

Backend

- PHP 8+
- PDO
- REST API
- JWT Authentication

Database

- MySQL

Development

- XAMPP
- Git
- GitHub

---

# Application Flow

Customer

Browser

↓

Frontend

↓

PHP API

↓

MySQL Database

↓

Response

↓

Frontend Updates UI

---

Admin Flow

Admin Login

↓

Dashboard

↓

CRUD Operations

↓

PHP API

↓

Database

↓

Updated Dashboard

---

# Folder Structure

Bagicha/

    admin/
        includes/
            header.php
            sidebar.php
            footer.php
        assets/
            css/
                admin.css
                admin-sidebar.css
            js/
                admin.js
                admin-ui.js
                admin-charts.js
        messages.php
        settings.php
        ...
    api/
        admin/
            change-password.php
            settings/
            ...
        auth/
            login.php
            logout.php
            change-password.php
            register.php
            ...
        products/
        categories/
        cart/
        orders/
        wishlist/
        reviews/
        users/
        contact/
        notifications/

    assets/
        css/
        js/
            app.js
            auth.js
            password-toggle.js
            ...

    uploads/
        plants/
        categories/
        users/

    config/
        database.php
        constants.php

    includes/
        header.php
        navbar.php
        footer.php
        about-section.php
        cart-drawer.php

    pages/
        contact.php
        track-order.php
        guarantee.php
        products.php
        product-detail.php
        cart.php
        checkout.php
        orders.php
        profile.php

    index.php

---

# Database Tables

Users

Categories

Products

ProductImages

Wishlist

Cart

Orders

OrderItems

Reviews

Coupons

Addresses

Notifications

ContactMessages

---

# Authentication Flow

Register

↓

Verify User

↓

Login

↓

JWT Token

↓

Protected APIs

↓

Logout

---

# API Pattern

GET

POST

PUT

DELETE

Every endpoint returns JSON.

---

# Security

Password Hashing

Prepared Statements

JWT Authentication

Input Validation

File Upload Validation

Role-Based Authorization

CSRF Protection

Session Management
