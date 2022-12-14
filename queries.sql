create database restapi;

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
 last_name varchar(50) NOT NULL,
 password varchar(400) NOT NULL,
 email varchar(100) NOT NULL,
 rol tinyint(4) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS sessions (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(100) NOT NULL,
  token mediumtext NOT NULL,
  expirate int(11) NOT NULL,
  inicialice int(11) NOT NULL,
  PRIMARY KEY (id)
)

CREATE TABLE IF NOT EXISTS posts (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(100) NOT NULL,
  description text NOT NULL,
  user_id int(11) NOT NULL,
  createAt datetime DEFAULT NULL,
  PRIMARY KEY (id),
) 

ALTER TABLE posts
ADD CONSTRAINT fk_foreign_user_post
FOREIGN KEY (user_id)
REFERENCES users(id);