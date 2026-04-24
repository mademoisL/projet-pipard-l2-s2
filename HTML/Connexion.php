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
<body class="page-centree" style="display: flex; flex-direction: column; min-height: 100vh; margin: 0;">
    <main style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 80px 20px;">
        <div class="conteneur login-box" style="max-width: 700px; width: 100%; padding: 60px; box-sizing: border-box;">
            
            <a href="accueil.html" class="nav-left" style="justify-content: center; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; text-decoration: none;">
                <img src="../Images/Logo sans nom.png" alt="Logo" class="header-logo" style="height: 45px; width: auto; filter: drop-shadow(0 0 5px white);">
                <div class="logo-text" style="margin: 0; color: white;">SMARTPARK</div>
            </a>

            <div class="ligne-texte">
                <div class="hr-container"></div>
                <h3>Connexion</h3>
                <div class="hr-container"></div>
            </div>

            <?php if ($erreur): ?>
                <p style="color:#ff4d4d; text-align:center; margin-bottom:15px;"><?= $erreur ?></p>
            <?php endif; ?>

            <!-- Seul vrai changement : <form method="POST"> remplace le onclick="login()" -->
            <form method="POST" action="connexion.php">
                <div class="input-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" placeholder="Entrer votre email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Entrer votre mot de passe">
                </div>
                <button type="submit" class="btn">Se connecter</button>
            </form>

            <div class="links">
                <a href="Mdp_oubli.html">Mot de passe oublié ?</a>
                <a href="Inscription.html">Inscription</a>
            </div>
        </div>
    </main>
</body>
</html>