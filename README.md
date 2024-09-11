# Crafty Corners


[![License](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

### Description

This is created for the final requirement of our thesis subject. It is created using as a stand-alone API for the use of
our front-end application. It is created using **Laravel 10** and **PHP 8.2**. It is a **RESTful API** that is used to create a system
similar to Reddit but focuses on cultivating student hobbies and interests. 

This project is created by the following student:
- **Jaycie G. Dela Cruz** - *Front-end Developer*
- **Nixon Jr. M. Somoza** - *Back-end Developer*
- **Yvanne Zechael B. Vinzon**- *Project Manager*
- **Jan Andrei M. Francisco** - *Researcher/Technical Writer*




## PREREQUISITES

To run the project, ensure that you have the following installed:
1. PHP(version 8.0 or higher)
	Get it from https://www.php.net/downloads
2. Composer
	Download from https://getcomposer.org/download/

SETUP

1. Navigate to the project directory by running this command
 	cd example-path-to-project

2. Install Dependencies. 
	Run this command composer install

3. Environment Setup
	There is a .env.example already provided inside the project and to use that just run cp .env.example .env .
	Generate the app key by running php artisan key:genarate

4. Database Configuration
	Open .env file and configure the database

	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=your_database_name
	DB_USERNAME=your_username
	DB_PASSWORD=your_password
5. Migate the Database
	Run the following command to setup the database tables
	php artisan migrate

6. Run the application:
	Finally, start the Laravel server by running
	php artisan serve
