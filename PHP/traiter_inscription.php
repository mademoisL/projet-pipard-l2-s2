<?php
// 1. Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=smartpark_db;charset=utf8', 'root', '');

// 2. On récupère les infos du formulaire
$prenom = $_POST['prenom'];
$nom    = $_POST['nom'];
$email  = $_POST['email'];
$mdp    = $_POST['password']; // Mot de passe en clair pour faire simple

// 3. On vérifie si l'email existe déjà
$verif = $db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$verif->execute([$email]);

if ($verif->fetch()) {
    // Si l'utilisateur existe : on retourne à l'inscription avec une alerte
    header("Location: ../HTML/Inscription.php?erreur=1");
} else {
    // Si l'utilisateur n'existe pas : on le crée
    $ins = $db->prepare("INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, abonnement_id) VALUES (?, ?, ?, ?, 1)");
    $ins->execute([$prenom, $nom, $email, $mdp]);
    
    // Une fois fini, on l'envoie vers la connexion
    header("Location: ../HTML/Connexion.php");
}
exit();
?>