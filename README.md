
# Crafty Corners



### Description



This is created for the final requirement of our thesis subject. It is created using as a stand-alone API for the use of

our front-end application. It is created using **Laravel 10** and **PHP 8.2**. It is a **RESTful API** that is used to create a system

similar to Reddit but focuses on cultivating student hobbies and interests.



This project is created by the following student:

-  **Jaycie G. Dela Cruz** - *Front-end Developer*

-  **Nixon Jr. M. Somoza** - *Back-end Developer*

-  **Yvanne Zechael B. Vinzon**- *Project Manager*

-  **Jan Andrei M. Francisco** - *Researcher/Technical Writer*


### PREREQUISITES

  

  

#### To run the project, ensure that you have the following installed:

  

1.  **PHP** (version 8.0 or higher)

  

Get it from [PHP Downloads](https://www.php.net/downloads)

  

  

2.  **Composer**

  

Download from [Composer](https://getcomposer.org/download/)

  
3. **MySQL or XAMPP (which includes MySQL)**

* Download MySQL from [MySQL Downloads](https://dev.mysql.com/downloads/mysql/)
* Download XAMPP from [XAMPP Download](https://www.apachefriends.org/download.html)

---

  
## Database Structure
The image below represents the structure of the database:

![CraftyCorners](https://i.ibb.co/L83dFnZ/erd.png)

---  
  

## SETUP

  
  

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
php artisan serve
```
