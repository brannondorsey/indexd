-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 31, 2013 at 05:36 PM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `AWU`
--

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `organization`) VALUES
(33, 'test'),
(34, 'blue'),
(35, 'black'),
(36, 'bat'),
(37, 'ball'),
(38, 'ballocks'),
(39, 'belt'),
(40, 'beltway'),
(41, 'highway'),
(42, 'high'),
(43, 'how'),
(44, 'hole'),
(45, 'holy'),
(46, 'hol'),
(47, 'back');
