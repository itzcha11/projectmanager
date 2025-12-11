For information purposes, below is a short explanation of what the files do in the following location. 

 

Database: 

PhpProject\database\ -> contains projectdb.sql, which is the database you need to import, used to populate the user and project tables. 

 

Recording: 

PhpProject\recording\ -> contains a recording in which I talk through the web functionality. 

 

Project Files: 

PhpProject\projectmanager\ -> Contains the main project files and subfolders.. 

Subfolders: 

PhpProject\projectmanager\css\ -> this contains styles.css which is used to 	control the page layout and ‘beautify’ it. 

PhpProject\projectmanager\includes\ -> this contains footer.php & header.php, 	which are the headers and footers. 

PhpProject\projectmanager\sql\ -> this contains the sql file ‘aproject.sql’ and a 	readme.txt, which explains the purpose, this file was used to create the users & 	projects table initially, provided by the tutor. 

PhpProject\projectmanager\tools\ -> this folder contains the following files, 	generate_hash.php, reset_password.php & readme.txt (just explains what they do), 	but are simple tools I created whilst developping the website initially, as I ran into 	some problems, however, 1 generates a hash value of a plaintext hardcoded 			password, and the other resets the password. 

 

 

 

PHP Files (in projectmanager root) PhpProject\projectmanager\: 

Config.php -> contains db connection settings / connecting using PDO 

Index.php -> handles search request for titles/dates, acts as a ‘homepage’ contains project titles, start dates, short description & view button. 

Login.php -> allows users to log into the website using credentials from user in database and prompts error if applicable. 

Logout.php -> logic to handle users loging out. 

Project_add.php -> code to allow users that are only logged in to be able to add a project. 

Project_edit.php -> code to allow owners of projects to edit their own projects, and logic for it. 

Project_view.php -> contains code for users to be able to view more project information such as end date/phase. 

Register.php -> code to allow users to register and save to db, allowing them to log in. 