SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `your_db_name`
--
CREATE DATABASE `your_db_name` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `your_db_name`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `graph_point`
--

CREATE TABLE IF NOT EXISTS `graph_point` (
  `id_graph_point` int(11) NOT NULL AUTO_INCREMENT,
  `p1x` varchar(256) DEFAULT NULL,
  `p2x` varchar(256) DEFAULT NULL,
  `p3x` varchar(256) DEFAULT NULL,
  `p4x` varchar(256) DEFAULT NULL,
  `p5x` varchar(256) DEFAULT NULL,
  `p1y` varchar(256) DEFAULT NULL,
  `p2y` varchar(256) DEFAULT NULL,
  `p3y` varchar(256) DEFAULT NULL,
  `p4y` varchar(256) DEFAULT NULL,
  `p5y` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id_graph_point`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;



--
-- Struktura tabeli dla tabeli `phpsec_settings`
--

CREATE TABLE IF NOT EXISTS `phpsec_settings` (
  `config_error_reporting_level` text NOT NULL,
  `config_default_timezone` text NOT NULL,
  `config_mail_from` text NOT NULL,
  `config_mail_from_name` text NOT NULL,
  `config_mail_reply_to` text NOT NULL,
  `config_mail_reply_to_name` text NOT NULL,
  `config_mail_subject` text NOT NULL,
  `config_sms_api` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Zrzut danych tabeli `phpsec_settings`
--

INSERT INTO `phpsec_settings` (`config_error_reporting_level`, `config_default_timezone`, `config_mail_from`, `config_mail_from_name`, `config_mail_reply_to`, `config_mail_reply_to_name`, `config_mail_subject`, `config_sms_api`, `id`) VALUES
('E_ALL~E_NOTICE', 'Europe/Warsaw', 'some_mail@yourdomain.tld', 'SuperSafeForm', 'some_mail@yourdomain.tld', 'SuperSafeForm', 'Your login code', 'http://your_sms_gateway_server.tld/api.php', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_graph`
--

CREATE TABLE IF NOT EXISTS `user_graph` (
  `id_user_graph` int(11) NOT NULL AUTO_INCREMENT,
  `id_users` int(11) NOT NULL,
  `number_of_ug` int(11) NOT NULL,
  `tryb` int(11) NOT NULL,
  `id_graph_point` int(11) NOT NULL,
  `picture_name` varchar(256) NOT NULL,
  PRIMARY KEY (`id_user_graph`),
  KEY `id_users` (`id_users`),
  KEY `id_graph_point` (`id_graph_point`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;


--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text NOT NULL,
  `fullname` text NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` text NOT NULL,
  `auth_mechanism` varchar(64) NOT NULL,
  `current_code` varchar(64) NOT NULL,
  `image_hitboxes` blob NOT NULL,
  `path_to_image_password` text NOT NULL,
  `phone` varchar(64) NOT NULL,
  `gauth_secret` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Zrzut danych tabeli `users`
-- Password for every example user is 'example'
--

INSERT INTO `users` (`id`, `login`, `fullname`, `password`, `email`, `auth_mechanism`, `current_code`, `image_hitboxes`, `path_to_image_password`, `phone`, `gauth_secret`) VALUES
(1, 'by_mail', 'Jan Mailowy', '1a79a4d60de6718e8e5b326e338ae533', 'users_mail@some.tld', 'email', '7489', '', '', '', ''), 
(2, 'by_sms', 'Krzysztof Smsowy', '1a79a4d60de6718e8e5b326e338ae533', 'users_mail@some.tld', 'sms', '2021', '', '', '+02220137210', ''),
(3, 'by_pic', 'Mariusz Obrazkowy', '1a79a4d60de6718e8e5b326e338ae533', 'users_mail@some.tld', 'image', '4029', '', '', '', ''),
(4, 'by_ga', 'Ksawery Googlowy', '1a79a4d60de6718e8e5b326e338ae533', 'users_mail@some.tld', 'gauth', '6648', '', '', '+0221377219', 'XVQ2UIGO75XRUKJO');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
