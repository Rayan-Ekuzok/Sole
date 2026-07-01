# 👟 SOLE — Boutique de chaussures en ligne

> 📚 Projet réalisé dans le cadre de mes études en **BTS SIO** (Services Informatiques aux Organisations).  
> Il est volontairement rendu public sur GitHub à titre de référence et d'apprentissage. Vous êtes libres de vous en inspirer.

Application web e-commerce complète de vente de chaussures, développée en **PHP / MySQL**. Elle permet à des clients de parcourir un catalogue, de commander des articles et de laisser des avis, avec une gestion de session sécurisée et un système de panier persistant.

---

## 🌐 Démo en ligne

| | URL |
|---|---|
| **Application** | [https://sole.alwaysdata.net](https://sole.alwaysdata.net) |
| **Dépôt GitHub** | [github.com/Rayan-Ekuzok/Sole](https://github.com/Rayan-Ekuzok/Sole) |

---

## 🧪 Compte de test

| Identifiant | Mot de passe |
|---|---|
| `Joe@gmail.com` | `JeSuisJoe` |

---

## 🚀 Technologies utilisées

| Technologie | Rôle |
|---|---|
| PHP 8 | Back-end, logique métier, sessions |
| MySQL / PDO | Base de données relationnelle |
| HTML / CSS | Interface utilisateur |
| JavaScript | Interactions dynamiques (panier, étoiles, minuterie) |
| Bebas Neue / DM Sans | Typographie (Google Fonts) |

---

## ⚙️ Installation

```bash
# Cloner le dépôt
git clone https://github.com/Rayan-Ekuzok/Sole.git
cd Sole

# Importer la base de données
# Dans phpMyAdmin ou MySQL CLI :
mysql -u root -p slam_projet2 < slam_projet2.sql
```

Puis configurer la connexion dans `bdd.php` :

```php
$host   = 'localhost';
$dbname = 'slam_projet2';
$user   = 'root';
$password = '';
```

Servir le projet depuis un serveur PHP local (XAMPP, Laragon, PHP built-in server…).

---

## 🗄️ Base de données

La base `slam_projet2` contient les tables suivantes :

| Table | Description |
|---|---|
| `utilisateur` | Comptes clients (nom, email, adresse, mot de passe hashé) |
| `modèle` | Modèles de chaussures (nom, prix, description, image) |
| `marque` | Marques associées aux modèles |
| `categorie` | Catégories de produits |
| `exemplaire` | Stock par combinaison modèle / taille / couleur |
| `taille` | Tailles disponibles avec augmentation de prix |
| `couleur` | Couleurs disponibles |
| `panier` | Articles en cours dans le panier de chaque utilisateur |
| `commande` | Commandes passées avec statut et montant total |
| `ligne_commande` | Détail des articles par commande |
| `avis` | Avis clients avec note et commentaire |

---

## 📋 Fonctionnalités

### 🛍️ Catalogue & produits
- Affichage de tous les modèles actifs avec image, marque, prix de base et catégorie
- Page produit avec sélection de taille et couleur
- Prix mis à jour dynamiquement selon la taille sélectionnée
- Affichage du stock disponible en temps réel
- Boutons désactivés pour les combinaisons en rupture de stock

### 🛒 Panier
- Ajout au panier avec vérification du stock
- Mise à jour de la quantité et suppression sans rechargement (fetch / JSON)
- Récapitulatif dynamique avec total recalculé côté client
- Vérification du stock à la validation de commande

### 📦 Commandes
- Création de commande avec transaction SQL (intégrité garantie)
- Décrémentation du stock à la validation
- Page de confirmation animée avec récapitulatif complet
- Historique des commandes avec détail par article et badge de statut

### ⭐ Avis clients
- Système d'étoiles interactives (hover + clic)
- Publication réservée aux clients ayant commandé le produit
- Un seul avis autorisé par utilisateur et par modèle
- Affichage de la note moyenne et du nombre d'avis

### 🔐 Authentification & sécurité
- Inscription avec validation complète (email, mot de passe, adresse)
- Mot de passe hashé en **SHA-256 + sel** côté serveur
- Session PHP avec expiration après **10 minutes d'inactivité**
- Minuterie JavaScript côté client avec compte à rebours et déconnexion automatique
- Reconnexion automatique redirigée vers la page demandée

---

## 🔐 Gestion de session

La session est surveillée côté serveur (`menu.php`) et côté client (JavaScript) :

- Chaque page vérifie l'activité via `$_SESSION['last_activity']`
- Après 9 minutes sans action, un **avertissement** s'affiche avec un compte à rebours de 60 secondes
- Après 10 minutes, la session est détruite et l'utilisateur est redirigé vers la page de connexion avec un message explicatif
- Les événements surveillés : `click`, `keydown`, `mousemove`, `scroll`, `touchstart`

---

## 🗂️ Structure des fichiers

```
Sole/
├── index.php           — Catalogue des modèles
├── article.php         — Page produit (tailles, couleurs, avis)
├── panier.php          — Panier interactif
├── validerPanier.php   — Validation et création de commande
├── confirmation.php    — Page de confirmation animée
├── mesCommandes.php    — Historique des commandes
├── login.php           — Formulaire de connexion
├── doLogin.php         — Traitement de la connexion
├── register.php        — Formulaire d'inscription
├── doRegister.php      — Traitement de l'inscription
├── logout.php          — Déconnexion
├── ajouterPanier.php   — Ajout au panier (POST)
├── soumettreAvis.php   — Soumission d'un avis (POST)
├── menu.php            — Navigation commune (include)
├── bdd.php             — Connexion PDO et toutes les fonctions SQL
├── style.css           — Feuille de style
└── slam_projet2.sql    — Script de création de la base de données
```
