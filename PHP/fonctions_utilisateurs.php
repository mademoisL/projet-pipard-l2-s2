<?php
//Toutes les fonctions liées aux utilisateurs

// Fonction pour vérifier si un email est déjà utilisé
function emailExiste($pdo, $email) {
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false; // Retourne true ou false
}
 
// Fonction pour créer un nouvel utilisateur
function creerUtilisateur($pdo, $prenom, $nom, $email, $mdp) {
    $stmt = $pdo->prepare(
        "INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, abonnement_id) VALUES (?, ?, ?, ?, 1)"
    );
    return $stmt->execute([$prenom, $nom, $email, $mdp]); // Retourne true/false
}
 
// Fonction pour trouver un utilisateur par son email
function trouverUtilisateurParEmail($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch(); // Retourne le tableau ou false
}
 
// Fonction pour récupérer toutes les infos d'un utilisateur par son ID
function getUtilisateur($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
 
// Fonction pour mettre à jour le profil
function mettreAJourProfil($pdo, $id, $prenom, $nom, $email, $telephone = null, $adresse = null, $code_postal = null, $ville = null) {
    $stmt = $pdo->prepare(
        "UPDATE utilisateurs SET prenom=?, nom=?, email=?, telephone=?, adresse=?, code_postal=?, ville=? WHERE id=?"
    );
    return $stmt->execute([$prenom, $nom, $email, $telephone, $adresse, $code_postal, $ville, $id]);
}
 
//Toutes les fonctions liées aux véhicules
// Fonction pour récupérer tous les véhicules d'un utilisateur, retourne un tableau
function getVehicules($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
 
// Fonction pour ajouter un véhicule
function ajouterVehicule($pdo, $userId, $immat, $marque, $modele) {
    $stmt = $pdo->prepare(
        "INSERT INTO vehicules (utilisateur_id, immatriculation, marque, modele) VALUES (?, ?, ?, ?)"
    );
    return $stmt->execute([$userId, $immat, $marque, $modele]);
}
 
// Fonction pour récupérer l'immatriculation avant suppression (sécurité)
function getVehiculeParId($pdo, $vehiculeId, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM vehicules WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$vehiculeId, $userId]);
    return $stmt->fetch();
}
 
// Fonction pour supprimer un véhicule
function supprimerVehicule($pdo, $vehiculeId) {
    $stmt = $pdo->prepare("DELETE FROM vehicules WHERE id = ?");
    return $stmt->execute([$vehiculeId]);
}
 
//Toutes les fonctions liées aux capteurs
// Fonction qui retourne les capteurs accessibles selon l'abonnement de l'utilisateur
function getCapteurs($pdo, $abonnementId = 1) {
    // Standard (1) : abonnement_requis <= 1 → 5 capteurs
    // Premium  (2) : abonnement_requis <= 2 → 10 capteurs
    $stmt = $pdo->prepare(
        "SELECT type_appareil, etat FROM capteurs WHERE abonnement_requis <= ? ORDER BY abonnement_requis, id"
    );
    $stmt->execute([$abonnementId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction avec Switch pour déterminer la couleur d'un capteur
function getCouleurCapteur($etat) {
    switch ($etat) {
        case 'En ligne':    return '#00ffcc';
        case 'Maintenance': return '#ffcc00';
        default:            return '#ff4d4d';
    }
}
 
// Toutes les fonctions liées à l'historique
// Fonction pour ajouter une entrée dans l'historique
function ajouterHistorique($pdo, $userId, $action) {
    $stmt = $pdo->prepare("INSERT INTO historique (utilisateur_id, action) VALUES (?, ?)");
    return $stmt->execute([$userId, $action]);
}
 
// Fonction pour récupérer les N dernières entrées d'historique
// Argument par défaut
function getHistorique($pdo, $userId, $limite = 5) {
    // On utilise :id et :limite (homogénéité)
    $sql = "SELECT action, date_action FROM historique 
            WHERE utilisateur_id = :id 
            ORDER BY date_action DESC 
            LIMIT :limite";
            
    $stmt = $pdo->prepare($sql);
    
    // On lie les valeurs
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}