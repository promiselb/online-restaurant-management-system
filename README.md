#  🍽️  Online Restaurant Management System

## Description
The purpose of this project is to implement web development techniques and principles as demanded by [CNAM University](https://si.isae.edu.lb/) in [Développement Web (3)](https://formation.cnam.fr/rechercher-par-discipline/developpement-web-3-mise-en-pratique-208576.kjsp?RF=&EXT=cnam). The goal is to allow an easy and secure way for a restaurant to manage its database of clients, products, categories etc... at real-time.
> NO OFFENSE INTEDED: It was a coincidence that the template is about an italian cuisine. Altough the data provided in the database doesn't look much italian. It is clearly for educational purposes. No offense is intended for the italian food if any misunderstanding occurs!!!

## 📚 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Technologies Used](#-technologies-used)
- [Installation](#%EF%B8%8F-installation)
- [Usage](#-usage)
- [Database Setup](#%EF%B8%8F-database-setup)
- [Folder Structure](#-folder-structure)
- [Contributing](#-contributing)
- [License](#-license)
- [Authors](#authors)

## ✅ Features

- 🧾 User-friendly menu browsing
- 🛒 Shopping cart with quantity management
- 🧍 Customer order placement
- 🛠️ Admin panel for:
  - Adding/updating/deleting products
  - Managing categories
  - Viewing and managing orders
- 📦 Order status tracking (e.g., pending, delivered)
- 🔐 Basic access control for admin dashboard

## 📸 Screenshots
> *(Add screenshots here by uploading them to the repo or using links)*

## 🧰 Technologies Used

- **Backend:** PHP (Procedural)
- **Frontend:** HTML, CSS, Bootstrap
- **Database:** MySQL
- **Server Requirements:** Apache/Nginx with PHP support (XAMPP/LAMP recommended)

## ⚙️ Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/promiselb/online-restaurant-management-system.git
   cd online-restaurant-management-system

2. **Move the project to your local server directory:**
  For XAMPP: Move the folder into `htdocs/`.

3. **Create the database:**
  Import the restaurant.sql file into your MySQL server.

4. **Update DB connection:**
  In `connection.php`, make sure your DB credentials are correct:

  ```php
  $conn = new mysqli("localhost", "root", "", "restaurant");
  ```

5. **Run the project:**
  Open your browser and go to http://localhost/online-restaurant-management-system/  

## 📋 Usage
- Homepage: Menu browsing and ordering by customers.
- Cart: Add/remove items and submit orders.
- Admin Panel: Accessible from /admin (credentials can be found in the database or added manually).

## 🗃️ Database Setup
The SQL schema is located at dataProject.sql.
Tables:
- account
- client
- employee
- admin
- delivery man
- categorie
- product
- order
- order_product
- admin secret key
- reservations
- contact messages

Make sure MySQL is running and the database is properly imported before using the app. Also there are couples of insertion statments for products and categories.

## 📁 Folder Structure
```online-restaurant-management-system/
├── .idea/              # Not important  
├── vscode/             # Not important  
├── css/                # Default styles by the template and our own style  
├── js/                 # Default JS code by the template  
├── vendor/             # Some style related folers by the template  
├── dataProject.sql     # Database schema  
└── ...                 # Main PHP code  
```
## 🤝 Contributing
Contributions are welcome! If you'd like to add a feature or fix a bug:
Fork the repo
1. Create a new branch (`git checkout -b feature-name`)  
2. Commit your changes (`git commit -am 'Add new feature'`)  
3. Push to the branch (`git push origin feature-name`)  
4. Open a pull request

## 📄 License

This project is licensed under the [MIT License](LICENSE), except for third-party assets.

### Template License

The HTML/CSS/JS front-end template is based on the [Restaurant Template](https://htmlcodex.com/demo/?item=140) by [HTML Codex](https://htmlcodex.com/), used under their [Free License](https://htmlcodex.com/license).  
Please respect their license terms if reusing or redistributing the template code.  

## Authors
[Mahmoud Mnajjed](https://github.com/)
[Waed Mansour](https://github.com/promiselb)

