CREATE TABLE IF NOT EXISTS 	`Mail` (
	`id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
	`Name` varchar(150) COLLATE utf8_czech_ci NOT NULL,
	`Email` varchar(150) COLLATE utf8_czech_ci NOT NULL,
	`Sent` bool default false,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


INSERT INTO `Mail` (`Name`, `Email`) VALUES
('Otto Sabart', 'seberm@gmail.com'),
('Otto Sabart', 'seberm@gmail.com');


CREATE TABLE IF NOT EXISTS 	`Message` (
	`id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
	`Subject` varchar(100) COLLATE utf8_czech_ci NOT NULL,
	`Text` text COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `Message` (`Subject`, `Text`) VALUES
('Zkusebni email', '<b>The test e-mail message</b><br><br>ěšččřřžžýýáííégšřť');


CREATE TABLE IF NOT EXISTS 	`System` (
	`Item` varchar(100) COLLATE utf8_czech_ci NOT NULL,
	`Value` int(10) unsigned NOT NULL,
	UNIQUE KEY (`Item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `System` (`Item`, `Value`) VALUES
('SendingMessageID', 0),
('ActiveSending', false);
