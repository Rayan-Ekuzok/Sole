-- ============================================================
--  SOLE — Création de la base de données + données de test
-- ============================================================

CREATE DATABASE IF NOT EXISTS sole CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sole;

-- ============================================================
--  STRUCTURE
-- ============================================================

CREATE TABLE marque(
   Id_marque INT AUTO_INCREMENT,
   libelle VARCHAR(50),
   PRIMARY KEY(Id_marque)
);

CREATE TABLE categorie(
   Id_categorie INT AUTO_INCREMENT,
   libelle VARCHAR(50),
   PRIMARY KEY(Id_categorie)
);

CREATE TABLE utilisateur(
   Id_utilisateur INT AUTO_INCREMENT,
   nom VARCHAR(50),
   prenom VARCHAR(50),
   email VARCHAR(50),
   password VARCHAR(256),
   adresse VARCHAR(50),
   code_postal VARCHAR(50),
   pays VARCHAR(50),
   ville VARCHAR(50),
   PRIMARY KEY(Id_utilisateur)
);

CREATE TABLE couleur(
   Id_couleur INT AUTO_INCREMENT,
   libelle VARCHAR(50),
   PRIMARY KEY(Id_couleur)
);

CREATE TABLE taille(
   Id_taille INT AUTO_INCREMENT,
   libelle VARCHAR(50),
   augmentation_prix DECIMAL(15,2),
   PRIMARY KEY(Id_taille)
);

CREATE TABLE modèle(
   Id_modèle INT AUTO_INCREMENT,
   nom VARCHAR(50),
   actif BOOLEAN,
   prix DECIMAL(15,2),
   description VARCHAR(255),
   libelle VARCHAR(50),
   image VARCHAR(500),
   Id_categorie INT NOT NULL,
   Id_marque INT NOT NULL,
   PRIMARY KEY(Id_modèle),
   FOREIGN KEY(Id_categorie) REFERENCES categorie(Id_categorie),
   FOREIGN KEY(Id_marque) REFERENCES marque(Id_marque)
);

CREATE TABLE exemplaire(
   Id_exemplaire INT AUTO_INCREMENT,
   quantite INT,
   Id_modèle INT NOT NULL,
   Id_taille INT NOT NULL,
   Id_couleur INT NOT NULL,
   PRIMARY KEY(Id_exemplaire),
   FOREIGN KEY(Id_modèle) REFERENCES modèle(Id_modèle),
   FOREIGN KEY(Id_taille) REFERENCES taille(Id_taille),
   FOREIGN KEY(Id_couleur) REFERENCES couleur(Id_couleur)
);

CREATE TABLE commande(
   Id_commande INT AUTO_INCREMENT,
   date_commande DATETIME,
   statut VARCHAR(50),
   montant_total DECIMAL(15,2),
   remise INT,
   Id_utilisateur INT NOT NULL,
   PRIMARY KEY(Id_commande),
   FOREIGN KEY(Id_utilisateur) REFERENCES utilisateur(Id_utilisateur)
);

CREATE TABLE ligne_commande(
   Id_ligne_commande INT AUTO_INCREMENT,
   quantite INT,
   prix_unitaire DECIMAL(15,2),
   Id_exemplaire INT NOT NULL,
   Id_commande INT NOT NULL,
   PRIMARY KEY(Id_ligne_commande),
   FOREIGN KEY(Id_exemplaire) REFERENCES exemplaire(Id_exemplaire),
   FOREIGN KEY(Id_commande) REFERENCES commande(Id_commande)
);

CREATE TABLE avis(
   Id_avis INT AUTO_INCREMENT,
   note INT,
   commentaire VARCHAR(255),
   date_avis DATETIME,
   Id_modèle INT NOT NULL,
   Id_utilisateur INT NOT NULL,
   PRIMARY KEY(Id_avis),
   FOREIGN KEY(Id_modèle) REFERENCES modèle(Id_modèle),
   FOREIGN KEY(Id_utilisateur) REFERENCES utilisateur(Id_utilisateur)
);

-- ============================================================
--  DONNÉES
-- ============================================================

-- Marques
INSERT INTO marque (libelle) VALUES
('Nike'),
('Adidas'),
('New Balance'),
('Vans'),
('Converse');

-- Catégories
INSERT INTO categorie (libelle) VALUES
('Sneakers'),
('Running'),
('Skate'),
('Lifestyle'),
('Montante');

-- Couleurs
INSERT INTO couleur (libelle) VALUES
('Blanc'),
('Noir'),
('Rouge'),
('Bleu marine'),
('Gris'),
('Vert olive'),
('Beige'),
('Dégradé noir/blanc');

-- Tailles (augmentation_prix = montant fixe ajouté au prix du modèle)
INSERT INTO taille (libelle, augmentation_prix) VALUES
('36', 0.00),
('37', 0.00),
('38', 0.00),
('39', 0.00),
('40', 0.00),
('41', 0.30),
('42', 0.30),
('43', 0.60),
('44', 0.60),
('45', 1.00);

-- Modèles
-- (Id_marque : 1=Nike 2=Adidas 3=New Balance 4=Vans 5=Converse)
-- (Id_categorie : 1=Sneakers 2=Running 3=Skate 4=Lifestyle 5=Montante)
INSERT INTO modèle (nom, actif, prix, description, libelle, image, Id_categorie, Id_marque) VALUES
('Air Max 90',        1, 129.99, 'Icône intemporelle du streetwear. Tige en mesh et cuir, amorti Air visible.',          'AM90',  'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/e0cb7a1d-74d4-44ac-b3da-44f1c2c4a5a8/air-max-90-shoes-kRsBnD.png', 1, 1),
('Air Force 1 Low',   1, 109.99, 'La chaussure la plus vendue de tous les temps. Silhouette basse en cuir pleine fleur.', 'AF1',   'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/b7d9211c-26e7-431a-ac24-b0540fb3c00f/air-force-1-07-shoes-WjJFNk.png', 4, 1),
('React Infinity',    1, 159.99, 'Conçue pour réduire les blessures. Semelle React ultra-souple et réactive.',           'RI',    'https://static.nike.com/a/images/c_limit,w_592,f_auto/t_product_v1/i1-665455a5-45de-40fb-945f-c1852b82400d/react-infinity-run-flyknit-3-road-running-shoes-zX42Nc.png', 2, 1),
('Stan Smith',        1,  89.99, 'Le classique du tennis devenu icône mode. Tige cuir lisse, trois bandes perforées.',    'SS',    'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/68ae7ea7849b43eca70aac1e00f5146d_9366/Stan_Smith_Shoes_White_FX5502_01_standard.jpg', 4, 2),
('Superstar',         1,  99.99, 'La légendaire basket aux embouts coquille. Tige cuir, semelle dentée caoutchouc.',      'SSTAR', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7ed0855435194229a525aad6009a0497_9366/Superstar_Shoes_White_EG4958_01_standard.jpg', 1, 2),
('Ultraboost 23',     1, 189.99, 'La référence running Adidas. Tige Primeknit+, semelle Boost haute restitution.',        'UB23',  'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/fbaf991a78bc4896a3e9ad7800abcec6_9366/Ultraboost_Light_Running_Shoes_Black_HQ6351_01_standard.jpg', 2, 2),
('574',               1,  84.99, 'Référence lifestyle depuis les années 80. Mesh et suède, semelle ENCAP.',               'NB574', 'https://nb.scene7.com/is/image/NB/ml574evg_nb_02_i?$&qlt=80&fmt=webp&wid=440&hei=440', 4, 3),
('990v6',             1, 249.99, 'Fabriquée aux USA. Le summum du confort, tige mesh et daim premium.',                   'NB990', 'https://nb.scene7.com/is/image/NB/m990gl6_nb_02_i?$&qlt=80&fmt=webp&wid=440&hei=440', 2, 3),
('Old Skool',         1,  74.99, 'Première chaussure avec le Sidestripe Vans. Toile et suède, semelle Waffle.',           'OS',    'https://images.vans.com/is/image/VansEU/VN000D3HY28-HERO?83x583$', 3, 4),
('Sk8-Hi',            1,  84.99, 'Montante emblématique du skate. Canvas et suède, col matelassé pour la cheville.',      'SK8',   'https://images.vans.com/is/image/VansEU/VN000D5IB8C-HERO?83x583$', 5, 4),
('Chuck Taylor',      1,  64.99, 'La sneaker originale depuis 1917. Toile, embout caoutchouc, semelle Ortholite.',        'CT',    'https://www.converse.com/dw/image/v2/BCZC_PRD/on/demandware.static/-/Sites-cnv-master-catalog/default/dwb5e74e45/images/a_107/M9160C_A_107X1.jpg', 1, 5),
('Run Star Hike',     1,  94.99, 'Chuck Taylor revisitée plateforme XXL. Toile, semelle crantée chunky.',                 'RSH',   'https://www.converse.com/dw/image/v2/BCZC_PRD/on/demandware.static/-/Sites-cnv-master-catalog/default/dw3f0a4f3a/images/a_107/170969C_A_107X1.jpg', 1, 5);

-- Exemplaires
-- modèle 1 : Air Max 90
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(5,  1, 5, 1), (4,  1, 6, 1), (6,  1, 7, 1), (3,  1, 8, 1),  -- Blanc 40-43
(5,  1, 5, 2), (4,  1, 6, 2), (7,  1, 7, 2), (3,  1, 8, 2);  -- Noir  40-43

-- modèle 2 : Air Force 1 Low
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(10, 2, 4, 1), (8,  2, 5, 1), (12, 2, 6, 1), (7,  2, 7, 1), (5, 2, 8, 1); -- Blanc 39-43

-- modèle 3 : React Infinity
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(4,  3, 5, 4), (6,  3, 6, 4), (5,  3, 7, 4), (3,  3, 8, 4),  -- Bleu marine 40-43
(4,  3, 5, 2), (5,  3, 6, 2), (4,  3, 7, 2);                  -- Noir 40-42

-- modèle 4 : Stan Smith
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(6,  4, 4, 1), (9,  4, 5, 1), (7,  4, 6, 1), (5,  4, 7, 1),  -- Blanc 39-42
(4,  4, 5, 6), (3,  4, 6, 6), (2,  4, 7, 6);                  -- Vert olive 40-42

-- modèle 5 : Superstar
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(5,  5, 4, 8), (8,  5, 5, 8), (6,  5, 6, 8), (4,  5, 7, 8), (3, 5, 8, 8); -- Dégradé 39-43

-- modèle 6 : Ultraboost 23
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(4,  6, 5, 2), (5,  6, 6, 2), (7,  6, 7, 2), (3,  6, 8, 2), (2, 6, 9, 2),  -- Noir 40-44
(4,  6, 5, 4), (6,  6, 6, 4), (5,  6, 7, 4);                                -- Bleu marine 40-42

-- modèle 7 : 574
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(7,  7, 4, 5), (9,  7, 5, 5), (8,  7, 6, 5), (6,  7, 7, 5),  -- Gris 39-42
(4,  7, 5, 4), (5,  7, 6, 4), (3,  7, 7, 4);                  -- Bleu marine 40-42

-- modèle 8 : 990v6
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(3,  8, 5, 5), (4,  8, 6, 5), (5,  8, 7, 5), (2,  8, 8, 5), (1, 8, 9, 5); -- Gris 40-44

-- modèle 9 : Old Skool
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(10, 9, 3, 2), (12, 9, 4, 2), (15, 9, 5, 2), (11, 9, 6, 2), (8, 9, 7, 2), -- Noir 38-42
(6,  9, 3, 8), (8,  9, 4, 8), (9,  9, 5, 8);                               -- Dégradé 38-40

-- modèle 10 : Sk8-Hi
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(5, 10, 4, 2), (7, 10, 5, 2), (6, 10, 6, 2), (4, 10, 7, 2),  -- Noir 39-42
(3, 10, 4, 1), (5, 10, 5, 1), (4, 10, 6, 1);                  -- Blanc 39-41

-- modèle 11 : Chuck Taylor
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(15, 11, 2, 1), (18, 11, 3, 1), (20, 11, 4, 1), (22, 11, 5, 1), (18, 11, 6, 1), -- Blanc 37-41
(10, 11, 2, 2), (12, 11, 3, 2), (15, 11, 4, 2), (18, 11, 5, 2), (14, 11, 6, 2), -- Noir 37-41
(5,  11, 2, 3), (6,  11, 3, 3), (8,  11, 4, 3);                                  -- Rouge 37-39

-- modèle 12 : Run Star Hike
INSERT INTO exemplaire (quantite, Id_modèle, Id_taille, Id_couleur) VALUES
(5, 12, 2, 7), (7, 12, 3, 7), (6, 12, 4, 7), (5, 12, 5, 7),  -- Beige 37-40
(3, 12, 2, 2), (4, 12, 3, 2), (5, 12, 4, 2);                  -- Noir 37-39

-- Utilisateurs
-- password123 → ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f (SHA256)
-- JeSuisJoe   → 988d4bdc08df60ae93997d4be260b3210dc00d386390af9b67532edbf58c90cc (SHA256)
INSERT INTO utilisateur (nom, prenom, email, password, adresse, code_postal, pays, ville) VALUES
('Dupont',  'Marie',   'marie.dupont@email.fr',   'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '12 rue des Lilas',    '75011', 'France', 'Paris'),
('Martin',  'Lucas',   'lucas.martin@email.fr',   'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '5 avenue Foch',       '69006', 'France', 'Lyon'),
('Bernard', 'Sophie',  'sophie.bernard@email.fr', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '8 rue Paradis',       '13001', 'France', 'Marseille'),
('Leroy',   'Thomas',  'thomas.leroy@email.fr',   'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '3 place du Capitole', '31000', 'France', 'Toulouse'),
('Moreau',  'Julie',   'julie.moreau@email.fr',   'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '17 rue de la Paix',   '06000', 'France', 'Nice'),
('Doe',     'Joe',     'Joe@gmail.com',            '988d4bdc08df60ae93997d4be260b3210dc00d386390af9b67532edbf58c90cc', '1 rue du Test',       '00000', 'France', 'Paris');

-- Commandes
INSERT INTO commande (date_commande, statut, montant_total, remise, Id_utilisateur) VALUES
('2025-01-10 14:32:00', 'livree',      130.29,  0, 1),
('2025-02-14 09:15:00', 'livree',      164.98,  0, 1),
('2025-03-22 17:45:00', 'expediee',    190.29,  0, 2),
('2025-04-05 11:20:00', 'en_attente',  149.98, 10, 3),
('2025-04-18 16:10:00', 'annulee',      74.99,  0, 4),
('2025-05-02 10:00:00', 'livree',      249.99,  0, 2),
('2025-05-15 13:30:00', 'expediee',    160.29,  5, 5),
('2025-06-01 08:50:00', 'en_attente',  189.98,  0, 1);

-- Lignes de commande
-- Commande 1 : Air Max 90 / Blanc / 42  (exemplaire Id=3, prix 129.99 + 0.30)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 130.29, 3, 1);

-- Commande 2 : Stan Smith / Blanc / 40 (ex=9) + Old Skool / Noir / 40 (ex=30)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 89.99, 9,  2),
(1, 74.99, 30, 2);

-- Commande 3 : Ultraboost 23 / Noir / 42 (ex=19, prix 189.99+0.30)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 190.29, 19, 3);

-- Commande 4 : Chuck Taylor / Blanc / 39 (ex=38) + Chuck Taylor / Noir / 39 (ex=43)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 64.99, 38, 4),
(1, 64.99, 43, 4);

-- Commande 5 : Old Skool / Noir / 39 (ex=29, annulée)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 74.99, 29, 5);

-- Commande 6 : 990v6 / Gris / 42 (ex=25)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 249.99, 25, 6);

-- Commande 7 : React Infinity / Bleu / 41 (ex=12) + Chuck Taylor / Blanc / 38 (ex=37)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(1, 159.99, 12, 7),
(1,  64.99, 37, 7);

-- Commande 8 : Run Star Hike / Beige / 38 x2 (ex=63)
INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande) VALUES
(2, 94.99, 63, 8);

-- Avis
INSERT INTO avis (note, commentaire, date_avis, Id_modèle, Id_utilisateur) VALUES
(5, 'Parfaites ! Très confortables dès la première utilisation.',             '2025-01-20', 1,  1),
(4, 'Belles chaussures, légèrement rigides au début mais ça passe.',          '2025-02-25', 4,  1),
(5, 'Les meilleures chaussures de running. Le Boost c est une autre dimension.', '2025-04-01', 6, 2),
(3, 'Qualité correcte mais déçu par le coloris, moins vibrant qu en photo.',  '2025-04-10', 5,  3),
(5, 'Un classique indémodable. Ma troisième paire, toujours aussi satisfait.','2025-03-15', 11, 4),
(4, 'Confort exceptionnel. Parfait pour mes longues sessions de course.',      '2025-05-20', 3,  5),
(5, 'Superbe finition, cuir de qualité. La Air Force 1 reste indétrônable.',  '2025-02-28', 2,  2),
(4, 'Bon rapport qualité/prix. La semelle chunky est vraiment originale.',     '2025-06-02', 12, 1);
