CREATE DATABASE dockerworkshop;

USE dockerworkshop;

CREATE TABLE visits (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    time DATETIME,
    ip VARCHAR(64)
);