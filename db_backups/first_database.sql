-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 24, 2013 at 02:52 AM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `AWU`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(75) NOT NULL,
  `password` char(40) NOT NULL,
  `url` varchar(100) NOT NULL,
  `description` varchar(140) DEFAULT NULL,
  `city` varchar(40) NOT NULL,
  `state` varchar(40) DEFAULT NULL,
  `country` varchar(50) NOT NULL,
  `datetime_joined` datetime NOT NULL,
  `media` varchar(200) DEFAULT NULL,
  `tags` varchar(300) DEFAULT NULL,
  `API_key` char(40) NOT NULL,
  `verified` tinyint(4) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `url`, `description`, `city`, `state`, `country`, `datetime_joined`, `media`, `tags`, `API_key`, `verified`, `first_name`, `last_name`) VALUES
(1, 'bob@thomas.com', 'b7d045ced8a819cb3948f0c45fd115c5da797342', 'bobart.com', 'This is bob''s art.', 'Philadelphia', 'PA', 'United States', '0000-00-00 00:00:00', 'Painting', 'Color field, large paintings, modern', 'aaba9a110665cfff6656b99f20bd59f1c2146565', 0, 'Bob', 'Robinson'),
(2, 'robart@jonas.com', 'bfed89e80ea0c1c69ea35d3920d226aeebdded33', 'robart.com', 'A cool guy who just wants to make poop art.', 'San Antonio', 'TX', 'United States', '0000-00-00 00:00:00', 'Poop', 'poop, art, paintings, rob', '356a192b7913b04c54574d18c28d46e6395428ab', 1, 'Robinson', 'Jacoffrey'),
(3, 'gregorrrrrreeey@greg.com', '00b7b8118efde44c546b69243ec15b21cd64c9d9', 'thisisgreg.com', 'World class artist', 'Baltimore', 'MD', 'United States', '0000-00-00 00:00:00', 'sculpture, sound', 'loud, grunge art, sassy sculpture', 'c0392e39c0c0ed9126e7906adb64e621b11b70ba', 0, 'Gregory', 'Renoldson'),
(4, 'bada55bboy420@yahoo.com', 'bf945223e89cce48dcfe24ca3d2596a85d6a90cc', 'bada55bboy.biz', 'Just a bboy trying to make some cheddar in this world.', 'Juneau', 'AK', 'United States', '0000-00-00 00:00:00', 'Dance', 'breakdance, bboy, dance, hip-hop', 'da4b9237bacccdf19c0760cab7aec4a8359010b0', 1, 'Donald', 'Smeltzer'),
(5, 'garlandFrances@gmail.com', '408a5771606748ff56935b5d1da14d2d738d90b8', 'garlandfrances.com', 'Art at its finest', 'Paris', 'Little France', 'France', '0000-00-00 00:00:00', 'Photography, Digital Imaging', 'photo, garland, france, best french photographer', '53c85adb9432412d2479a812091eafea5af1c85a', 0, 'Garland', 'Frances'),
(6, 'coolguy42@gmail.com', '1b4a8aa34158a0d2b18211a8c37f5afec08b4cd5', 'brammomblorkey.com', 'Cool guy who''s in to cool things.', 'Richmond', 'VA', 'United States', '0000-00-00 00:00:00', 'Everything', 'I, Make, Cool, Stuff', 'ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4', 1, 'Brammom', 'Blorkey'),
(7, 'ChanceyAdams@gmail.com', '7c090e3a08dc393d4240d86a87a9116529601f8d', 'chanceyart.com', 'Art with Chance!', 'Richmond', 'VA', 'United States', '0000-00-00 00:00:00', 'Painting', 'physics', '9a6747fc6259aa374ab4e1bb03074b6ec672cf99', 1, 'Chancey', 'Adams'),
(8, 'browningtons@gmail.com', '61ecb633a78568f483a8b0ad0bde3ec090e504da', 'browningtons.com', 'How brown can you go?', 'Cleveland', 'Ohio', 'United States', '0000-00-00 00:00:00', 'Painting', 'Brown, paintings, if, you, know, what, I, mean', '553ae7da92f5505a92bbb8c9d47be76ab9f65bc2', 0, 'Debbie', 'Black'),
(9, 'jolenecrafts@jlen.com', 'f06f6d529824c807ac2c1d5468a87c4e00a5b5a7', 'www.jo-lene-crafts.co.uk', 'I just like to make crafts. Crafty crafts crafts crafts.', 'Austin', 'TX', 'United States', '0000-00-00 00:00:00', 'Crafts', 'crafts, austin, blankets, knit', '1b0f1dc92cf8e36adf61ca36ed95f8f5ce1ececd', 1, 'Jolene', 'Jenson');
