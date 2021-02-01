-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 20, 2021 at 02:18 PM
-- Server version: 10.2.36-MariaDB-log
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `theret14_nesdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `ID` int(11) NOT NULL,
  `gamename` text NOT NULL COMMENT 'the actual published name of the game.',
  `demo_rom` text NOT NULL COMMENT 'the public, playable demo.  THIS is what the emulator we are creating will see',
  `full_rom` text NOT NULL COMMENT 'hidden from the public',
  `developer_name` text NOT NULL COMMENT 'the legal name of the developer',
  `developer_email` text NOT NULL COMMENT 'official email for developer',
  `external_link` text NOT NULL COMMENT 'If developer has an external link',
  `date` date NOT NULL COMMENT 'publish date',
  `genre` text NOT NULL COMMENT 'The style of game',
  `description` text NOT NULL COMMENT '256 character description',
  `credits` text NOT NULL COMMENT 'Credits for anyone involved in this game',
  `promo_image` text NOT NULL COMMENT 'The image associated with this game',
  `screenshot1` text NOT NULL COMMENT '1',
  `screenshot2` text NOT NULL COMMENT '2',
  `screenshot3` text NOT NULL COMMENT '3',
  `video_link` text NOT NULL COMMENT 'A link to gameplay video',
  `rating` double NOT NULL COMMENT 'The user rating for this game',
  `cartridge_available` tinyint(4) NOT NULL COMMENT 'A toggle that either lists that the cartridge is available or unavailable for purchase.',
  `free_full_game` tinyint(4) NOT NULL COMMENT 'a toggle that will be used in a later milestone.',
  `demo_only` tinyint(4) NOT NULL COMMENT 'a toggle that will be used in a later milestone',
  `tag` text NOT NULL COMMENT 'Game select tag',
  `cat` text NOT NULL COMMENT 'cat number',
  `user_id` int(11) NOT NULL COMMENT 'account for next use',
  `rate_users` text NOT NULL COMMENT 'rated users, separated by ,',
  `rate_count` text NOT NULL COMMENT 'rated user count'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`ID`, `gamename`, `demo_rom`, `full_rom`, `developer_name`, `developer_email`, `external_link`, `date`, `genre`, `description`, `credits`, `promo_image`, `screenshot1`, `screenshot2`, `screenshot3`, `video_link`, `rating`, `cartridge_available`, `free_full_game`, `demo_only`, `tag`, `cat`, `user_id`, `rate_users`, `rate_count`) VALUES
(1, 'DimensionShift', './uploads/ROM/5ffa3a8e7026d5ffa3a8e702715ffa3a8e70272.nes', '', 'The New 8bit Heroes, LLC', '', '', '2021-01-09', '1', 'Dimension Shift is mapper 30.', '', './uploads/Image/5ffa3a8f0c92d5ffa3a8f0c9305ffa3a8f0c931.jpg', './uploads/Image/5ffa3a8f0e0755ffa3a8f0e0775ffa3a8f0e078.jpg', './uploads/Image/5ffa3a8f0e28b5ffa3a8f0e28c5ffa3a8f0e28d.jpg', './uploads/Image/5ffa3a8f0e48b5ffa3a8f0e48c5ffa3a8f0e48d.jpg', '', 0, 1, 0, 0, '2', 'NM-21-000000', 2, '', ''),
(2, 'Flea', './uploads/ROM/5ffa3c5652be35ffa3c5652be85ffa3c5652be9.nes', '', 'The New 8bit Heroes, LLC', '', '', '2021-01-09', '1', 'This is Flea game', '', './uploads/Image/5ffa3c569d2685ffa3c569d26c5ffa3c569d26d.jpg', './uploads/Image/5ffa3c569e94d5ffa3c569e9505ffa3c569e951.jpg', './uploads/Image/5ffa3c569f4ff5ffa3c569f5005ffa3c569f501.jpg', './uploads/Image/5ffa3c56a19025ffa3c56a19045ffa3c56a1905.jpg', '', 4, 0, 0, 1, '5', 'NM-21-000001', 2, '', ''),
(3, 'MysticOrigins', './uploads/ROM/5ffa3d982cece5ffa3d982ced15ffa3d982ced2.nes', '', 'The New 8bit Heroes, LLC', '', '', '2021-01-09', '3', 'This is Mystic Origins game', '', './uploads/Image/5ffa3d98bbf665ffa3d98bbf6a5ffa3d98bbf6b.jpg', './uploads/Image/5ffa3d98bca425ffa3d98bca445ffa3d98bca45.jpg', './uploads/Image/5ffa3d98bf3115ffa3d98bf3135ffa3d98bf314.jpg', './uploads/Image/5ffa3d98c1b865ffa3d98c1b885ffa3d98c1b89.jpg', '', 0, 1, 0, 0, '6', 'NM-21-000002', 2, '', ''),
(6, 'Test Upload', './uploads/ROM/5ffb083822e9d5ffb083822ea05ffb083822ea1.nes', './uploads/ROM/5ffb08387c3ad5ffb08387c3af5ffb08387c3b0.nes', 'Whomever', '', '', '2021-01-10', '8', 'This is a long description meant to test a lot of things.  Does this have a max count for description, or can I drone on forever?  I know I had said something about 256 characters, but let\'s see if that\'s actually a limit or if the limit is arbitrary to whatever it can be.  I\'ll just keep typing.\r\n\r\nAnd then...dramatically make a new paragraph a few spaces down.  Let\'s see what that will do.  I wonder.  Will this work?  How long can this be?  Is there a maximum length?  What width will this draw the description on screen?  We\'ll see!', 'Art: Joe', './uploads/Image/5ffb0838acbdd5ffb0838acbe05ffb0838acbe1.jpg', '', '', '', '', 0, 0, 0, 0, '11', 'NM-21-000005', 2, '', ''),
(5, 'SilverIsland', './uploads/ROM/5ffa3e4e19ae15ffa3e4e19ae45ffa3e4e19ae5.nes', '', 'The New 8bit Heroes, LLC', '', '', '2021-01-09', '1', 'This is silver island game', '', './uploads/Image/5ffa3e4e625515ffa3e4e625545ffa3e4e62555.jpg', './uploads/Image/5ffa3e4e63d055ffa3e4e63d065ffa3e4e63d07.jpg', './uploads/Image/5ffa3e4e648cd5ffa3e4e648ce5ffa3e4e648cf.jpg', './uploads/Image/5ffa3e4e671605ffa3e4e671615ffa3e4e67162.jpg', '', 0, 0, 0, 1, '8', 'NM-21-000004', 2, '', ''),
(7, 'TrollBurner', './uploads/ROM/5ffdcf55a49425ffdcf55a49455ffdcf55a4946.nes', '', 'The New 8bit Heroes, LLC', 'test@nestest.com', '', '2021-01-12', '1', 'Mapper30', 'Artist: Test user,\r\nDesigner: LLC', './uploads/Image/5ffdcf5657c5d5ffdcf5657c5f5ffdcf5657c60.png', './uploads/Image/5ffdcf565a06c5ffdcf565a06e5ffdcf565a06f.jpg', './uploads/Image/5ffdcf565cdd35ffdcf565cdd45ffdcf565cdd5.jpg', './uploads/Image/5ffdcf565d8c65ffdcf565d8c75ffdcf565d8c8.png', '', 0, 0, 0, 0, '13,', 'NM-21-000006', 2, '', ''),
(8, 'Test Game Project Dart', './uploads/ROM/5ffdd6c8e710f5ffdd6c8e71145ffdd6c8e7115.nes', './uploads/ROM/5ffdd6c95b0225ffdd6c95b0245ffdd6c95b025.nes', 'CutterCross', 'nothing@nothing.com', '', '2021-01-12', '1', 'This is a game.', 'Joe: Art\r\nJoe: Music\r\nJoe: Programming', './uploads/Image/5ffdd6c96384f5ffdd6c9638515ffdd6c963852.png', '', '', '', '', 0, 1, 0, 0, '12,', 'NM-21-000007', 1, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
