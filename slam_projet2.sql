-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 05 juin 2026 à 13:27
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `slam_projet2`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `Id_avis` int(11) NOT NULL,
  `note` int(11) DEFAULT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  `date_avis` datetime DEFAULT NULL,
  `Id_modèle` int(11) NOT NULL,
  `Id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`Id_avis`, `note`, `commentaire`, `date_avis`, `Id_modèle`, `Id_utilisateur`) VALUES
(1, 5, 'Parfaites ! Très confortables dès la première utilisation.', '2025-01-20 00:00:00', 1, 1),
(2, 4, 'Belles chaussures, légèrement rigides au début mais ça passe.', '2025-02-25 00:00:00', 4, 1),
(3, 5, 'Les meilleures chaussures de running. Le Boost c est une autre dimension.', '2025-04-01 00:00:00', 6, 2),
(4, 3, 'Qualité correcte mais déçu par le coloris, moins vibrant qu en photo.', '2025-04-10 00:00:00', 5, 3),
(5, 5, 'Un classique indémodable. Ma troisième paire, toujours aussi satisfait.', '2025-03-15 00:00:00', 11, 4),
(6, 4, 'Confort exceptionnel. Parfait pour mes longues sessions de course.', '2025-05-20 00:00:00', 3, 5),
(7, 5, 'Superbe finition, cuir de qualité. La Air Force 1 reste indétrônable.', '2025-02-28 00:00:00', 2, 2),
(8, 4, 'Bon rapport qualité/prix. La semelle chunky est vraiment originale.', '2025-06-02 00:00:00', 12, 1),
(9, 5, 'Passable', '2026-06-04 05:52:01', 6, 6),
(10, 2, 'azeazeaze', '2026-06-04 05:52:30', 5, 6);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `Id_categorie` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`Id_categorie`, `libelle`) VALUES
(1, 'Sneakers'),
(2, 'Running'),
(3, 'Skate'),
(4, 'Lifestyle'),
(5, 'Montante');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `Id_commande` int(11) NOT NULL,
  `date_commande` datetime DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `montant_total` decimal(15,2) DEFAULT NULL,
  `remise` int(11) DEFAULT NULL,
  `Id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`Id_commande`, `date_commande`, `statut`, `montant_total`, `remise`, `Id_utilisateur`) VALUES
(1, '2025-01-10 14:32:00', 'livree', 130.29, 0, 1),
(2, '2025-02-14 09:15:00', 'livree', 164.98, 0, 1),
(3, '2025-03-22 17:45:00', 'expediee', 190.29, 0, 2),
(4, '2025-04-05 11:20:00', 'en_attente', 149.98, 10, 3),
(5, '2025-04-18 16:10:00', 'annulee', 74.99, 0, 4),
(6, '2025-05-02 10:00:00', 'livree', 249.99, 0, 2),
(7, '2025-05-15 13:30:00', 'expediee', 160.29, 5, 5),
(8, '2025-06-01 08:50:00', 'en_attente', 189.98, 0, 1),
(9, '2026-06-04 02:03:49', 'en_attente', 100.59, 0, 6),
(10, '2026-06-04 02:04:23', 'en_attente', 300.28, 0, 6),
(11, '2026-06-04 03:29:27', 'en_attente', 64.99, 0, 6),
(12, '2026-06-04 04:00:09', 'en_attente', 255.87, 0, 6),
(13, '2026-06-04 04:01:35', 'en_attente', 649.95, 0, 6),
(14, '2026-06-04 04:35:41', 'en_attente', 190.29, 0, 6);

-- --------------------------------------------------------

--
-- Structure de la table `couleur`
--

CREATE TABLE `couleur` (
  `Id_couleur` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `couleur`
--

INSERT INTO `couleur` (`Id_couleur`, `libelle`) VALUES
(1, 'Blanc'),
(2, 'Noir'),
(3, 'Rouge'),
(4, 'Bleu marine'),
(5, 'Gris'),
(6, 'Vert olive'),
(7, 'Beige'),
(8, 'Dégradé noir/blanc');

-- --------------------------------------------------------

--
-- Structure de la table `exemplaire`
--

CREATE TABLE `exemplaire` (
  `Id_exemplaire` int(11) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  `Id_modèle` int(11) NOT NULL,
  `Id_taille` int(11) NOT NULL,
  `Id_couleur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exemplaire`
--

INSERT INTO `exemplaire` (`Id_exemplaire`, `quantite`, `Id_modèle`, `Id_taille`, `Id_couleur`) VALUES
(1, 0, 1, 5, 1),
(2, 4, 1, 6, 1),
(3, 6, 1, 7, 1),
(4, 3, 1, 8, 1),
(5, 5, 1, 5, 2),
(6, 4, 1, 6, 2),
(7, 7, 1, 7, 2),
(8, 3, 1, 8, 2),
(9, 10, 2, 4, 1),
(10, 7, 2, 5, 1),
(11, 12, 2, 6, 1),
(12, 7, 2, 7, 1),
(13, 5, 2, 8, 1),
(14, 4, 3, 5, 4),
(15, 6, 3, 6, 4),
(16, 5, 3, 7, 4),
(17, 3, 3, 8, 4),
(18, 4, 3, 5, 2),
(19, 5, 3, 6, 2),
(20, 4, 3, 7, 2),
(21, 6, 4, 4, 1),
(22, 9, 4, 5, 1),
(23, 7, 4, 6, 1),
(24, 5, 4, 7, 1),
(25, 4, 4, 5, 6),
(26, 3, 4, 6, 6),
(27, 2, 4, 7, 6),
(28, 5, 5, 4, 8),
(29, 8, 5, 5, 8),
(30, 6, 5, 6, 8),
(31, 4, 5, 7, 8),
(32, 2, 5, 8, 8),
(33, 4, 6, 5, 2),
(34, 5, 6, 6, 2),
(35, 6, 6, 7, 2),
(36, 3, 6, 8, 2),
(37, 2, 6, 9, 2),
(38, 4, 6, 5, 4),
(39, 6, 6, 6, 4),
(40, 4, 6, 7, 4),
(41, 7, 7, 4, 5),
(42, 9, 7, 5, 5),
(43, 8, 7, 6, 5),
(44, 6, 7, 7, 5),
(45, 4, 7, 5, 4),
(46, 5, 7, 6, 4),
(47, 0, 7, 7, 4),
(48, 3, 8, 5, 5),
(49, 4, 8, 6, 5),
(50, 5, 8, 7, 5),
(51, 2, 8, 8, 5),
(52, 1, 8, 9, 5),
(53, 10, 9, 3, 2),
(54, 12, 9, 4, 2),
(55, 15, 9, 5, 2),
(56, 11, 9, 6, 2),
(57, 8, 9, 7, 2),
(58, 6, 9, 3, 8),
(59, 8, 9, 4, 8),
(60, 9, 9, 5, 8),
(61, 5, 10, 4, 2),
(62, 7, 10, 5, 2),
(63, 6, 10, 6, 2),
(64, 4, 10, 7, 2),
(65, 3, 10, 4, 1),
(66, 5, 10, 5, 1),
(67, 4, 10, 6, 1),
(68, 15, 11, 2, 1),
(69, 18, 11, 3, 1),
(70, 20, 11, 4, 1),
(71, 22, 11, 5, 1),
(72, 18, 11, 6, 1),
(73, 10, 11, 2, 2),
(74, 12, 11, 3, 2),
(75, 15, 11, 4, 2),
(76, 18, 11, 5, 2),
(77, 14, 11, 6, 2),
(78, 4, 11, 2, 3),
(79, 6, 11, 3, 3),
(80, 8, 11, 4, 3),
(81, 5, 12, 2, 7),
(82, 7, 12, 3, 7),
(83, 6, 12, 4, 7),
(84, 5, 12, 5, 7),
(85, 3, 12, 2, 2),
(86, 4, 12, 3, 2),
(87, 5, 12, 4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_commande`
--

CREATE TABLE `ligne_commande` (
  `Id_ligne_commande` int(11) NOT NULL,
  `quantite` int(11) DEFAULT NULL,
  `prix_unitaire` decimal(15,2) DEFAULT NULL,
  `Id_exemplaire` int(11) NOT NULL,
  `Id_commande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ligne_commande`
--

INSERT INTO `ligne_commande` (`Id_ligne_commande`, `quantite`, `prix_unitaire`, `Id_exemplaire`, `Id_commande`) VALUES
(1, 1, 130.29, 3, 1),
(2, 1, 89.99, 9, 2),
(3, 1, 74.99, 30, 2),
(4, 1, 190.29, 19, 3),
(5, 1, 64.99, 38, 4),
(6, 1, 64.99, 43, 4),
(7, 1, 74.99, 29, 5),
(8, 1, 249.99, 25, 6),
(9, 1, 159.99, 12, 7),
(10, 1, 64.99, 37, 7),
(11, 2, 94.99, 63, 8),
(12, 1, 100.59, 32, 9),
(13, 1, 109.99, 10, 10),
(14, 1, 190.29, 35, 10),
(15, 1, 64.99, 78, 11),
(16, 3, 85.29, 47, 12),
(17, 5, 129.99, 1, 13),
(18, 1, 190.29, 40, 14);

-- --------------------------------------------------------

--
-- Structure de la table `marque`
--

CREATE TABLE `marque` (
  `Id_marque` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `marque`
--

INSERT INTO `marque` (`Id_marque`, `libelle`) VALUES
(1, 'Nike'),
(2, 'Adidas'),
(3, 'New Balance'),
(4, 'Vans'),
(5, 'Converse');

-- --------------------------------------------------------

--
-- Structure de la table `modèle`
--

CREATE TABLE `modèle` (
  `Id_modèle` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT NULL,
  `prix` decimal(15,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `libelle` varchar(50) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `Id_categorie` int(11) NOT NULL,
  `Id_marque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modèle`
--

INSERT INTO `modèle` (`Id_modèle`, `nom`, `actif`, `prix`, `description`, `libelle`, `image`, `Id_categorie`, `Id_marque`) VALUES
(1, 'Air Max 90', 1, 129.99, 'Icône intemporelle du streetwear. Tige en mesh et cuir, amorti Air visible.', 'AM90', 'https://static.nike.com/a/images/t_default/u_9ddf04c7-2a9a-4d76-add1-d15af8f0263d,c_scale,fl_relative,w_1.0,h_1.0,fl_layer_apply/wzitsrb4oucx9jukxsmc/AIR+MAX+90.png', 1, 1),
(2, 'Air Force 1 Low', 1, 109.99, 'La chaussure la plus vendue de tous les temps. Silhouette basse en cuir pleine fleur.', 'AF1', 'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/b7d9211c-26e7-431a-ac24-b0540fb3c00f/air-force-1-07-shoes-WjJFNk.png', 4, 1),
(3, 'React Infinity', 1, 159.99, 'Conçue pour réduire les blessures. Semelle React ultra-souple et réactive.', 'RI', 'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/i1-665455a5-45de-40fb-945f-c1852b82400d/react-infinity-run-flyknit-3-road-running-shoes-zX42Nc.png', 2, 1),
(4, 'Stan Smith', 1, 89.99, 'Le classique du tennis devenu icône mode. Tige cuir lisse, trois bandes perforées.', 'SS', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/68ae7ea7849b43eca70aac1e00f5146d_9366/Stan_Smith_Shoes_White_FX5502_01_standard.jpg', 4, 2),
(5, 'Superstar', 1, 99.99, 'La légendaire basket aux embouts coquille. Tige cuir, semelle dentée caoutchouc.', 'SSTAR', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7ed0855435194229a525aad6009a0497_9366/Superstar_Shoes_White_EG4958_01_standard.jpg', 1, 2),
(6, 'Ultraboost 23', 1, 189.99, 'La référence running Adidas. Tige Primeknit+, semelle Boost haute restitution.', 'UB23', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/fbaf991a78bc4896a3e9ad7800abcec6_9366/Ultraboost_Light_Running_Shoes_Black_HQ6351_01_standard.jpg', 2, 2),
(7, '574', 1, 84.99, 'Référence lifestyle depuis les années 80. Mesh et suède, semelle ENCAP.', 'NB574', 'https://nb.scene7.com/is/image/NB/ml574evg_nb_02_i?$&qlt=80&fmt=webp&wid=440&hei=440', 4, 3),
(8, '990v6', 1, 249.99, 'Fabriquée aux USA. Le summum du confort, tige mesh et daim premium.', 'NB990', 'https://nb.scene7.com/is/image/NB/m990gl6_nb_02_i?$&qlt=80&fmt=webp&wid=440&hei=440', 2, 3),
(9, 'Old Skool', 1, 74.99, 'Première chaussure avec le Sidestripe Vans. Toile et suède, semelle Waffle.', 'OS', 'https://assets.vans.com/images/t_Thumbnail/v1747942442/VN000D3HY28-HERO/Old-Skool-Shoe-VANS-Black-White-HERO.png', 3, 4),
(10, 'Sk8-Hi', 1, 84.99, 'Montante emblématique du skate. Canvas et suède, col matelassé pour la cheville.', 'SK8', 'https://assets.vans.com/images/t_Thumbnail/v1753918019/VN000TS9BJ4-HERO/Sk8Hi-Shoe-VANS-Black-HERO.png', 5, 4),
(11, 'Chuck Taylor', 1, 64.99, 'La sneaker originale depuis 1917. Toile, embout caoutchouc, semelle Ortholite.', 'CT', 'https://static.nike.com/a/images/t_default/u_9ddf04c7-2a9a-4d76-add1-d15af8f0263d,c_scale,fl_relative,w_1.0,h_1.0,fl_layer_apply/toqshuuheqdl3ljzobnc/ALL+STAR+HI+BLACK.png', 1, 5),
(12, 'Run Star Hike', 1, 94.99, 'Chuck Taylor revisitée plateforme XXL. Toile, semelle crantée chunky.', 'RSH', 'https://www.converse.com/dw/image/v2/BCZC_PRD/on/demandware.static/-/Sites-cnv-master-catalog/default/dwabd90c97/images/a_107/166800C_A_107X1.jpg?sw=406&strip=false', 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `Id_panier` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `Id_utilisateur` int(11) NOT NULL,
  `Id_exemplaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`Id_panier`, `quantite`, `Id_utilisateur`, `Id_exemplaire`) VALUES
(8, 3, 6, 80),
(9, 1, 6, 40),
(10, 1, 7, 29);

-- --------------------------------------------------------

--
-- Structure de la table `taille`
--

CREATE TABLE `taille` (
  `Id_taille` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL,
  `augmentation_prix` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `taille`
--

INSERT INTO `taille` (`Id_taille`, `libelle`, `augmentation_prix`) VALUES
(1, '36', 0.00),
(2, '37', 0.00),
(3, '38', 0.00),
(4, '39', 0.00),
(5, '40', 0.00),
(6, '41', 0.30),
(7, '42', 0.30),
(8, '43', 0.60),
(9, '44', 0.60),
(10, '45', 1.00);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `Id_utilisateur` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `code_postal` varchar(50) DEFAULT NULL,
  `pays` varchar(50) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`Id_utilisateur`, `nom`, `prenom`, `email`, `password`, `adresse`, `code_postal`, `pays`, `ville`) VALUES
(1, 'Dupont', 'Marie', 'marie.dupont@email.fr', '5a881a4bde334b18a94a5d18e76d209df9ec00756e33297d4ae7c1d24d2a28a6', '12 rue des Lilas', '75011', 'France', 'Paris'),
(2, 'Martin', 'Lucas', 'lucas.martin@email.fr', '5a881a4bde334b18a94a5d18e76d209df9ec00756e33297d4ae7c1d24d2a28a6', '5 avenue Foch', '69006', 'France', 'Lyon'),
(3, 'Bernard', 'Sophie', 'sophie.bernard@email.fr', '5a881a4bde334b18a94a5d18e76d209df9ec00756e33297d4ae7c1d24d2a28a6', '8 rue Paradis', '13001', 'France', 'Marseille'),
(4, 'Leroy', 'Thomas', 'thomas.leroy@email.fr', '5a881a4bde334b18a94a5d18e76d209df9ec00756e33297d4ae7c1d24d2a28a6', '3 place du Capitole', '31000', 'France', 'Toulouse'),
(5, 'Moreau', 'Julie', 'julie.moreau@email.fr', '5a881a4bde334b18a94a5d18e76d209df9ec00756e33297d4ae7c1d24d2a28a6', '17 rue de la Paix', '06000', 'France', 'Nice'),
(6, 'Doe', 'Joe', 'Joe@gmail.com', '291a741e67088bc2ca3295910e5241253bbe495916a7cb908c2203ba6b0650a5', '1 rue du Test', '00000', 'France', 'Paris'),
(7, 'Fellous', 'Rayan', 'fellousrayan@gmail.com', 'dc606d34c79ebfa884aedc9990e6501becb5de01758e2e924bc85c09390ba2ea', '12 Rue du lion', '34543', 'France', 'Paris');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`Id_avis`),
  ADD KEY `Id_modèle` (`Id_modèle`),
  ADD KEY `Id_utilisateur` (`Id_utilisateur`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`Id_categorie`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`Id_commande`),
  ADD KEY `Id_utilisateur` (`Id_utilisateur`);

--
-- Index pour la table `couleur`
--
ALTER TABLE `couleur`
  ADD PRIMARY KEY (`Id_couleur`);

--
-- Index pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD PRIMARY KEY (`Id_exemplaire`),
  ADD KEY `Id_modèle` (`Id_modèle`),
  ADD KEY `Id_taille` (`Id_taille`),
  ADD KEY `Id_couleur` (`Id_couleur`);

--
-- Index pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
  ADD PRIMARY KEY (`Id_ligne_commande`),
  ADD KEY `Id_exemplaire` (`Id_exemplaire`),
  ADD KEY `Id_commande` (`Id_commande`);

--
-- Index pour la table `marque`
--
ALTER TABLE `marque`
  ADD PRIMARY KEY (`Id_marque`);

--
-- Index pour la table `modèle`
--
ALTER TABLE `modèle`
  ADD PRIMARY KEY (`Id_modèle`),
  ADD KEY `Id_categorie` (`Id_categorie`),
  ADD KEY `Id_marque` (`Id_marque`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`Id_panier`),
  ADD KEY `Id_utilisateur` (`Id_utilisateur`),
  ADD KEY `Id_exemplaire` (`Id_exemplaire`);

--
-- Index pour la table `taille`
--
ALTER TABLE `taille`
  ADD PRIMARY KEY (`Id_taille`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`Id_utilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `Id_avis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `Id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `Id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `couleur`
--
ALTER TABLE `couleur`
  MODIFY `Id_couleur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  MODIFY `Id_exemplaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
  MODIFY `Id_ligne_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `marque`
--
ALTER TABLE `marque`
  MODIFY `Id_marque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `modèle`
--
ALTER TABLE `modèle`
  MODIFY `Id_modèle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `Id_panier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `taille`
--
ALTER TABLE `taille`
  MODIFY `Id_taille` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `Id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`Id_modèle`) REFERENCES `modèle` (`Id_modèle`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`);

--
-- Contraintes pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD CONSTRAINT `exemplaire_ibfk_1` FOREIGN KEY (`Id_modèle`) REFERENCES `modèle` (`Id_modèle`),
  ADD CONSTRAINT `exemplaire_ibfk_2` FOREIGN KEY (`Id_taille`) REFERENCES `taille` (`Id_taille`),
  ADD CONSTRAINT `exemplaire_ibfk_3` FOREIGN KEY (`Id_couleur`) REFERENCES `couleur` (`Id_couleur`);

--
-- Contraintes pour la table `ligne_commande`
--
ALTER TABLE `ligne_commande`
  ADD CONSTRAINT `ligne_commande_ibfk_1` FOREIGN KEY (`Id_exemplaire`) REFERENCES `exemplaire` (`Id_exemplaire`),
  ADD CONSTRAINT `ligne_commande_ibfk_2` FOREIGN KEY (`Id_commande`) REFERENCES `commande` (`Id_commande`);

--
-- Contraintes pour la table `modèle`
--
ALTER TABLE `modèle`
  ADD CONSTRAINT `modèle_ibfk_1` FOREIGN KEY (`Id_categorie`) REFERENCES `categorie` (`Id_categorie`),
  ADD CONSTRAINT `modèle_ibfk_2` FOREIGN KEY (`Id_marque`) REFERENCES `marque` (`Id_marque`);

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`Id_utilisateur`) REFERENCES `utilisateur` (`Id_utilisateur`),
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`Id_exemplaire`) REFERENCES `exemplaire` (`Id_exemplaire`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
