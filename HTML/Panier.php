<?php
session_start(); // On allume la mémoire du serveur

// 1. Si on vient de cliquer sur "Ajouter au panier" dans Abonnements.php
if (isset($_POST['nom_offre'])) {
    $_SESSION['panier'] = [
        'nom' => $_POST['nom_offre'],
        'prix' => ($_POST['nom_offre'] == 'Standard') ? '5,99 €' : '12,99 €',
        'id' => $_POST['produit_id']
    ];
}

// 2. Pour vider le panier (optionnel)
if (isset($_GET['action']) && $_GET['action'] == 'vider') {
    unset($_SESSION['panier']);
    header("Location: Panier.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SMARTPARK | Mon Panier</title>
    <link rel="stylesheet" href="../CSS/Style.css">
    <link rel="icon" type="image/png" href="../Images/Logo sans nom.png">
    <style>
    /* On force la couleur blanche sur tous les états du bouton */
    .btn-paiement, 
    .btn-paiement:visited, 
    .btn-paiement:active, 
    .btn-paiement:hover {
        color: white !important;
        text-decoration: none;
    }
</style>
</head>
<body>

<nav>
    <div class="nav-left">
        <img src="../Images/Logo sans nom.png" alt="Logo SmartPark" class="header-logo">
        <div class="logo-text">SMARTPARK</div>
    </div>
    <ul class="nav-links">
        <li><a href="accueil.html">Accueil</a></li>
        <li><a href="Abonnements.php">Nos abonnements</a></li>
        <li><a href="Panier.php">Mon panier</a></li>
        <li><a href="Connexion.php">Connexion</a></li>
    </ul>
</nav>

<main style="padding: 150px 20px; text-align: center;">
    <h2>Mon Panier</h2>

    <div class="conteneur" style="max-width: 600px; margin: 0 auto; background: rgba(255,255,255,0.05); padding: 40px; border-radius: 15px;">
        
        <?php if (empty($_SESSION['panier'])): ?>
            <div class="empty-cart">
                <p style="font-size: 1.2rem; color: #888;">Votre panier est actuellement vide.</p>
                <a href="Abonnements.php" class="btn" style="display:inline-block; margin-top:20px; width:auto;">Voir les offres</a>
            </div>

        <?php else: ?>
            <div class="cart-item" style="text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px;">
                <h3 style="color: var(--accent-color);"><?php echo $_SESSION['panier']['nom']; ?></h3>
                <p>Abonnement mensuel SmartPark</p>
                <p style="font-size: 1.5rem; font-weight: bold;"><?php echo $_SESSION['panier']['prix']; ?></p>
            </div>

            <div style="margin-top: 30px;">
                <p style="font-size: 0.9rem; margin-bottom: 20px;">
                    <i>Note : La création d'un compte est obligatoire pour finaliser l'achat.</i>
                </p>

                <?php if (isset($_SESSION['user_id'])): ?>
                <div style="margin-top: 30px;">
                <a href="Paiement.php" class="btn-paiement">Procéder au paiement</a>
                </div>
                <?php else: ?>
                    <div style="display: flex; gap: 10px;">
                        <a href="INSCRIPTION.php" class="btn">Créer un compte</a>
                        <a href="Connexion.php" class="btn" style="background: transparent; border: 1px solid white; color: white;">Se connecter</a>
                    </div>
                <?php endif; ?>

                <br>
                <div style="margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px;">
                <a href="Panier.php?action=vider" style="color: #ff4d4d; font-size: 0.8rem; text-decoration: none;">Vider le panier</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

</body>
</html>