CREATE TABLE users (

uid int NOT NULL,

username varchar(30),

password varchar(30),

email varchar(30),

PRIMARY KEY (uid)

);


CREATE TABLE projects (

 pid int NOT NULL,

 title varchar(30),

 start_date date,

 end_date date,

 short_description varchar(255),

 phase varchar(30),

 uid int(11) NOT NULL,

 PRIMARY KEY (pid),

 FOREIGN KEY (uid) REFERENCES users(uid)

);