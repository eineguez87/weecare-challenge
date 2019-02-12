CREATE DATABASE `weecare_challenge`;

USE weecare_challenge;

CREATE TABLE `albums` (
album_id BIGINT NOT NULL PRIMARY KEY,
  name varchar(255) NOT NULL,
  artist varchar(255) NOT NULL,
  artist_link varchar(255) NOT NULL,
  category_id int(11) NOT NULL,
  release_date timestamp NOT NULL,
  rank int(11) NOT NULL,
  inserted timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `categories` (
  category_id int(11) NOT NULL UNIQUE,
  name varchar(255) NOT NULL,
  link varchar(255) NOT NULL
);

CREATE TABLE `album_art` (
album_id BIGINT NOT NULL,
  album_image varchar(255),
  image_size int(4)
);

ALTER TABLE `album_art`
ADD UNIQUE KEY `album_art_unique` (`album_id`, `image_size`);
