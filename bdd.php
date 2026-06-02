<?php

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

// Tous les modèles actifs avec marque et catégorie
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

// Exemplaires disponibles pour un modèle (quantite > 0)
// Retourne aussi le prix final = prix modèle + augmentation_prix taille
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
               t.libelle AS taille, t.augmentation_prix,
               co.libelle AS couleur,
               m.Id_modèle, m.nom AS modele, m.prix,
               (m.prix + t.augmentation_prix) AS prix_final
        FROM exemplaire e
        JOIN modèle  m  ON e.Id_modèle  = m.Id_modèle
        JOIN taille  t  ON e.Id_taille  = t.Id_taille
        JOIN couleur co ON e.Id_couleur = co.Id_couleur
        WHERE e.Id_exemplaire = :id
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// Avis d'un modèle avec note moyenne
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

// Utilisateur par email
function getUserByEmail($email) {
    $pdo = getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

// Créer une commande et ses lignes
// $items = [['id_exemplaire' => X, 'quantite' => Y, 'prix_unitaire' => Z], ...]
function creerCommande($id_utilisateur, $items, $remise = 0) {
    $pdo = getConnexion();

    $montant_total = array_sum(array_map(fn($i) => $i['prix_unitaire'] * $i['quantite'], $items));
    if ($remise > 0) {
        $montant_total = round($montant_total * (1 - $remise / 100), 2);
    }

    $pdo->beginTransaction();
    try {
        // Créer la commande
        $stmt = $pdo->prepare("
            INSERT INTO commande (date_commande, statut, montant_total, remise, Id_utilisateur)
            VALUES (NOW(), 'en_attente', :montant, :remise, :id_user)
        ");
        $stmt->execute([':montant' => $montant_total, ':remise' => $remise, ':id_user' => $id_utilisateur]);
        $id_commande = $pdo->lastInsertId();

        // Créer les lignes et décrémenter le stock
        foreach ($items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO ligne_commande (quantite, prix_unitaire, Id_exemplaire, Id_commande)
                VALUES (:qte, :prix, :id_ex, :id_cmd)
            ");
            $stmt->execute([
                ':qte'    => $item['quantite'],
                ':prix'   => $item['prix_unitaire'],
                ':id_ex'  => $item['id_exemplaire'],
                ':id_cmd' => $id_commande,
            ]);

            // Décrémenter le stock
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