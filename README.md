# php-bibliotheek
eindopdracht voor php <br>
op het moment krijg ik het niet voor elkaar om de site een github pagina te maken met een onbekende reden dit zal op een later tijdstip opgelost worden

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


https://jortkalsbeek.github.io/php-bibliotheek/ 
