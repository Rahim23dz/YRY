-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 31 mai 2025 à 07:08
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
-- Base de données : `yry`
--

-- --------------------------------------------------------

--
-- Structure de la table `address`
--

CREATE TABLE `address` (
  `id_address` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vendeur_id` int(11) DEFAULT NULL,
  `commune` varchar(100) DEFAULT NULL,
  `adresse_exacte` text DEFAULT NULL,
  `id_wilaya` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `address`
--

INSERT INTO `address` (`id_address`, `user_id`, `vendeur_id`, `commune`, `adresse_exacte`, `id_wilaya`) VALUES
(2, NULL, NULL, NULL, 'azasa', 23),
(3, NULL, NULL, NULL, 'esefefef', 8),
(4, NULL, NULL, NULL, 'qatarr', 23),
(5, NULL, NULL, NULL, '128 logements bloc v01 n°16 oued dheb 2 Annaba', 1),
(6, NULL, NULL, NULL, '128 logements bloc v01 n°16 oued dheb 2 Annaba', 1),
(23, NULL, NULL, NULL, 'qqqqqqqqqqqq', 23),
(24, NULL, NULL, NULL, 'ddddddddddddddd', 2),
(25, NULL, NULL, NULL, 'ddddddddddd', 1),
(26, NULL, NULL, NULL, 'ddxzcv', 1),
(27, NULL, NULL, NULL, 'ddddddddaaaaaa', 1),
(28, NULL, NULL, NULL, 'sdfsef', 17),
(29, NULL, NULL, NULL, 'gdffrg', 1),
(30, NULL, NULL, NULL, 'qqqqqqqqqqrg', 1),
(31, NULL, NULL, NULL, 'qqqqqqqqqqqqesfdfe', 1),
(32, NULL, NULL, NULL, 'sefdsf', 52),
(33, NULL, NULL, NULL, 'ESFDSF', 34),
(34, NULL, NULL, NULL, 'qww', 1),
(35, NULL, NULL, NULL, 'qqq', 1),
(36, 22, NULL, NULL, 'rim', 23),
(37, NULL, 16, NULL, 'bouni', 23);

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'rahal', 'rahal@gmail.com', 'rahal', 'admin', '2025-05-26 01:05:41', '2025-05-26 01:05:41');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom_categorie`) VALUES
(1, 'suspension'),
(2, 'freinage'),
(3, 'moteur'),
(4, 'divers'),
(5, 'Embrayage et Boîte de vitesse'),
(6, 'Optiques / Phares / Ampoules'),
(7, 'Essuie-glaces et pièces'),
(9, 'Pneus et Equipements Roue'),
(10, 'Chauffage et Climatisation'),
(11, 'Démarrage électrique');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(20) DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `id_user`, `total`, `date_commande`, `statut`) VALUES
(44, 22, 8700.00, '2025-05-31 03:19:53', 'accepté');

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_detail_commande` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_detail_commande`, `id_commande`, `id_product`, `quantite`, `total`) VALUES
(60, 44, 195, 1, 8700.00);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_commande` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `id_user`, `id_commande`, `message`, `lu`, `date_creation`) VALUES
(15, 22, 44, 'Commande accepté', 1, '2025-05-31 04:20:26');

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('en attente','en cours','livré','annulé') DEFAULT 'en attente',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order_details`
--

CREATE TABLE `order_details` (
  `id_order_detail` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id_panier` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantite` int(11) DEFAULT 1,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `categorie` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `id_vendeur` int(11) NOT NULL,
  `id_sous_type` int(11) DEFAULT NULL,
  `id_vehicule` int(11) NOT NULL,
  `etat` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id_product`, `nom`, `description`, `categorie`, `prix`, `image`, `stock`, `id_vendeur`, `id_sous_type`, `id_vehicule`, `etat`) VALUES
(195, 'Amortisseur Avant Toyota Corolla', 'Amortisseur avant d\'origine pour Toyota Corolla 2009-2013', 1, 8700.00, 'Amortisseu_%20Avant_Toyota_Corolla.jpg', 10, 16, 1, 85, 8),
(196, 'Disque de frein avant Peugeot 207', 'Disque de frein ventilé compatible avec Peugeot 207', 2, 5400.00, 'Disque de frein avant Peugeot207.jpg', 5, 16, 7, 71, 3),
(197, 'Filtre à huile Renault Clio 4', 'Filtre à huile haute qualité pour Renault Clio 4', 3, 1200.00, 'filtre_huile_renault_clio4.jpg', 20, 16, 11, 74, 1),
(198, 'Bougie d\'allumage Denso Hyundai i10', 'Bougie d\'allumage en iridium haute performance pour Hyundai i10', 3, 950.00, 'bougie_allumage_hyundai_i10.jpg', 15, 16, 12, 15, 1),
(199, 'Amortisseur Avant Volkswagen Golf 4', 'Amortisseur avant pour Volkswagen Golf 4', 1, 9200.00, 'amorisseur_vw_golf4.jpg', 8, 16, 1, 87, 7),
(200, 'Kit de frein Opel Astra', 'Kit complet de frein pour Opel Astra', 2, 7000.00, 'kit_frein_opel_astra.jpg', 12, 16, 8, 23, 6),
(201, 'Batterie Bosch 12V pour Nissan Sentra', 'Batterie de démarrage Bosch S4 12V 60Ah pour Nissan Sentra', 3, 15200.00, 'batterie_bosch_nissan_sentra.jpg', 6, 16, 13, 21, 1),
(202, 'Amortisseur Arrière BMW Série 3', 'Amortisseur arrière compatible BMW Série 3 (E90)', 1, 12500.00, 'Amortisseur Arrière BMW Série 3.jpg', 4, 16, 1, 10, 8),
(203, 'Filtre à air Peugeot 206', 'Filtre à air haute performance pour Peugeot 206', 3, 1800.00, 'filtre_air_peugeot_206.jpg', 30, 16, 11, 25, 1),
(204, 'Disque de frein arrière Seat Ibiza', 'Disque de frein arrière compatible Seat Ibiza', 2, 5600.00, 'Disque de frein arrière Seat Ibiza.jpg', 7, 16, 7, 80, 4),
(205, 'Bougie d\'allumage Toyota Yaris', 'Bougie d\'allumage iridium pour Toyota Yaris', 3, 1050.00, 'bougie_allumage_toyota_yaris.jpg', 18, 16, 12, 86, 1),
(206, 'Amortisseur Avant Hyundai Elantra', 'Amortisseur avant de qualité OEM pour Hyundai Elantra', 1, 8900.00, 'amortisseur_hyundai_elantra.jpg', 9, 16, 1, 14, 7),
(207, 'Kit chaîne distribution Renault Symbol', 'Kit chaîne distribution complet pour Renault Symbol', 3, 9500.00, 'kit_chaine_renault_symbol.jpg', 5, 16, 14, 75, 2),
(208, 'Disque de frein avant Suzuki Swift', 'Disque de frein ventilé pour Suzuki Swift', 2, 5300.00, 'disque_frein_suzuki_swift.jpg', 11, 16, 7, 83, 5),
(209, 'Filtre à carburant Fiat Punto', 'Filtre à carburant pour Fiat Punto', 3, 1300.00, 'Filtre_a_carburant_Fiat_Punto.jpg', 22, 16, 15, 8, 1),
(210, 'Disque de frein arrière Nissan Micra', 'Disque de frein arrière ventilé pour Nissan Micra', 2, 5200.00, 'disque_frein_nissan_micra.jpg', 6, 16, 7, 20, 4),
(211, 'Filtre à carburant Fiat Tipo', 'Filtre à carburant haute qualité compatible Fiat Tipo', 3, 1400.00, 'filtre_carburant_fiat_tipo.jpg', 15, 16, 15, 9, 1),
(212, 'Bougie d\'allumage Suzuki Alto', 'Bougie d\'allumage longue durée pour Suzuki Alto', 3, 900.00, 'bougie_allumage_suzuki_alto.jpg', 12, 16, 12, 82, 1),
(213, 'Amortisseur arrière Kia Rio', 'Amortisseur arrière d\'origine pour Kia Rio', 1, 9200.00, 'amortisseur_arriere_kia_rio.jpg', 5, 16, 1, 17, 7),
(214, 'Kit plaquettes de frein Peugeot 208', 'Kit complet plaquettes de frein pour Peugeot 208', 2, 6100.00, 'kit_plaquettes_peugeot_208.jpg', 9, 16, 14, 72, 5),
(215, 'Filtre à air Hyundai Accent', 'Filtre à air haute performance compatible Hyundai Accent', 3, 1700.00, 'filtre_air_hyundai_accent.jpg', 20, 16, 11, 13, 1),
(216, 'Batterie 12V 70Ah Bosch pour Renault Clio 4', 'Batterie Bosch S5 12V 70Ah compatible Renault Clio 4', 3, 15800.00, 'batterie_bosch_renault_clio4.jpg', 4, 16, 13, 74, 1),
(217, 'Disque de frein avant Ford Focus', 'Disque de frein avant ventilé pour Ford Focus', 2, 5700.00, 'disque_frein_ford_focus.jpg', 7, 16, 7, 10, 5),
(218, 'Amortisseur avant Seat Leon', 'Amortisseur avant OEM pour Seat Leon', 1, 8900.00, 'amortisseur_avant_seat_leon.jpg', 8, 16, 1, 81, 8),
(219, 'Kit chaîne distribution Peugeot 301', 'Kit complet chaîne distribution Peugeot 301', 3, 9800.00, 'kit_chaine_peugeot_301.jpg', 6, 16, 14, 73, 2);

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `date_review` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sous_types`
--

CREATE TABLE `sous_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `id_category` int(11) DEFAULT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sous_types`
--

INSERT INTO `sous_types` (`id`, `name`, `id_category`, `slug`) VALUES
(1, 'Amortisseurs', 1, 'amortisseur.php'),
(2, 'Suspension d’essieux', 1, 'Suspension_d_essieux.php'),
(3, 'Transmission', 1, 'transmission.php'),
(4, 'Rotules / Direction', 1, 'rotule.php'),
(5, 'Moyeux et roulements', 1, 'roulement.php'),
(6, 'Plaquettes de frein', 2, 'Plaquettes_de_frein.php'),
(7, 'Disques de frein', 2, 'Disques_de_frein.php'),
(8, 'Hydraulique', 2, 'hydraulique.php'),
(9, 'Freins à tambours', 2, 'freintomb.php'),
(10, 'Capteurs et câbles de freinage', 2, 'capteur.php'),
(11, 'Assistance au freinage', 2, 'assistance.php'),
(12, 'Outils freinage', 2, 'outils.php'),
(13, 'Filtre à huile', 3, 'filtre_huile.php'),
(14, 'Batterie 12V', 3, 'batterie.php'),
(15, 'Huile moteur 5W-30', 3, 'huile.php'),
(16, 'Filtre à air', 3, 'filtre_a_air.php'),
(17, 'Kit de courroie de distribution', 3, 'kit_de_courroie.php'),
(18, 'Kit_de_bougies', 4, 'bougie.php'),
(19, 'Pompe à eau', 4, 'pompe_eau.php'),
(20, 'Boîtier de filtre à carburant', 4, 'boitier_de_filtre.php'),
(21, 'Rétroviseur extérieur', 4, 'retroviseur.php'),
(22, 'Silencieux', 4, 'silencieux.php'),
(36, 'Pompes', 3, 'pompes'),
(37, 'Supports moteur', 3, 'supports-moteur'),
(38, 'Courroies et Distribution', 3, 'courroies-et-distribution'),
(39, 'Refroidissement moteur', 3, 'refroidissement-moteur'),
(40, 'Injection carburation', 3, 'injection-carburation'),
(41, 'Capteurs et câbles moteur', 3, 'capteurs-et-cables-moteur'),
(42, 'Turbo', 3, 'turbo'),
(47, 'Direction', 1, 'direction'),
(48, 'Butées', 1, 'butees'),
(49, 'Ressorts et Soufflets', 1, 'ressorts-et-soufflets'),
(50, 'Embrayage et Volant-moteur', 5, 'embrayage-et-volant-moteur'),
(51, 'Accessoires de boîte de vitesse', 5, 'accessoires-boite-vitesse'),
(52, 'Autres pièces d\'Embrayage', 5, 'autres-pieces-embrayage'),
(53, 'Optiques et Phares', 6, 'optiques-et-phares'),
(54, 'Ampoules, Éclairage avant', 6, 'ampoules-eclairage-avant'),
(55, 'Ampoules, Éclairage arrière', 6, 'ampoules-eclairage-arriere'),
(56, 'Ampoules, Éclairage intérieur et signalisation', 6, 'ampoules-eclairage-interieur-signalisation'),
(57, 'Moteur d\'essuie-glace', 7, 'moteur-essuie-glace'),
(58, 'Pompe de lave-glace', 7, 'pompe-lave-glace'),
(59, 'Lave-glace', 7, 'lave-glace'),
(60, 'Balai d\'essuie-glace', 7, 'balai-essuie-glace'),
(61, 'Pneumatiques', 9, 'pneumatiques'),
(62, 'Chaînes-neiges et Equipements Roue', 9, 'chaines-neiges-equipements-roue'),
(63, 'Outils pneu', 9, 'outils-pneu'),
(64, 'Climatisation', 10, 'climatisation'),
(65, 'Chauffage et Ventilation', 10, 'chauffage-et-ventilation'),
(67, 'Alternateurs', 11, 'alternateurs'),
(68, 'Démarreurs', 11, 'demarreurs'),
(69, 'Outils Batteries', 11, 'outils-batteries');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_wilaya` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `phone`, `profile_image`, `role`, `date_inscription`, `id_wilaya`) VALUES
(22, 'rahim', 'tghachi7@gmail.com', '$2y$10$/QKU9tkPc50rUWu2Nq5hQuMCsU92fFIuMUGGqK29e7xiqwdQMxIkG', '0557004569', '683a21d9cc51d_photo_2025-05-30_22-13-32rahim.jpg', 'client', '2025-05-30 21:22:05', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id_vehicule` int(11) NOT NULL,
  `marque` varchar(50) DEFAULT NULL,
  `modele` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id_vehicule`, `marque`, `modele`) VALUES
(1, 'Chery', 'Fulwin'),
(2, 'Chery', 'QQ'),
(3, 'Citroën', 'C-Elysée'),
(4, 'Citroën', 'C4'),
(5, 'Daewoo', 'Matiz'),
(6, 'Daewoo', 'Nubira'),
(7, 'Dacia', 'Logan'),
(8, 'Fiat', 'Punto'),
(9, 'Fiat', 'Tipo'),
(10, 'Ford', 'Focus'),
(11, 'Geely', 'CK'),
(12, 'Great Wall', 'Voleex'),
(13, 'Hyundai', 'Accent'),
(14, 'Hyundai', 'Elantra'),
(15, 'Hyundai', 'i10'),
(16, 'Kia', 'Picanto'),
(17, 'Kia', 'Rio'),
(18, 'Lada', 'Samara'),
(19, 'Mazda', '3'),
(20, 'Nissan', 'Micra'),
(21, 'Nissan', 'Sentra'),
(22, 'Nissan', 'Sunny'),
(23, 'Opel', 'Astra'),
(24, 'Opel', 'Corsa'),
(25, 'Peugeot', '206'),
(71, 'Peugeot', '207'),
(72, 'Peugeot', '208'),
(73, 'Peugeot', '301'),
(74, 'Renault', 'Clio 4'),
(75, 'Renault', 'Symbol'),
(76, 'Skoda', 'Fabia'),
(77, 'Skoda', 'Octavia'),
(78, 'Skoda', 'Rapid'),
(79, 'Seat', 'Cordoba'),
(80, 'Seat', 'Ibiza'),
(81, 'Seat', 'Leon'),
(82, 'Suzuki', 'Alto'),
(83, 'Suzuki', 'Swift'),
(84, 'Toyota', 'Avensis'),
(85, 'Toyota', 'Corolla'),
(86, 'Toyota', 'Yaris'),
(87, 'Volkswagen', 'Golf 4'),
(88, 'Volkswagen', 'Polo');

-- --------------------------------------------------------

--
-- Structure de la table `vendeurs`
--

CREATE TABLE `vendeurs` (
  `id_vendeur` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `store_address` varchar(255) DEFAULT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_wilaya` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'refuse',
  `recus` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vendeurs`
--

INSERT INTO `vendeurs` (`id_vendeur`, `username`, `email`, `password`, `phone`, `profile_image`, `store_name`, `store_address`, `date_inscription`, `id_wilaya`, `status`, `recus`) VALUES
(16, 'yacine', 'myacine@gmail.com', '$2y$10$ft5MTV4jiMKLGQ1fSR0JDu4qhiSAChl7kJted/fv8QCgctTqehliW', '0556800457', 'profile_683a2284d6a982.92079261.jpg', 'yacine_auto', 'el bouni avant hadje grifa', '2025-05-30 21:26:28', 23, 'accepte', 'uploads/recus/1748640428_recus.png');

-- --------------------------------------------------------

--
-- Structure de la table `wilaya`
--

CREATE TABLE `wilaya` (
  `id_wilaya` int(11) NOT NULL,
  `nom_wilaya` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `wilaya`
--

INSERT INTO `wilaya` (`id_wilaya`, `nom_wilaya`) VALUES
(1, 'Adrar'),
(2, 'Chlef'),
(3, 'Laghouat'),
(4, 'Oum El Bouaghi'),
(5, 'Batna'),
(6, 'Béjaïa'),
(7, 'Biskra'),
(8, 'Béchar'),
(9, 'Blida'),
(10, 'Bouira'),
(11, 'Tamanrasset'),
(12, 'Tébessa'),
(13, 'Tlemcen'),
(14, 'Tiaret'),
(15, 'Tizi Ouzou'),
(16, 'Alger'),
(17, 'Djelfa'),
(18, 'Jijel'),
(19, 'Sétif'),
(20, 'Saïda'),
(21, 'Skikda'),
(22, 'Sidi Bel Abbès'),
(23, 'Annaba'),
(24, 'Guelma'),
(25, 'Constantine'),
(26, 'Médéa'),
(27, 'Mostaganem'),
(28, 'M\'Sila'),
(29, 'Mascara'),
(30, 'Ouargla'),
(31, 'Oran'),
(32, 'El Bayadh'),
(33, 'Illizi'),
(34, 'Bordj Bou Arreridj'),
(35, 'Boumerdès'),
(36, 'El Tarf'),
(37, 'Tindouf'),
(38, 'Tissemsilt'),
(39, 'El Oued'),
(40, 'Khenchela'),
(41, 'Souk Ahras'),
(42, 'Tipaza'),
(43, 'Mila'),
(44, 'Aïn Defla'),
(45, 'Naâma'),
(46, 'Aïn Témouchent'),
(47, 'Ghardaïa'),
(48, 'Relizane'),
(49, 'Timimoun'),
(50, 'Bordj Badji Mokhtar'),
(51, 'Ouled Djellal'),
(52, 'Béni Abbès'),
(53, 'In Salah'),
(54, 'In Guezzam'),
(55, 'Touggourt'),
(56, 'Djanet'),
(57, 'El M\'Ghair'),
(58, 'El Meniaa');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id_address`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_vendeur` (`vendeur_id`);

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_detail_commande`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_product` (`id_product`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_commande` (`id_commande`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id_order_detail`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_product` (`id_product`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id_panier`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_product` (`id_product`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `id_vendeur` (`id_vendeur`),
  ADD KEY `fk_sous_type` (`id_sous_type`),
  ADD KEY `fk_vehicule` (`id_vehicule`),
  ADD KEY `fk_category_products` (`categorie`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `sous_types`
--
ALTER TABLE `sous_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_category` (`id_category`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_wilaya` (`id_wilaya`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id_vehicule`);

--
-- Index pour la table `vendeurs`
--
ALTER TABLE `vendeurs`
  ADD PRIMARY KEY (`id_vendeur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_vendeurs_wilaya` (`id_wilaya`);

--
-- Index pour la table `wilaya`
--
ALTER TABLE `wilaya`
  ADD PRIMARY KEY (`id_wilaya`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `address`
--
ALTER TABLE `address`
  MODIFY `id_address` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_detail_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id_order_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id_panier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sous_types`
--
ALTER TABLE `sous_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id_vehicule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT pour la table `vendeurs`
--
ALTER TABLE `vendeurs`
  MODIFY `id_vendeur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_vendeur` FOREIGN KEY (`vendeur_id`) REFERENCES `vendeurs` (`id_vendeur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vendeur_new` FOREIGN KEY (`vendeur_id`) REFERENCES `vendeurs` (`id_vendeur`);

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `details_commande_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `details_commande_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE SET NULL;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Contraintes pour la table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`);

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`);

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_products` FOREIGN KEY (`categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sous_type` FOREIGN KEY (`id_sous_type`) REFERENCES `sous_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vehicule` FOREIGN KEY (`id_vehicule`) REFERENCES `vehicules` (`id_vehicule`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_vendeur`) REFERENCES `vendeurs` (`id_vendeur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_wilaya` FOREIGN KEY (`id_wilaya`) REFERENCES `wilaya` (`id_wilaya`);

--
-- Contraintes pour la table `vendeurs`
--
ALTER TABLE `vendeurs`
  ADD CONSTRAINT `fk_vendeurs_wilaya` FOREIGN KEY (`id_wilaya`) REFERENCES `wilaya` (`id_wilaya`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
