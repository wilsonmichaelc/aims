aims
====

#### Analytical Instrument Management System

###### Try it here: wilsoninformatics.com/ims

###### Step 1

LAMP Stack

###### Step 2

Import the SQL file located in the SQL directory. It will perform the following...

1. Create the database "aims" and all the tables needed.
2. Create an SQL user called "aims" with password "#MySecretPassword#". You will probably want to change this password.
3. Grant CREATE, DELETE, INSERT, SELECT, UPDATE on aims.* to "aims" user that was just created.
4. Insert new user "admin" into the "users" table so you can login to the system and have full control. Password is "password" so make sure you change it.

###### Step 3

Copy the aims directory (or the contents) to wherever you are hosting your content. Usually in /var/www on linux systems.
You probably want to omit the "sql" directory from this copy operation.

###### Notes
I created this project for the University of Maryland, Baltimore Mass Spectrometry Center. It's purpose is to allow tracking and management of the cost center samples and pricing. It has also been modified to generate invoices, but this feature still needs some work.

While it is technically fully function and mostly feature complete, the code could definitely use some work. I had never written PHP prior to this project so its not MVC and probably breaks a lot of rules.

If you are interested in using and/or contributing to this project, feel free to contact me.
