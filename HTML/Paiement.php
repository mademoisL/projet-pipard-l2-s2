<?php
session_start();
require_once '../PHP/connexion_bdd.php';
require_once '../PHP/fonctions_panier.php';
require_once '../PHP/fonctions_utilisateurs.php';

// Sécurité : utilisateur connecté et panier non vide
if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php");
    exit();
}
if (panierEstVide()) {
    header("Location: Panier.php");
    exit();
}

$panier = $_SESSION['panier'];

// Confirmation au POST : on active l'abonnement et on vide le panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET abonnement_id = ? WHERE id = ?");
    $stmt->execute([$panier['id'], $_SESSION['user_id']]);

    ajouterHistorique($pdo, $_SESSION['user_id'], "Souscription à l'abonnement " . $panier['nom']);

    viderPanier();
    header("Location: Profil.php?abonnement=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SMARTPARK | Paiement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Images/Logo sans nom.png">
    <link rel="stylesheet" href="../CSS/Style.css">
</head>
<body class="page-centree">

<main>
    <div class="login-box" style="max-width:500px; text-align:center;">

        <a href="accueil.html" class="nav-left" style="justify-content:center; text-decoration:none; margin-bottom:30px;">
            <img src="../Images/Logo sans nom.png" alt="Logo" class="header-logo">
            <div class="logo-text">SMARTPARK</div>
        </a>

        <h2 style="margin-bottom:5px;">Récapitulatif</h2>
        <p style="color:#888; margin-bottom:30px;">Vérifiez votre commande avant de confirmer.</p>

        <div style="background:rgba(255,255,255,0.05); border-radius:10px; padding:20px; margin-bottom:30px;">
            <p style="color:#aaa; font-size:0.85rem;">Abonnement sélectionné</p>
            <p style="font-size:1.4rem; font-weight:bold; color:var(--accent-color);">
                <?= htmlspecialchars($panier['nom']) ?>
            </p>
            <p style="font-size:2rem; font-weight:bold;"><?= $panier['prix'] ?></p>
            <p style="color:#888; font-size:0.8rem;">/ mois — sans engagement</p>
        </div>

        <form method="POST" action="Paiement.php">
            <button type="submit" class="btn">Confirmer et payer</button>
        </form>

        <a href="Panier.php" style="display:block; margin-top:15px; color:#888; font-size:0.85rem; text-decoration:none;">
            ← Retour au panier
        </a>
    </div>
</main>

</body>
</html>