-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Erstellungszeit: 06. Nov 2017 um 14:41
-- Server-Version: 5.6.35
-- PHP-Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `usermanagement`
--
CREATE DATABASE usermanagement;
USE usermanagement;
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Token`
--

CREATE TABLE `Token` (
  `id` int(11) NOT NULL,
  `token` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `Token`
--

INSERT INTO `Token` (`id`, `token`) VALUES
(1, 'm6912gkenj');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `Users`
--

INSERT INTO `Users` (`user_id`, `email`, `password`, `lastname`, `firstname`, `salt`, `created_at`) VALUES
(1, 'test1@test.de', '$2y$10$D6P2TVkMWAJDjzIEKE69I.TKF.nq57wi9ioSbt2oGhgps/hbUUtBq', 'Leue', 'Martin', '37bd2e06abecf2b3e3dfa999c1094ef9', '2017-11-06 13:11:48');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Token`
--
ALTER TABLE `Token`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
