-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 22 sep. 2020 à 14:33
-- Version du serveur :  10.3.22-MariaDB-1ubuntu1
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `twittux`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnements`
--

CREATE TABLE `abonnements` (
  `id` int(11) NOT NULL,
  `suiveur` varchar(255) NOT NULL,
  `suivi` varchar(255) NOT NULL,
  `etat` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `abonnements`
--

INSERT INTO `abonnements` (`id`, `suiveur`, `suivi`, `etat`) VALUES
(28, 'tester', 'christophe_kheder', 1),
(51, 'alexa', 'christophe_kheder', 1),
(57, 'christophe_kheder', 'tester', 1);

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id_comm` int(255) NOT NULL,
  `commentaire` text NOT NULL,
  `id_tweet` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `tweets`
--

CREATE TABLE `tweets` (
  `id_tweet` int(11) NOT NULL,
  `user_tweet` varchar(255) NOT NULL,
  `user_timeline` varchar(255) NOT NULL,
  `contenu_tweet` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `nb_commentaire` int(111) NOT NULL DEFAULT 0,
  `nb_partage` int(111) NOT NULL DEFAULT 0,
  `nb_like` int(111) NOT NULL DEFAULT 0,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `allow_comment` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tweets`
--

INSERT INTO `tweets` (`id_tweet`, `user_tweet`, `user_timeline`, `contenu_tweet`, `created`, `nb_commentaire`, `nb_partage`, `nb_like`, `private`, `allow_comment`) VALUES
(85757725, 'christophe_kheder', 'christophe_kheder', 'test', '2020-09-22 10:37:43', 0, 0, 0, 0, 0),
(795019360, 'christophe_kheder', 'christophe_kheder', '<a href=\"/twittux/search/hashtag/%23test\">#test</a>', '2020-09-22 14:31:41', 0, 0, 0, 0, 0),
(1149918933, 'christophe_kheder', 'christophe_kheder', 'test Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis  ', '2020-09-22 10:38:22', 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text,
  `lieu` text,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `description`, `lieu`, `created`) VALUES
(1, 'tester', '$2y$10$CVsSgE.2Say5l/Gg6BLU5.XHhN4bfS2d6p156laMZCNUl1fOi5GZS', 'osefman57@gmail.com', 'bêta testeurCEO at Mighty Schools. Marketing and Advertising. Seeking a new job and new opportunities.', 'new york', '2020-04-13 09:12:53'),
(27, 'christophe_kheder', '$2y$10$tS8onC/LafzR/MHV.21CRutYePrIJLQtJiBZHc7/ubx78mW9uiAyu', 'christophekheder@gmail.com', 'Développeur WEB', 'Metz', '2020-04-17 08:23:27'),
(30, 'alexa', '$2y$10$Ymzba5RjliSyF.EMkQ4ALe/MxxRZ.ubTbPBY0RJ0F04fqje1iKM3m', 'alexa@gmail.com', 'la best #test', 'aucun lieu', '2020-07-02 10:21:33');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `abonnements`
--
ALTER TABLE `abonnements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id_comm`),
  ADD KEY `id_tweet` (`id_tweet`);

--
-- Index pour la table `tweets`
--
ALTER TABLE `tweets`
  ADD PRIMARY KEY (`id_tweet`);
ALTER TABLE `tweets` ADD FULLTEXT KEY `ft_contenu_tweet` (`contenu_tweet`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);
ALTER TABLE `users` ADD FULLTEXT KEY `ft_username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `abonnements`
--
ALTER TABLE `abonnements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `fk_id_tweet` FOREIGN KEY (`id_tweet`) REFERENCES `tweets` (`id_tweet`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
