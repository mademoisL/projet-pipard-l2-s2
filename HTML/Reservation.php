<?php
session_start();
require_once '../PHP/connexion_bdd.php';

// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php");
    exit();
}

// 1. Récupérer l'ID de la place envoyé par l'URL
if (!isset($_GET['id'])) {
    header("Location: Places.php");
    exit();
}

$place_id = $_GET['id'];

// 2. Récupérer les infos de cette place
$stmt = $pdo->prepare("SELECT * FROM places WHERE id = ?");
$stmt->execute([$place_id]);
$place = $stmt->fetch();

// Si la place n'existe pas ou n'est plus libre
if (!$place || $place['statut'] !== 'Libre') {
    header("Location: Places.php?erreur=indisponible");
    exit();
}

// 3. Logique de confirmation de réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On passe la place en "Réservée"
    $update = $pdo->prepare("UPDATE places SET statut = 'Réservée' WHERE id = ?");
    
    if ($update->execute([$place_id])) {
        // Optionnel : Tu pourrais ici insérer une ligne dans une table "reservations" 
        // pour savoir quel utilisateur a réservé quelle place.
        header("Location: Places.php?success=reservee");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SMARTPARK – Réserver une place</title>
    <link rel="stylesheet" href="../CSS/Style.css">
</head>
<body class="page-centree">

    <main>
        <div class="login-box" style="max-width: 500px; text-align: center;">
            <a href="Places.php" style="text-decoration: none; color: #888; font-size: 0.8rem; display: block; margin-bottom: 20px;">← Retour au parking</a>
            
            <h2 style="color: var(--accent-color); margin-bottom: 10px;">Réserver la place</h2>
            <h1 style="font-size: 4rem; margin: 20px 0;"><?= htmlspecialchars($place['numero_place']) ?></h1>
            
            <p style="color: #ccc; margin-bottom: 30px;">
                Voulez-vous confirmer la réservation de cette place ? <br>
                Elle vous sera réservée pendant que vous arrivez.
            </p>

            <form method="POST">
                <button type="submit" class="btn">CONFIRMER LA RÉSERVATION</button>
            </form>
            
            <p style="font-size: 0.7rem; color: #666; margin-top: 20px;">
                Utilisateur : <?= htmlspecialchars($_SESSION['user_prenom']) ?> <?= htmlspecialchars($_SESSION['user_nom']) ?>
            </p>
        </div>
    </main>

</body>
</html>