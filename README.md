# Study Schedule Manager

## Justification of the need

The target audience is students, teachers, and administrative staff of universities and institutions of secondary vocational education.
In an educational institution, it is often necessary to perform substitutions and postponements of classes. This requires comparing large amounts of information and coordinating the plans of different parties. At the same time, this work is most often performed manually, which leads to a large expenditure of time and effort, the choice of suboptimal options, and conflicts.
The application solves these problems by:
- automating the provision of schedules in different formats;
- selection and selection of options for replacing and rescheduling classes;
- coordination of the resulting changes in the plans of the different parties.

A detailed description and demonstration of the functionality and interface of the application is available in [presentation](https://docs.google.com/presentation/d/1maPxwBLoTUOyWbs5pewZkGxrePXk1EvHxAX8YykAL-k/edit#slide=id.g1d7221fd002_0_60)


## Database Settings

1. Based on the .env.example template, create the .env configuration file in the root of the project.
In the configuration file .env specify the following database parameters:

DB_CONNECTION=mysql
DB_HOST=mysql-db
DB_PORT=3306
DB_DATABASE=***** // specify any database name
DB_USERNAME=***** // specify any username
MYSQL_ROOT_PASSWORD=***** // create your password
DB_PASSWORD=***** // create your password


## Email Sending settings

The application provides for sending letters to teachers with suggestions for replacing or rescheduling classes.
To configure the operation of this function, you must:

1. Create an application mailbox in one of the mail services, for example, email - schedule_manager@mail.ru . The application will send emails from this mailbox.

2. In the mailbox account, in the settings, in the section related to passwords and security, create a password for external applications. This password will be used to access the mailbox from this particular application. However, this password is not used for personal access to the mailbox in the browser or for access from other applications.

3. Fill in the specified fields in the ”.env” configuration file. Here is an example for a mail server mail.ru:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.ru
MAIL_PORT=465
MAIL_USERNAME=********** // for example, schedule_manager@mail.ru
MAIL_PASSWORD=**************** // password for external applications
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=********** // for example, schedule_manager@mail.ru
MAIL_FROM_NAME=********* // please provide any name that suits you.
QUEUE_CONNECTION=database

4. If you are in the process of testing in the .env configuration file, specify "true" in the IS_TESTING field, and specify the email address in the TESTING_EMAIL field where replacement requests should be sent. If this is not done, the letters will be sent to the addresses of the teachers indicated in the database.

5. Emails are sent in the "My replacement requests" section. More information about this can be found from [презентации](https://docs.google.com/presentation/d/1maPxwBLoTUOyWbs5pewZkGxrePXk1EvHxAX8YykAL-k/edit#slide=id.g1d7221fd002_0_60).


## Local deployment

The application is deployed and managed using Docker.
1. Create an application image in the terminal at the root of the project:
`docker compose build`

2. Launch the application containers:
`docker compose up -d`

3. Log in from the root of the project to the terminal of the application container:
`docker exec -it laravel-app /bin/bash`

4. Generate the project key:
`php artisan key:generate`
After that, the key will appear in the configuration file .env (APP_KEY)

5. Start migrations and filling the database with demodata:
`php artisan migrate —seed`
or
`php artisan migrate:fresh --seed` (when restarting).
The application contains fictitious data of teachers, student groups and training sessions, and is only for the Faculty of Business and Social Technologies.


## Access to services

Application: http://localhost
To log in as an administrator:
    login: schedule_manager@mail.ru
    password: schedule-admin

PHPMyAdmin: http://localhost:8080


--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

# Менеджер учебного расписания

## Обоснование

Целевая аудитория - студенты, преподаватели и административный персонал университетов и учреждений среднего профессионального образования.
В учебном заведении часто возникает необходимость в замене и переносе занятий. Это требует сопоставления больших объемов информации и согласования планов разных сторон. При этом чаще всего эта работа выполняется вручную, что приводит к большим затратам времени и усилий, выбору неоптимальных вариантов и конфликтам.
Приложение решает эти проблемы путем:
- автоматизации предоставления расписаний в различных форматах;
- выбора вариантов замены и перепланировки занятий;
- координации возникающих изменений в планах различных сторон.

Подробное описание и демонстрация функциональности и интерфейса приложения доступны в [презентации](https://docs.google.com/presentation/d/1maPxwBLoTUOyWbs5pewZkGxrePXk1EvHxAX8YykAL-k/edit#slide=id.g1d7221fd002_0_60)


## Настройки базы данных

1. На основе шаблона .env.example создайте файл конфигурации .env в корне проекта.
В файле конфигурации .env укажите следующие параметры базы данных:

DB_CONNECTION=mysql
DB_HOST=mysql-db
DB_PORT=3306
DB_DATABASE=***** // укажите любое имя базы данных
DB_USERNAME=***** // укажите любое имя пользователя
MYSQL_ROOT_PASSWORD=***** // создайте свой пароль
DB_PASSWORD=***** // создайте свой пароль


## Настройки отправки электронной почты

Приложение позволяет отправлять учителям письма с предложениями о замене или переносе занятий.
Чтобы настроить работу этой функции, вам необходимо:

1. Создать почтовый ящик приложения в одном из почтовых сервисов, например, email - schedule_manager@mail.ru . Приложение будет отправлять электронные письма из этого почтового ящика.

2. В учетной записи почтового ящика, в настройках, в разделе, связанном с паролями и безопасностью, создайте пароль для внешних приложений. Этот пароль будет использоваться для доступа к почтовому ящику из этого конкретного приложения. Однако этот пароль не используется для личного доступа к почтовому ящику в браузере или для доступа из других приложений.

3. Заполните указанные поля в файле конфигурации .env. Вот пример для почтового сервера mail.ru:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.ru
MAIL_PORT=465
MAIL_USERNAME=********** // например, schedule_manager@mail.ru
MAIL_PASSWORD=**************** // пароль для внешних приложений
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=********** // например, schedule_manager@mail.ru
MAIL_FROM_NAME=********* // пожалуйста, укажите любое подходящее вам имя.
QUEUE_CONNECTION=database

4. Если вы находитесь в процессе тестирования, в конфигурационном файле .env, укажите "true" в поле IS_TESTING, а в поле TESTING_EMAIL укажите адрес электронной почты, на который следует отправлять запросы на замену. Если этого не сделать, письма будут отправлены на адреса преподавателей, указанные в базе данных.

5. Электронные письма отправляются в разделе "Мои запросы на замену". Более подробную информацию об этом можно найти в [презентации](https://docs.google.com/presentation/d/1maPxwBLoTUOyWbs5pewZkGxrePXk1EvHxAX8YykAL-k/edit#slide=id.g1d7221fd002_0_60).


## Локальное развертывание

Приложение развертывается и управляется с помощью Docker.
1. Создайте образ приложения в терминале в корневой папке проекта:
`docker compose build`

2. Запустите контейнеры приложений:
`docker compose up -d`

3. Войдите из корневого каталога проекта в терминал контейнера приложения:
`docker exec -it laravel-app /bin/bash`

4. Сгенерируйте ключ проекта:
`php artisan key:generate`
После этого ключ появится в конфигурационном файле .env (APP_KEY)

5. Запустите миграцию и заполнение базы данных демоданными:
`php artisan migrate —seed`
или
`php artisan migrate:fresh --seed` (при перезапуске).
Приложение содержит вымышленные данные преподавателей, студенческих групп и учебных занятий и предназначено только для факультета бизнеса и социальных технологий.


## Доступ к сервисам

Приложение: http://localhost
Для входа в систему в качестве администратора:
    логин: schedule_manager@mail.ru
    пароль: schedule-admin

phpMyAdmin: http://localhost:8080