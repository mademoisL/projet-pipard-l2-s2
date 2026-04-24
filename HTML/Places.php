<?php
session_start();
require_once '../PHP/connexion_bdd.php';

// 1. Récupérer toutes les places
$query = $pdo->query("SELECT * FROM places ORDER BY numero_place ASC");
$places = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SMARTPARK – Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Images/Logo sans nom.png">
    <link rel="stylesheet" href="../CSS/Style.css">

    <nav>
    <div class="nav-left">
        <img src="../Images/Logo sans nom.png" alt="Logo SmartPark" class="header-logo">
        <div class="logo-text">SMARTPARK</div>
    </div>
    <ul class="nav-links">
        <li><a href="accueil.html">Accueil</a></li>
        <li><a href="Abonnements.html">Nos abonnements</a></li>
        <li><a href="Presentation_des_appareils.html">Nos appareils</a></li>
        <li><a href="Nous.html">À propos de nous</a></li>
        <li><a href="Contacts.html">Contacts</a></li>
        <li><a href="Places.php">Parking en temps réel</a></li>
        <li><a href="../PHP/logout.php" style="color: #ff4d4d;">Déconnexion</a></li>
    </ul>
</nav>

    <style>
        /* Styles spécifiques pour la grille de parking */
        .parking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .place-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid transparent;
            transition: 0.3s;
        }

        /* Couleurs selon le statut */
        .statut-libre { border-color: #00ffcc; color: #00ffcc; background: rgba(0, 255, 204, 0.05); }
        .statut-occupee { border-color: #ff4d4d; color: #ff4d4d; background: rgba(255, 0, 0, 0.05); }
        .statut-reservee { border-color: #00d4ff; color: #00d4ff; background: rgba(0, 212, 255, 0.05); }

        .statut-label {
            display: block;
            font-size: 0.8rem;
            margin-top: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php if (isset($_GET['success']) && $_GET['success'] == 'reservee'): ?>
    <div style="background: rgba(0, 255, 204, 0.1); border: 1px solid #00ffcc; color: #00ffcc; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
        Votre place a été réservée avec succès !
    </div>
<?php endif; ?>

<?php if (isset($_GET['erreur']) && $_GET['erreur'] == 'indisponible'): ?>
    <div style="background: rgba(255, 77, 77, 0.1); border: 1px solid #ff4d4d; color: #ff4d4d; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
        Désolé, cette place vient d'être prise ou est indisponible.
    </div>
<?php endif; ?>

    <main style="padding-top: 120px; max-width: 1000px; margin: 0 auto;">
        <h2 style="text-align: center;">État du Parking en Temps Réel</h2>
        
        <div class="parking-grid">
            <?php foreach ($places as $place): 
                // Déterminer la classe CSS selon le statut
                $classeStatut = '';
                if ($place['statut'] == 'Libre') $classeStatut = 'statut-libre';
                elseif ($place['statut'] == 'Occupée') $classeStatut = 'statut-occupee';
                else $classeStatut = 'statut-reservee';
            ?>
                
                <div class="place-card <?= $classeStatut ?> card">
                    <span style="font-size: 1.5rem; font-weight: bold;">
                        <?= htmlspecialchars($place['numero_place']) ?>
                    </span>
                    <span class="statut-label"><?= $place['statut'] ?></span>
                    
                    <?php if ($place['statut'] == 'Libre'): ?>
                        <a href="Reservation.php?id=<?= $place['id'] ?>" class="btn-detail" style="margin-top:15px; font-size:0.7rem;">Réserver</a>
                    <?php endif; ?>
                </div>

            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>