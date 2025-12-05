PHP CRUD Application using AJAX

A simple and lightweight Core PHP CRUD application that performs Create, Read, Update, and Delete operations without page reload using AJAX, jQuery, and MySQL.

This project demonstrates a clean approach to building fast, responsive CRUD functionality without any frameworks.

🚀 Features

Full CRUD (Create, Read, Update, Delete)

AJAX-based operations — no page reload

MySQL database integration

Clean, modular code structure

Simple UI using HTML & Bootstrap

JSON response handling

📁 Project Structure
project-folder/
│── config/
│   └── db.php            # Database connection file
│
│── ajax/
│   ├── insert.php        # Create record
│   ├── fetch.php         # Read records
│   ├── edit.php          # Fetch a single record
│   ├── update.php        # Update record
│   └── delete.php        # Delete record
│
│── assets/
│   ├── js/
│   │   └── script.js     # AJAX logic
│   └── css/
│       └── style.css     # Custom UI styles
│
│── user.sql              # Database file
│── index.php             # Main UI
│── README.md             # Project documentation

🛠️ Technologies Used

Core PHP

MySQL

jQuery / AJAX

Bootstrap

HTML / CSS

JSON

🗄️ Database Setup (Using user.sql)

Follow these steps to import the provided database file (user.sql) using phpMyAdmin:

Open phpMyAdmin in your browser:

http://localhost/phpmyadmin


Click New (left sidebar) to create a new database.

Enter a database name (example):

php_ajax_crud


Then click Create.

Select the newly created database from the left sidebar.

Click the Import tab from the top menu.

Click Choose File and select:

user.sql


Confirm the format is SQL.

Scroll down and click Go.

After successful import, tables will appear inside the database.

Update your database credentials in:

config/db.php

▶️ How to Run the Project

Download/clone the project.

Extract it inside your web server directory:

XAMPP → htdocs/

WAMP → www/

Import the database using the steps above.

Update DB credentials in config/db.php.

Open the project in your browser:

http://localhost/your-project-folder/