# php-bibliotheek
eindopdracht voor php <br>
bij nader inzien kan github geen backend zoals php runnen vandaar de problemen waar ik tegen liep

/*dit is de code die in xampp gebruikt moet worden om de database te maken*/

CREATE DATABASE bibliotheek;
USE bibliotheek;

CREATE TABLE boeken (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    status ENUM('beschikbaar', 'uitgeleend') NOT NULL DEFAULT 'beschikbaar',
    uitleendatum DATETIME NULL,
    terugbrengdatum DATE NULL
);

