<?php
session_start();
require_once '../PHP/connexion_bdd.php';

if (!isset($_SESSION['user_id'])) { header("Location: connexion.php"); exit(); }

$id  = $_SESSION['user_id'];
$msg = "";

if ($_POST['action'] ?? '' === 'modifier_profil') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) { $msg = "❌ Email invalide."; }
    else {
        $pdo->prepare("UPDATE utilisateurs SET prenom=?,nom=?,email=?,telephone=? WHERE id=?")
            ->execute([htmlspecialchars($_POST['prenom']), htmlspecialchars($_POST['nom']), $email, htmlspecialchars($_POST['telephone']), $id]);
        $pdo->prepare("INSERT INTO historique (utilisateur_id,action) VALUES (?,?)")->execute([$id,"Mise à jour du profil"]);
        $_SESSION['user_prenom'] = htmlspecialchars($_POST['prenom']);
        $msg = "✅ Profil mis à jour !";
    }
}

if ($_POST['action'] ?? '' === 'ajouter_vehicule') {
    $i = htmlspecialchars($_POST['immatriculation']); $ma = htmlspecialchars($_POST['marque']); $mo = htmlspecialchars($_POST['modele']);
    if ($i && $ma && $mo) {
        $pdo->prepare("INSERT INTO vehicules (utilisateur_id,immatriculation,marque,modele) VALUES (?,?,?,?)")->execute([$id,$i,$ma,$mo]);
        $pdo->prepare("INSERT INTO historique (utilisateur_id,action) VALUES (?,?)")->execute([$id,"Ajout du véhicule $i"]);
        $msg = "✅ Véhicule ajouté !";
    }
}

if ($_POST['action'] ?? '' === 'supprimer_vehicule') {
    $vid = (int)$_POST['vehicule_id'];
    $v = $pdo->prepare("SELECT immatriculation FROM vehicules WHERE id=? AND utilisateur_id=?");
    $v->execute([$vid,$id]); $row = $v->fetch();
    if ($row) {
        $pdo->prepare("DELETE FROM vehicules WHERE id=?")->execute([$vid]);
        $pdo->prepare("INSERT INTO historique (utilisateur_id,action) VALUES (?,?)")->execute([$id,"Suppression du véhicule ".$row['immatriculation']]);
        $msg = "✅ Véhicule supprimé.";
    }
}

// Lecture BDD
$user      = $pdo->prepare("SELECT * FROM utilisateurs WHERE id=?"); $user->execute([$id]); $user = $user->fetch();
$vehicules = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id=?"); $vehicules->execute([$id]); $vehicules = $vehicules->fetchAll();
$capteurs  = $pdo->query("SELECT c.type_appareil,c.etat,p.numero_place FROM capteurs c JOIN places p ON p.id=c.place_id")->fetchAll();
$histo     = $pdo->prepare("SELECT action,date_action FROM historique WHERE utilisateur_id=? ORDER BY date_action DESC LIMIT 5"); $histo->execute([$id]); $histo = $histo->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SMARTPARK – Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../Images/Logo sans nom.png">
    <link rel="stylesheet" href="../CSS/Style.css">
</head>
<body>
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
        <li><a href="../PHP/logout.php" class="lien-deconnexion">Déconnexion</a></li>
    </ul>
</nav>

<main class="profil-main">
    <h1 class="profil-titre">Mon Espace Personnel</h1>
    <p class="profil-sous-titre">Bienvenue, <strong><?= htmlspecialchars($user['prenom'].' '.$user['nom']) ?></strong></p>

    <?php if ($msg): ?>
        <p class="profil-message"><?= $msg ?></p>
    <?php endif; ?>

    <div class="grid-container profil-grid">

        <!-- Infos personnelles -->
        <div class="card">
            <h3 class="card-titre">Infos Personnelles</h3>
            <form method="POST" action="Profil.php">
                <input type="hidden" name="action" value="modifier_profil">
                <div class="input-group"><label>Prénom</label><input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required></div>
                <div class="input-group"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required></div>
                <div class="input-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
                <div class="input-group"><label>Téléphone</label><input type="tel" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>"></div>
                <button type="submit" class="btn">Enregistrer</button>
            </form>
        </div>

        <!-- Véhicules -->
        <div class="card">
            <h3 class="card-titre">Mes Véhicules</h3>
            <table class="profil-table">
                <thead><tr><th>Immatriculation</th><th>Marque</th><th>Modèle</th><th></th></tr></thead>
                <tbody>
                    <?php if (empty($vehicules)): ?>
                        <tr><td colspan="4" class="table-vide">Aucun véhicule.</td></tr>
                    <?php else: foreach ($vehicules as $v): ?>
                        <tr>
                            <td><?= htmlspecialchars($v['immatriculation']) ?></td>
                            <td><?= htmlspecialchars($v['marque']) ?></td>
                            <td><?= htmlspecialchars($v['modele']) ?></td>
                            <td>
                                <form method="POST" action="Profil.php">
                                    <input type="hidden" name="action" value="supprimer_vehicule">
                                    <input type="hidden" name="vehicule_id" value="<?= $v['id'] ?>">
                                    <button type="submit" class="btn-suppr">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
            <h4 class="sous-titre-form">Ajouter un véhicule</h4>
            <form method="POST" action="Profil.php" class="form-vehicule">
                <input type="hidden" name="action" value="ajouter_vehicule">
                <input type="text" name="immatriculation" placeholder="AA-123-BB" required>
                <input type="text" name="marque" placeholder="Marque" required>
                <input type="text" name="modele" placeholder="Modèle" required>
                <button type="submit" class="btn btn-outline">+ Ajouter</button>
            </form>
        </div>

        <!-- Capteurs -->
        <div class="card">
            <h3 class="card-titre">État des capteurs</h3>
            <ul class="capteurs-liste">
                <?php foreach ($capteurs as $c):
                    $couleur = $c['etat'] === 'En ligne' ? '#00ffcc' : ($c['etat'] === 'Maintenance' ? '#ffcc00' : '#ff4d4d'); ?>
                    <li>
                        <span style="color:<?= $couleur ?>">●</span>
                        <?= htmlspecialchars($c['type_appareil']) ?>
                        <span class="capteur-place">Place <?= htmlspecialchars($c['numero_place']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Historique -->
        <div class="card">
            <h3 class="card-titre">Dernière Activité</h3>
            <?php if (empty($histo)): ?>
                <p class="table-vide">Aucune activité.</p>
            <?php else: foreach ($histo as $h): ?>
                <div class="histo-item">
                    <strong><?= date('d/m H:i', strtotime($h['date_action'])) ?></strong>
                    — <?= htmlspecialchars($h['action']) ?>
                </div>
            <?php endforeach; endif; ?>
        </div>

    </div>
</main>

<footer>
    <span>contact@smartpark.com</span>
    <span><a href="FAQ.html">FAQ</a> · <a href="AIDE.html">Aide</a></span>
    <span>© 2026 SMARTPARK</span>
</footer>
</body>
</html>