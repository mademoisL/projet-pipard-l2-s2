<?php
session_start();
require_once 'connexion_bdd.php';
 
// Récupère le type de formulaire envoyé (contact, faq ou aide)
$type = $_POST['type'] ?? '';
 
// Champs communs à tous les formulaires
$nom     = htmlspecialchars(trim($_POST['nom'] ?? ''));
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$sujet   = htmlspecialchars(trim($_POST['sujet'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));
 
// Validation : message obligatoire
if (empty($message)) {
    header("Location: ../HTML/" . $_POST['retour'] . "?erreur=message_vide");
    exit();
}
 
// INSERT dans la table messages
$pdo->prepare("INSERT INTO messages (type, nom, email, sujet, message) VALUES (?,?,?,?,?)")
    ->execute([$type, $nom, $email ?: null, $sujet, $message]);
 
// Redirige vers la page d'origine avec un message de succès
header("Location: ../HTML/" . $_POST['retour'] . "?succes=1");
exit();
?>