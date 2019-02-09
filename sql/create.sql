CREATE DATABASE `weecare_challenge`;

USE weecare_challenge;

CREATE TABLE `albums` (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name varchar(255) NOT NULL,
  artist varchar(255) NOT NULL,
  artist_link varchar(255) NOT NULL,
  category_id int(11) NOT NULL,
  release_date timestamp NOT NULL,
  inserted timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `categories` (
  category_id int(11) NOT NULL UNIQUE,
  name varchar(255) NOT NULL,
  link varchar(255) NOT NULL
);

