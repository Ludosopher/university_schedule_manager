# Study Schedule Manager

## Project Overview

**Study Schedule Manager** is a web application designed to streamline the management of academic schedules in universities and secondary vocational institutions. 

The primary goal is to automate the complex process of scheduling class substitutions and rescheduling, which is traditionally done manually. This leads to significant time savings, reduced conflicts, and more optimal scheduling decisions.

**Key Features:**
- Automated schedule generation and display in multiple formats.
- Intelligent substitution and rescheduling suggestions.
- Collaborative approval workflow for schedule changes.

📄 **Detailed Presentation:** [Google Slides Presentation](https://docs.google.com/presentation/d/1maPxwBLoTUOyWbs5pewZkGxrePXk1EvHxAX8YykAL-k/edit#slide=id.g1d7221fd002_0_60)

---

## System Requirements

- **Docker** & **Docker Compose**
- **PHP 8.0+** (inside container)
- **MySQL 8.0+**
- **Laravel 7**

---

## Installation & Configuration

### 1. Environment Configuration

1. Copy the template file to create your environment configuration:
   ```bash
   cp .env.example .env

2. Open the .env file and configure the database connection:

DB_CONNECTION=mysql
DB_HOST=mysql-db
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
MYSQL_ROOT_PASSWORD=your_root_password

3. Administrator Credentials

Set the default administrator account in the .env file:

ADMIN_NAME="Your Name"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=your_secure_password

Note: If these values are not set, the system will use the following default credentials:

Name: admin
Login: schedule_manager@mail.ru
Password: schedule-admin

4. Email Sending Configuration
The application sends email notifications to teachers about substitution and rescheduling requests.

Step-by-Step Setup:
Create an Application Mailbox
Use a mail service like Mail.ru (e.g., schedule_manager@mail.ru).

Generate an App-Specific Password
In your mailbox settings, navigate to the security/passwords section and create a password specifically for external applications.
(This is not your personal mailbox password.)

Update the .env File
Example configuration for Mail.ru:

MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.ru
MAIL_PORT=465
MAIL_USERNAME=schedule_manager@mail.ru
MAIL_PASSWORD=your_app_specific_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=schedule_manager@mail.ru
MAIL_FROM_NAME="Study Schedule Manager"
QUEUE_CONNECTION=database

5. Testing Mode (Optional)
To redirect all emails to a single test address during development, set:

IS_TESTING=true
TESTING_EMAIL=test@example.com

Emails are sent from the "My Replacement Requests" section. See the presentation for more details.


### 2. Local Deployment with Docker

1. Build the Application Image:
`docker compose build`

2. Start Containers:
`docker compose up -d`

3. Enter the Application Container:
`docker exec -it laravel-app /bin/bash`

4. Generate the Application Key:
`php artisan key:generate`
After that, the key will appear in the configuration file .env (APP_KEY)

5. Run Migrations & Seed the Database:
`php artisan migrate --seed`

6. To refresh the database and re-seed:
`php artisan migrate:fresh --seed` (when restarting).

Demo Data Note:
The demo data is fictional and is only fully populated (to demonstrate all features) for the Faculty of Business and Social Technologies.


### 3. Service Access

Application: http://localhost

PHPMyAdmin: http://localhost:8080

Default Administrator Login (if not changed):

Login: schedule_manager@mail.ru
Password: schedule-admin