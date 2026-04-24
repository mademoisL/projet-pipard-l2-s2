<?php
session_start();
require_once '../PHP/connexion_bdd.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mdp   = $_POST['password'];

    if (empty($email) || empty($mdp)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['mot_de_passe'] === $mdp) {
            $_SESSION['user_id']     = $user['id'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_nom']    = $user['nom'];
            header("Location: Profil.php");
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMARTPARK | Connexion</title>
    <link rel="stylesheet" href="../CSS/Style.css">
</head>

<body class="page-centree">

    <main>
        <div class="login-box" style="max-width: 500px;">
            
            <a href="accueil.php" class="nav-left" style="justify-content: center; text-decoration: none; margin-bottom: 30px;">
                <img src="../Images/Logo sans nom.png" alt="Logo" class="header-logo">
                <div class="logo-text">SMARTPARK</div>
            </a>

            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="font-weight: 300; letter-spacing: 2px;">Connexion</h3>
            </div>

            <?php if ($erreur): ?>
                <p style="color:#ff4d4d; text-align:center; font-size: 0.9rem; margin-bottom:15px;"><?= $erreur ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['inscrit'])): ?>
                <p style="color:#00ffcc; text-align:center; font-size: 0.9rem; margin-bottom:15px;">Inscription réussie ! Connectez-vous.</p>
            <?php endif; ?>

            <form method="POST" action="Connexion.php">
                <div class="input-group">
                    <label>Adresse email</label>
                    <input type="email" name="email" placeholder="Entrer votre email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Entrer votre mot de passe" required>
                </div>
                
                <button type="submit" class="btn">Se connecter</button>
            </form>

            <div class="links">
                <a href="Mdp_oubli.html">Mot de passe oublié ?</a>
                <a href="Inscription.php">Inscription</a>
            </div>
        </div>
    </main>
</body>
</html>