<?php
session_start();

require_once '../PHP/connexion_bdd.php';

require_once '../PHP/fonctions_parking.php';

//Sécurité : utilisateur connecté ?
if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php");
    exit();
}

//Vérification de l'ID en URL
if (!isset($_GET['id'])) {
    header("Location: Places.php");
    exit();
}

$place_id = (int) $_GET['id'];

//Récupération de la place via fonction
$place = getPlaceParId($pdo, $place_id);

//Place inexistante ou plus libre
if (!$place || $place['statut'] !== 'Libre') {
    header("Location: Places.php?erreur=indisponible");
    exit();
}

//Confirmation de la réservation via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Appel de fonction changerStatutPlace()
    if (changerStatutPlace($pdo, $place_id, 'Réservée')) {
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
        <div class="login-box" style="max-width:500px; text-align:center;">
            <a href="Places.php" style="text-decoration:none; color:#888; font-size:0.8rem; display:block; margin-bottom:20px;">← Retour au parking</a>

            <h2 style="color:var(--accent-color); margin-bottom:10px;">Réserver la place</h2>
            <h1 style="font-size:4rem; margin:20px 0;"><?= htmlspecialchars($place['numero_place']) ?></h1>

            <p style="color:#ccc; margin-bottom:30px;">
                Voulez-vous confirmer la réservation de cette place ?<br>
                Elle vous sera réservée pendant votre trajet.
            </p>

            <form method="POST">
                <button type="submit" class="btn">CONFIRMER LA RÉSERVATION</button>
            </form>

            <p style="font-size:0.7rem; color:#666; margin-top:20px;">
                Utilisateur : <?= htmlspecialchars($_SESSION['user_prenom']) ?> <?= htmlspecialchars($_SESSION['user_nom']) ?>
            </p>
        </div>
    </main>
</body>
</html>