<?php
session_start();
require_once 'connexion_bdd.php';
 
//Récupération et nettoyage des variables
$type    = $_POST['type']   ?? '';
$retour  = $_POST['retour'] ?? 'Contacts.php';
$nom     = htmlspecialchars(trim($_POST['nom']     ?? ''));
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sujet   = htmlspecialchars(trim($_POST['sujet']   ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));
 
//Validation par if/else
if (empty($message)) {
    header("Location: ../HTML/" . $retour . "?erreur=message_vide");
    exit();
}
 
//Insertion via une fonction dédiée
enregistrerMessage($pdo, $type, $nom, $email, $sujet, $message);
 
header("Location: ../HTML/" . $retour . "?succes=1");
exit();
 
//Définition de la fonction (peut aussi aller dans un fichier fonctions_messages.php)
function enregistrerMessage($pdo, $type, $nom, $email, $sujet, $message) {
    $stmt = $pdo->prepare(
        "INSERT INTO messages (type, nom, email, sujet, message) VALUES (?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$type, $nom, $email ?: null, $sujet, $message]);
}
?>