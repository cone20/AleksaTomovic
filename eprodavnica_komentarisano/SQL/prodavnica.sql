-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 23, 2023 at 10:14 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prodavnica`
--

-- --------------------------------------------------------

--
-- Table structure for table `administratori`
--

CREATE TABLE `administratori` (
  `id` int(11) NOT NULL,
  `korisnicko_ime` varchar(255) NOT NULL,
  `lozinka` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administratori`
--

INSERT INTO `administratori` (`id`, `korisnicko_ime`, `lozinka`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Table structure for table `artikli`
--

CREATE TABLE `artikli` (
  `id` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL,
  `kolicina` int(11) DEFAULT 0,
  `cena` float DEFAULT 0,
  `opis` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artikli`
--

INSERT INTO `artikli` (`id`, `naziv`, `kolicina`, `cena`, `opis`) VALUES
(1, 'Stark Keks', 1, 10, 'Stark keks sa ukusom cokolade'),
(2, 'Stark Smoki', 100, 3, 'Stark Smoki sa kikirikijem'),
(3, 'Lubenica', 99, 20, 'Sveze voce'),
(4, 'Ulje Dijamant', 100, 10, 'Dijamant suncokretovo ulje'),
(5, 'Trik Perece', 310, 3, '-'),
(6, 'Bake Rolls Salt', 501, 5, 'Bake Rolls sa slanim ukusom'),
(7, 'Bake Rolls Pizza', 23, 6, 'Bake Rolls sa ukusom pice'),
(8, 'Chipsy Domacinski Sir', 98, 4, 'Chipsy \"domacinski\" cips sa ukusom sira'),
(9, 'Kokice (100gr)', 100, 8.99, 'Kokice'),
(10, 'Bas Bas Tortilla Chips', 370, 8.5, 'Tzatziki tortilja cips'),
(11, 'Plazma Slana (140g)', 1000, 9.99, 'Maslina i sir, 140g'),
(12, 'FLIPS CLIPSY NUGAT 80G MARBO', 34, 10, '-'),
(13, 'BIOGRIC OVAS I LAN', 15, 15, '70G'),
(14, 'Integralni Krekeri', 253, 16.99, '100G '),
(15, 'Stapic Pardon', 133, 8.25, '220G'),
(16, 'Cips Pringles', 18, 30, 'Hot Paprika 165G'),
(17, 'Hummus Lentil Chips', 10, 19.99, 'Creamy Dill 30G EAT REAL'),
(18, 'Mini Snack Mix', 100, 23.55, 'Rifuz 100g'),
(19, 'Jaffacakes ', 283, 18, '-'),
(20, 'Najlepse zelje 100g', 234, 10, 'Mlecna cokolada'),
(21, 'Eurocrem ', 80, 23, '-'),
(22, 'Napolitanke Stark Kokos', 183, 17.22, '-'),
(23, 'Negro bombone', 389, 9, 'Odzacar grla'),
(24, 'Noblice', 239, 10, 'Sendvic keks'),
(25, 'Choco Biscuit', 158, 12.99, '60% najfinije cokolade'),
(26, 'Sweet Mlecni fil', 879, 2.99, 'Cokoladica'),
(27, 'Minjon kocke', 128, 13.99, '-'),
(28, 'Medela napolitanke', 83, 8.5, '-'),
(29, 'Pionir Karamela Lesnik', 26, 6.5, 'Karamela Lesnik bombone'),
(30, 'Praline Oragne', 17, 14, 'Praline sa pomorandzom'),
(31, 'Praline Jagoda', 38, 14.55, 'Praline sa jagodom'),
(32, 'Rum kasato', 103, 8, 'Slatkis sa rumom'),
(33, 'Take!', 103, 3, 'Cokoladica'),
(34, 'Toto Keks', 189, 12.5, 'Toto sendvic keks'),
(35, 'Vitanova jagoda', 100, 4, 'Vitanova sa ukusom jagode'),
(36, 'Menthol bombone', 890, 7, 'Bombone sa ukusom menthol-a'),
(37, 'Njamb lesnik', 103, 12, 'Njamb kocke sa ukusom lesnika'),
(38, 'Njamb cokolada', 398, 14.55, 'Njamb kocke sa ukusom cokolade'),
(39, 'Medela strudle', 183, 8, 'Strudle sa mesanim vocem'),
(40, 'Medela strudle smokva', 13, 9, 'Strudle sa filom od smokve'),
(41, 'Kidy cokoladica', 879, 4, 'Kidy cokoladica sa mlecnim punjenjem');

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lozinka` varchar(255) NOT NULL,
  `ime` varchar(255) NOT NULL,
  `prezime` varchar(255) NOT NULL,
  `stanje` float DEFAULT 1000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `email`, `lozinka`, `ime`, `prezime`, `stanje`) VALUES
(1, 'test@gmail.com', '202cb962ac59075b964b07152d234b70', 'Test', 'Test', 586.02),
(2, 'test1@gmail.com', '202cb962ac59075b964b07152d234b70', 'Test1', 'Test1', 1000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administratori`
--
ALTER TABLE `administratori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`);

--
-- Indexes for table `artikli`
--
ALTER TABLE `artikli`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administratori`
--
ALTER TABLE `administratori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `artikli`
--
ALTER TABLE `artikli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
