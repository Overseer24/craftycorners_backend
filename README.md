# Employee Management System  

A simple **CRUD (Create, Read, Update, Delete) application** for managing employees, built with **Laravel, Vue.js, Inertia.js, and Tailwind CSS**.  
This project includes **authentication**, employee listing, search, and pagination.  

💻 **Fully Responsive**: Works on **desktop, tablet, and mobile** screens.  

---

## 📸 Screenshots  

### 🔐 Login Page  
![Login](https://i.ibb.co/bRrcKKvy/login.png)  

### 📜 Empty Employee Table  
![Empty](https://i.ibb.co/2LVKHK0/empty-table.png)  

### ➕ Create Employee Form  
![Create](https://i.ibb.co/PdzD56w/create-employee.png)  

### ✅ Employee Added  
![Added Employee](https://i.ibb.co/tp8PHWcm/added-employee.png)  

### ✏️ Edit Employee Details  
![Update](https://i.ibb.co/1YwKQJcr/update-employee.png)  

### 🔄 Employee Updated  
![Updated](https://i.ibb.co/SwpYP7rr/updated-employee.png)  

### 🗑️ Delete Employee Confirmation Modal  
![Delete](https://i.ibb.co/8nVkX3V7/delete-employee.png)  

---

## ✨ Features  
✅ **User Authentication** (Login, Logout)  
✅ **Employee Management** (Create, Read, Update, Delete)  
✅ **Search Employees** (by Name or Email)  
✅ **Pagination Support**  
✅ **Sorting (Name, Email)**  
✅ **Modal Confirmation for Deletion**  
✅ **Fully Responsive UI** (Mobile-friendly, adapts to all screen sizes)  

---

## 🚀 Installation Guide  

1.  **Open a Terminal or Command Prompt:**

  

Navigate to the project directory by running this command

  

```bash
cd example-path-to-project
```

  

2.  **Install Dependencies:**

  

Run this command :

```bash
composer install
```

  
3.  **Environment Setup:**

  

There is a .env.example already provided inside the project and to use that just run

```bash
cp .env.example .env
```

  

Generate the app key by running:

```bash
php artisan key:generate
```

  
  

4.  **Database Configuration:**

  

Open ``.env`` file and configure the database

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5.  **Migrate the Database:**


Run the following command to setup the database tables

```bash
php artisan migrate
```


6.  **Run the application:**


Finally, start the Laravel server by running

```bash
php artisan serve && npm run dev
```
