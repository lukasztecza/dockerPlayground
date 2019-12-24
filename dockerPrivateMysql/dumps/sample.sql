CREATE DATABASE sample_database;

USE sample_database;

CREATE TABLE `sample_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `sample_items` (`name`) VALUES
('apple'), ('lemon'), ('cherry'), ('tomato'), ('pepper'), ('zucchini'), ('pumpkin'),
('bean'), ('onion'), ('potato'), ('carrot'), ('broccoli'), ('lettuce'), ('corn');
