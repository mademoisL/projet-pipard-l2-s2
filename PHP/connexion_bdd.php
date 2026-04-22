<?php
// LES 4 CLÉS POUR OUVRIR PHPMYADMIN SUR XAMPP :
$serveur = "localhost"; // Ton ordinateur local (serveur XAMPP)
$base_de_donnees = "smartpark_db"; // Le nom exact de la base que tu as créée
$utilisateur = "root"; // Le super-administrateur par défaut de XAMPP
$mot_de_passe = ""; // Sur XAMPP Windows, il n'y a PAS de mot de passe par défaut

try {
    // On lance le câble vers la base de données (Technologie PDO)
    $pdo = new PDO("mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8mb4", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Si un des 4 mots de passe/noms au-dessus est faux, ça coupe tout et affiche l'erreur
    die("ERREUR FATALE : Impossible de se connecter à la base. Détails : " . $e->getMessage());
}
?>