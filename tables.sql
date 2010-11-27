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
	`Item` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`Value` int(10) unsigned NOT NULL,
	UNIQUE KEY (`Item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `System` (`Item`, `Value`) VALUES
('SendingMessageID', 0),
('ActiveSending', false);





/* GUI tables */
CREATE TABLE IF NOT EXISTS 	`SystemSettings` (
	`Item` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`Value` varchar(100) COLLATE utf8_czech_ci NOT NULL,
	`Name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`Description` text COLLATE utf8_czech_ci NOT NULL,
	UNIQUE KEY (`Item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `SystemSettings` (`Item`, `Value`, `Name`, `Description`) VALUES
('bound', 'science-agency.cz', 'Boundary', ''),
('mailer', 'phpSMTPBulk', 'Mailer identification', ''),
('charset', 'utf-8', 'Charset', ''),
('from', 'somebody@server.tld', 'From', 'Here is the e-mail adress of sender.'),
('contentType', 'html', 'Content type', 'If you want to use HTML in your messages you must choose the HTML option here, if you don''nt you should choose the plaintext option.'),
('smtpServer', 'server.tld', 'SMTP server', ''),
('smtpLogin', 'login_name', 'SMTP login', ''),
('smtpPassword', 'password', 'SMTP password', ''),
('smtpPort', '25', 'SMTP Port', 'The default value is port number 25.'),
('smtpTimeout', '30', 'SMTP timeout', 'The default value is 30.'),
('smtpAuthType', 'login', 'SMTP Authorization type', 'The default value is "login".'),
('smtpSmtpType', 'smtp', 'SMTP Type', 'The default is "esmtp". You can choose the "smtp" value too.'),
('batch', '50', 'Batch', 'Number of e-mails which you want to send in one batch.');
