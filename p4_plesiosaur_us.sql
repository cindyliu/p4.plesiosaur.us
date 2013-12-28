-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 29, 2013 at 12:41 AM
-- Server version: 5.5.29
-- PHP Version: 5.4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `plesiosa_p4_plesiosaur_us`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_started` int(11) NOT NULL,
  `last_played` int(11) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'live' COMMENT 'is game current or finished?',
  PRIMARY KEY (`game_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `guesses`
--

CREATE TABLE `guesses` (
  `game_id` int(11) NOT NULL,
  `guess_no` int(11) NOT NULL AUTO_INCREMENT,
  `guess_date` int(11) NOT NULL,
  `word` varchar(5) NOT NULL,
  `num_correct` int(11) DEFAULT NULL,
  PRIMARY KEY (`guess_no`,`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `joined` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

