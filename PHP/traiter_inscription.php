<?php

require_once 'Database.php';
require_once 'fonctions_utilisateurs.php';
 
// Instanciation et connexion
$database = new Database();
$pdo      = $database->getConnection();
 
// Récupération des données du formulaire 
$prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
$nom    = htmlspecialchars(trim($_POST['nom']    ?? ''));
$email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$mdp    = $_POST['password'] ?? '';
 
// Validation simple
if (!$email || empty($prenom) || empty($nom) || empty($mdp)) {
    header("Location: ../HTML/Inscription.php?erreur=champs_invalides");
    exit();
}
 
// Vérification email + création (Fonctions avec valeur de retour)
if (emailExiste($pdo, $email)) {
    header("Location: ../HTML/Inscription.php?erreur=existant");
} else {
    creerUtilisateur($pdo, $prenom, $nom, $email, $mdp);
    header("Location: ../HTML/Connexion.php?inscrit=1");
}
exit();
?>


