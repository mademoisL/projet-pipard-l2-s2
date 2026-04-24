<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMARTPARK | Inscription</title>
    <link rel="stylesheet" href="../CSS/Style.css">
</head>

<body class="page-centree"> <main>
        <div class="login-box" style="max-width: 500px;"> 
            
            <a href="accueil.php" class="nav-left" style="justify-content: center; text-decoration: none; margin-bottom: 30px;">
                <img src="../Images/Logo sans nom.png" alt="Logo" class="header-logo">
                <div class="logo-text">SMARTPARK</div>
            </a>

            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="font-weight: 300; letter-spacing: 2px;">Créer un compte</h3>
            </div>

            <form action="../PHP/traiter_inscription.php" method="POST">
                
                <div class="input-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" placeholder="Votre prénom" required>
                </div>

                <div class="input-group">
                    <label>Nom</label>
                    <input type="text" name="nom" placeholder="Votre nom" required>
                </div>

                <div class="input-group">
                    <label>Adresse email</label>
                    <input type="email" name="email" placeholder="nom@exemple.com" required>
                </div>

                <div class="input-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Minimum 6 caractères" required>
                </div>

                <button type="submit" class="btn">Je m'inscris !</button>
            </form>

            <div class="links" style="justify-content: center;">
                <a href="Connexion.php">Déjà un compte ? Se connecter</a>
            </div>
        </div>
    </main>

    <?php 
    if(isset($_GET['erreur']) && $_GET['erreur'] == 'existant') {
        echo "<script>alert('Cet email est déjà utilisé par un autre utilisateur.');</script>";
    }
    ?>
</body>
</html>