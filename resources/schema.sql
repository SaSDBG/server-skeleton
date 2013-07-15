CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roles` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isChief` boolean NOT NULL,
  `chiefOf_ID` int(11)  DEFAULT NULL,
  `chiefOf_Name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `kontoNR` varchar(5) NOT NULL,
    `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `private` boolean NOT NULL,
    `ownerID` int(11) DEFAULT NULL,
    `balance` int(11) NOT NULL,
    `kredit` int(11) NOT NULL,
    `isLimitless` boolean NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `transactions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `zeit` varchar(255) NOT NULL,
    `verwendungszweck` varchar(255) NOT NULL,
    `zielKNr` varchar(5) NOT NULL,
    `zielName` varchar(255) NOT NULL,
    `quellKNr` varchar(5) NOT NULL,
    `quellName` varchar(255) NOT NULL,
    `betrag` int(11) NOT NULL,
    `bemerkung` varchar(255) NOT NULL,
    `baID` int(11) NOT NULL,
    `printed` boolean NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
