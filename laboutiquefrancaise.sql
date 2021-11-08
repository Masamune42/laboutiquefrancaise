-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  lun. 08 nov. 2021 à 14:30
-- Version du serveur :  5.7.31
-- Version de PHP :  5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `laboutiquefrancaise`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Manteaux'),
(2, 'Bonnets'),
(3, 'T-shirts'),
(4, 'Echarpes');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20211015130350', '2021-11-05 12:28:23', 90),
('DoctrineMigrations\\Version20211028124106', '2021-11-05 12:28:23', 88),
('DoctrineMigrations\\Version20211107104253', '2021-11-07 10:43:27', 133),
('DoctrineMigrations\\Version20211107143935', '2021-11-07 14:40:22', 387);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `illustration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D34A04AD12469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `category_id`, `name`, `slug`, `illustration`, `subtitle`, `description`, `price`) VALUES
(1, 2, 'Bonnet rouge', 'bonnet-rouge', 'd1c5c405b803f29b805e1589378feade52d4a654.jpg', 'Le bonnet parfait pour l\'hiver', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec urna porttitor, mattis sem ac, elementum felis. Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue. Pellentesque pretium convallis tristique. Quisque nisl mi, scelerisque eget malesuada in, accumsan sed arcu.', 900),
(2, 2, 'Le Bonnet du skieur', 'le-bonnet-du-skieur', '3ba53762bd5dfc04e8e3543bd15e431b7c4d2547.jpg', 'Le bonnet parfait pour le ski', 'Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue. Pellentesque pretium convallis tristique. Quisque nisl mi, scelerisque eget malesuada in, accumsan sed arcu.', 1400),
(3, 4, 'L\'écharpe du loveur', 'lecharpe-du-loveur', '76e7fe76a75184cee14ba3cc5ee6468f40166460.jpg', 'L\'écharpe parfaite pour les soirées romantiques', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec urna porttitor, mattis sem ac, elementum felis. Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue.', 1600),
(4, 1, 'Le manteau de soirée', 'le-manteau-de-soiree', 'e1187e22424d3bfd6ed994447cf32098a4690e67.jpg', 'Le manteau Français pour vos soirées', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec urna porttitor, mattis sem ac, elementum felis. Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue. Pellentesque pretium convallis tristique. Quisque nisl mi, scelerisque eget malesuada in, accumsan sed arcu.', 6900),
(5, 3, 'Le T-shirt manches longues', 'le-t-shirt-manches-longues', 'b06dbc1673ce9be66f79950bf04b61e9f4bce1fb.jpg', 'Le T-shirt taillé pour les hommes', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec urna porttitor, mattis sem ac, elementum felis. Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue. Pellentesque pretium convallis tristique. Quisque nisl mi, scelerisque eget malesuada in, accumsan sed arcu.', 2570),
(6, 4, 'L\'écharpe pas chère', 'lecharpe-pas-chere', 'ce9b98851fcaf53b7b027d4f67cbcfde25ef684b.jpg', 'L\'écharpe pour les petits budgets', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec urna porttitor, mattis sem ac, elementum felis. Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue. Pellentesque pretium convallis tristique. Quisque nisl mi, scelerisque eget malesuada in, accumsan sed arcu.', 750),
(7, 1, 'Le manteau chaud', 'le-manteau-chaud', 'c6808a54e9feaba1c325d66e9e305209f52766e9.jpg', 'Le manteau parfait quand vous avez (très) froid', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec urna porttitor, mattis sem ac, elementum felis. Sed quam mi, suscipit vitae vestibulum non, ullamcorper ut urna. Curabitur porttitor velit et vehicula rutrum. Maecenas vehicula nulla non mi eleifend suscipit. Cras justo erat, maximus in placerat eget, tristique a augue. Pellentesque pretium convallis tristique. Quisque nisl mi, scelerisque eget malesuada in, accumsan sed arcu.', 5600);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `firstname`, `lastname`) VALUES
(3, 'aaa@aa.fr', '[]', '$2y$13$aOczAKcfGBjUEZwy/lskse1rYi4PGUe5D/iHC/DJ9JBxeMh4O977e', 'aaaaaaa', 'aaa'),
(4, 'a.c@ht.fr', '[]', '$2y$13$Afg2PYzCmkrFxufMLcMS..CejI01QgK/7cMS1D6OPcoJS97qJrKoW', 'alex', 'cai');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
