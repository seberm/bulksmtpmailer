CREATE TABLE IF NOT EXISTS 	`Mail` (
	`id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(150) COLLATE utf8_czech_ci NOT NULL,
	`email` varchar(150) COLLATE utf8_czech_ci NOT NULL,
	`sent` bool default false,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


INSERT INTO `Mail` (`name`, `email`) VALUES
('Otto Sabart', 'seberm@gmail.com'),
('Otto Sabart', 'seberm@gmail.com');


CREATE TABLE IF NOT EXISTS 	`Message` (
	`id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
	`subject` varchar(100) COLLATE utf8_czech_ci NOT NULL,
	`text` text COLLATE utf8_czech_ci NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `Message` (`subject`, `text`) VALUES
('Zkusebni email', '<b>The test e-mail message</b><br><br>ěšččřřžžýýáííégšřť');


CREATE TABLE IF NOT EXISTS 	`Queue` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
	`messageID` int(10) unsigned NOT NULL,
	`isSending` bool default false,
	`isCompleted` bool default false,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `Users` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(50) NOT NULL,
  `realName` VARCHAR(200) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `Users` (`login`, `realName`, `password`) VALUES
('seberm', 'Sabart Otto', 'f78f4014da74ae989e0362c266c66ee86e95693c');


/* GUI tables */
CREATE TABLE IF NOT EXISTS 	`SystemSettings` (
	`item` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`value` varchar(100) COLLATE utf8_czech_ci NOT NULL,
	`name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
	`description` text COLLATE utf8_czech_ci NOT NULL,
	UNIQUE KEY (`Item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `SystemSettings` (`item`, `value`, `name`, `description`) VALUES
('bound', 'science-agency.cz', 'Boundary', ''),
('mailer', 'phpSMTPBulk', 'Mailer identification', ''),
('charset', 'utf-8', 'Charset', 'Fill a charset you want to use'),
('from', 'somebody@server.tld', 'From', 'Here is the e-mail adress of sender'),
('contentType', 'html', 'Content type', 'If you want to use HTML in your messages you must choose the HTML option here, if you don''nt you should choose the plaintext'),
('smtpServer', 'server.tld', 'SMTP server', 'This is your SMTP server adress'),
('smtpPort', '25', 'SMTP Port', 'The default value is port number 25'),
('smtpLogin', 'login_name', 'SMTP login', 'The login to your SMTP server'),
('smtpPassword', 'password', 'SMTP password', 'The password to your SMTP server'),
('smtpTimeout', '30', 'SMTP timeout', 'A value of keep-alive time-out; default is 30'),
('smtpAuthType', 'login', 'SMTP Authorization type', 'You can choose a type of SMTP authorization between "login" and "plain"; default is "login"'),
('smtpSmtpType', 'smtp', 'SMTP Type', 'You can choose between various SMTP types (smtp, esmtp); default is "esmtp"'),
('smtpUseProxy', '0', 'Use of proxy server', 'If you want to use proxy, you must fill 1. If you don''t, fill 0'),
('smtpProxyServer', '', 'SMTP Proxy server', 'Your proxy server''s address; use of proxy server must be switched to 1'),
('smtpProxyPort', '', 'SMTP Proxy port', 'Your proxy server''s port'),
('batch', '50', 'Batch', 'Number of e-mails which you want to send in a one batch.');
