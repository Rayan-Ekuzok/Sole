<?php

function getSel() {
    return 'LeSelLaBaleineOIHZEFUOzeriéàç-yèé&\'HFZOI';
}

function hashMotDePasse($password) {
    return hash('sha256', $password . getSel());
}

function getConnexion() {
    $host     = 'localhost';
    $dbname   = 'slam_projet2';
    $user     = 'root';
    $password = '';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// Tous les modèles actifs
function getModeles() {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT m.Id_modèle, m.nom, m.prix, m.description, m.libelle, m.actif, m.image,
               ma.libelle AS marque,
               c.libelle  AS categorie
        FROM modèle m
        JOIN marque   ma ON m.Id_marque    = ma.Id_marque
        JOIN categorie c ON m.Id_categorie = c.Id_categorie
        WHERE m.actif = 1
        ORDER BY ma.libelle, m.nom
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Un modèle par son ID
function getModeleById($id) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT m.Id_modèle, m.nom, m.prix, m.description, m.libelle, m.actif, m.image,
               ma.libelle AS marque,
               c.libelle  AS categorie
        FROM modèle m
        JOIN marque   ma ON m.Id_marque    = ma.Id_marque
        JOIN categorie c ON m.Id_categorie = c.Id_categorie
        WHERE m.Id_modèle = :id AND m.actif = 1
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// Exemplaires disponibles pour un modèle
function getExemplairesByModele($id_modele) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT e.Id_exemplaire, e.quantite,
               t.Id_taille, t.libelle AS taille, t.augmentation_prix,
               co.Id_couleur, co.libelle AS couleur,
               (m.prix + t.augmentation_prix) AS prix_final
        FROM exemplaire e
        JOIN modèle  m  ON e.Id_modèle  = m.Id_modèle
        JOIN taille  t  ON e.Id_taille  = t.Id_taille
        JOIN couleur co ON e.Id_couleur = co.Id_couleur
        WHERE e.Id_modèle = :id AND e.quantite > 0
        ORDER BY t.libelle, co.libelle
    ");
    $stmt->execute([':id' => $id_modele]);
    return $stmt->fetchAll();
}

// Un exemplaire par son ID
function getExemplaireById($id) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT e.Id_exemplaire, e.quantite,
               t.Id_taille, t.libelle AS taille, t.augmentation_prix,
               co.Id_couleur, co.libelle AS couleur,
               m.Id_modèle, m.nom AS modele, m.prix, m.image,
               ma.libelle AS marque,
               (m.prix + t.augmentation_prix) AS prix_final
        FROM exemplaire e
        JOIN modèle  m  ON e.Id_modèle  = m.Id_modèle
        JOIN marque  ma ON m.Id_marque  = ma.Id_marque
        JOIN taille  t  ON e.Id_taille  = t.Id_taille
        JOIN couleur co ON e.Id_couleur = co.Id_couleur
        WHERE e.Id_exemplaire = :id
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// Vérifier si un utilisateur a commandé un modèle (hors commandes annulées)
function utilisateurACommandeModele($id_utilisateur, $id_modele) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM ligne_commande lc, commande co, exemplaire e
        WHERE lc.Id_commande   = co.Id_commande
          AND lc.Id_exemplaire = e.Id_exemplaire
          AND co.Id_utilisateur = :id_user
          AND e.Id_modèle       = :id_modele
          AND co.statut        != 'annulee'
    ");
    $stmt->execute([':id_user' => $id_utilisateur, ':id_modele' => $id_modele]);
    return (int) $stmt->fetch()['total'] > 0;
}

// Vérifier si un utilisateur a déjà laissé un avis sur un modèle
function utilisateurADejaCommente($id_utilisateur, $id_modele) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total FROM avis
        WHERE Id_utilisateur = :id_user AND Id_modèle = :id_modele
    ");
    $stmt->execute([':id_user' => $id_utilisateur, ':id_modele' => $id_modele]);
    return (int) $stmt->fetch()['total'] > 0;
}

// Ajouter un avis
function ajouterAvis($id_utilisateur, $id_modele, $note, $commentaire) {
    $pdo = getConnexion();
    $pdo->prepare("
        INSERT INTO avis (note, commentaire, date_avis, Id_modèle, Id_utilisateur)
        VALUES (:note, :commentaire, NOW(), :id_modele, :id_user)
    ")->execute([
        ':note'        => $note,
        ':commentaire' => $commentaire,
        ':id_modele'   => $id_modele,
        ':id_user'     => $id_utilisateur,
    ]);
}

// Avis d'un modèle
function getAvisByModele($id_modele) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT a.note, a.commentaire, a.date_avis,
               u.nom, u.prenom
        FROM avis a
        JOIN utilisateur u ON a.Id_utilisateur = u.Id_utilisateur
        WHERE a.Id_modèle = :id
        ORDER BY a.date_avis DESC
    ");
    $stmt->execute([':id' => $id_modele]);
    return $stmt->fetchAll();
}

// Utilisateur par email
function getUserByEmail($email) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

// ── PANIER ────────────────────────────────────────────────────

// Récupérer le panier d'un utilisateur
function getPanierByUtilisateur($id_utilisateur) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT p.Id_panier, p.quantite,
               e.Id_exemplaire,
               t.libelle AS taille, t.augmentation_prix,
               co.libelle AS couleur,
               m.Id_modèle, m.nom AS modele, m.prix, m.image,
               ma.libelle AS marque,
               (m.prix + t.augmentation_prix) AS prix_final,
               ((m.prix + t.augmentation_prix) * p.quantite) AS sous_total
        FROM panier p
        JOIN exemplaire e  ON p.Id_exemplaire = e.Id_exemplaire
        JOIN modèle     m  ON e.Id_modèle     = m.Id_modèle
        JOIN marque     ma ON m.Id_marque      = ma.Id_marque
        JOIN taille     t  ON e.Id_taille      = t.Id_taille
        JOIN couleur    co ON e.Id_couleur     = co.Id_couleur
        WHERE p.Id_utilisateur = :id
        ORDER BY p.Id_panier DESC
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetchAll();
}

// Ajouter ou mettre à jour un article dans le panier
function ajouterAuPanier($id_utilisateur, $id_exemplaire, $quantite) {
    $pdo = getConnexion();

    // Vérifier si l'article est déjà dans le panier
    $stmt = $pdo->prepare("
        SELECT Id_panier, quantite FROM panier
        WHERE Id_utilisateur = :id_user AND Id_exemplaire = :id_ex
    ");
    $stmt->execute([':id_user' => $id_utilisateur, ':id_ex' => $id_exemplaire]);
    $existant = $stmt->fetch();

    if ($existant) {
        // Mettre à jour la quantité
        $pdo->prepare("
            UPDATE panier SET quantite = quantite + :qte
            WHERE Id_panier = :id
        ")->execute([':qte' => $quantite, ':id' => $existant['Id_panier']]);
    } else {
        // Créer la ligne
        $pdo->prepare("
            INSERT INTO panier (quantite, Id_utilisateur, Id_exemplaire)
            VALUES (:qte, :id_user, :id_ex)
        ")->execute([':qte' => $quantite, ':id_user' => $id_utilisateur, ':id_ex' => $id_exemplaire]);
    }
}

// Supprimer un article du panier
function supprimerDuPanier($id_panier, $id_utilisateur) {
    $pdo = getConnexion();
    $pdo->prepare("
        DELETE FROM panier WHERE Id_panier = :id AND Id_utilisateur = :id_user
    ")->execute([':id' => $id_panier, ':id_user' => $id_utilisateur]);
}

// Mettre à jour la quantité d'un article du panier
function mettreAJourPanier($id_panier, $id_utilisateur, $quantite) {
    $pdo = getConnexion();
    if ($quantite <= 0) {
        supprimerDuPanier($id_panier, $id_utilisateur);
    } else {
        $pdo->prepare("
            UPDATE panier SET quantite = :qte
            WHERE Id_panier = :id AND Id_utilisateur = :id_user
        ")->execute([':qte' => $quantite, ':id' => $id_panier, ':id_user' => $id_utilisateur]);
    }
}

// Vider le panier d'un utilisateur
function viderPanier($id_utilisateur) {
    $pdo = getConnexion();
    $pdo->prepare("DELETE FROM panier WHERE Id_utilisateur = :id")
        ->execute([':id' => $id_utilisateur]);
}

// Nombre d'articles dans le panier (pour le badge nav)
function getNbArticlesPanier($id_utilisateur) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(quantite), 0) AS total FROM panier WHERE Id_utilisateur = :id
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    return (int) $stmt->fetch()['total'];
}

// ── COMMANDES ─────────────────────────────────────────────────

// Commandes d'un utilisateur
function getCommandesByUtilisateur($id_utilisateur) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT Id_commande, date_commande, statut, montant_total, remise
        FROM commande
        WHERE Id_utilisateur = :id
        ORDER BY date_commande DESC
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetchAll();
}

// Lignes d'une commande
function getLignesByCommande($id_commande) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("
        SELECT lc.quantite, lc.prix_unitaire,
               m.nom      AS modele,
               ma.libelle AS marque,
               t.libelle  AS taille,
               co.libelle AS couleur
        FROM ligne_commande lc
        JOIN exemplaire e  ON lc.Id_exemplaire = e.Id_exemplaire
        JOIN modèle     m  ON e.Id_modèle      = m.Id_modèle
        JOIN marque     ma ON m.Id_marque       = ma.Id_marque
        JOIN taille     t  ON e.Id_taille       = t.Id_taille
        JOIN couleur    co ON e.Id_couleur      = co.Id_couleur
        WHERE lc.Id_commande = :id
    ");
    $stmt->execute([':id' => $id_commande]);
    return $stmt->fetchAll();
}

// Créer une commande depuis le panier
// $items = [['id_exemplaire'=>X,'quantite'=>Y,'prix_unitaire'=>Z], ...]
function creerCommande($id_utilisateur, $items, $remise = 0) {
    $pdo = getConnexion();

    $montant_total = array_sum(array_map(fn($i) => $i['prix_unitaire'] * $i['quantite'], $items));
    if ($remise > 0) {
        $montant_total = round($montant_total * (1 - $remise / 100), 2);
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO commande (date_commande, statut, montant_total, remise, Id_utilisateur)
            VALUES (NOW(), 'en_attente', :montant, :remise, :id_user)
        ");
        $stmt->execute([':montant' => $montant_total, ':remise' => $remise, ':id_user' => $id_utilisateur]);
        $id_commande = $pdo->lastInsertId();

        foreach ($items as $item) {
            // Vérifier le stock disponible
            $stock = $pdo->prepare("SELECT quantite FROM exemplaire WHERE Id_exemplaire = :id FOR UPDATE");
            $stock->execute([':id' => $item['id_exemplaire']]);
            $dispo = (int) $stock->fetch()['quantite'];

            if ($dispo < $item['quantite']) {
                throw new Exception("Stock insuffisant pour l'exemplaire {$item['id_exemplaire']}");
            }

            $pdo->prepare("
                INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande)
                VALUES (:qte, :prix, :id_ex, :id_cmd)
            ")->execute([
                ':qte'    => $item['quantite'],
                ':prix'   => $item['prix_unitaire'],
                ':id_ex'  => $item['id_exemplaire'],
                ':id_cmd' => $id_commande,
            ]);

            $pdo->prepare("
                UPDATE exemplaire SET quantite = quantite - :qte WHERE Id_exemplaire = :id
            ")->execute([':qte' => $item['quantite'], ':id' => $item['id_exemplaire']]);
        }

        $pdo->commit();
        return $id_commande;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}