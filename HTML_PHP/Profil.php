<?php
session_start();
require_once '../PHP/connexion_bdd.php';

if (!isset($_SESSION['user_id'])) { header("Location: connexion.php"); exit(); }

$id  = $_SESSION['user_id'];
$msg = "";

if ($_POST['action'] ?? '' === 'modifier_profil') {
    $pdo->prepare("UPDATE utilisateurs SET prenom=?,nom=?,email=? WHERE id=?")
        ->execute([$_POST['prenom'], $_POST['nom'], $_POST['email'], $id]);
    $_SESSION['user_prenom'] = $_POST['prenom'];
    $msg = "Modifications enregistrées !";
}
if ($_POST['action'] ?? '' === 'ajouter_vehicule') {
    $i=$_POST['immatriculation']; $ma=$_POST['marque']; $mo=$_POST['modele'];
    if ($i && $ma && $mo) {
        $pdo->prepare("INSERT INTO vehicules (utilisateur_id,immatriculation,marque,modele) VALUES (?,?,?,?)")->execute([$id,$i,$ma,$mo]);
        $pdo->prepare("INSERT INTO historique (utilisateur_id,action) VALUES (?,?)")->execute([$id,"Ajout du véhicule $i"]);
    }
}
if ($_POST['action'] ?? '' === 'supprimer_vehicule') {
    $vid=(int)$_POST['vehicule_id'];
    $row=$pdo->prepare("SELECT immatriculation FROM vehicules WHERE id=? AND utilisateur_id=?");
    $row->execute([$vid,$id]); $row=$row->fetch();
    if ($row) {
        $pdo->prepare("DELETE FROM vehicules WHERE id=?")->execute([$vid]);
        $pdo->prepare("INSERT INTO historique (utilisateur_id,action) VALUES (?,?)")->execute([$id,"Suppression du véhicule ".$row['immatriculation']]);
    }
}

// Lecture BDD
$u = $pdo->prepare("SELECT * FROM utilisateurs WHERE id=?"); $u->execute([$id]); $user=$u->fetch();
$vq = $pdo->prepare("SELECT * FROM vehicules WHERE utilisateur_id=?"); $vq->execute([$id]); $vehicules=$vq->fetchAll();
$capteurs = $pdo->query("SELECT c.type_appareil,c.etat FROM capteurs c")->fetchAll();
$hq = $pdo->prepare("SELECT action,date_action FROM historique WHERE utilisateur_id=? ORDER BY date_action DESC LIMIT 5"); $hq->execute([$id]); $histo=$hq->fetchAll();
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
        <li><a href="../PHP/logout.php" style="color: #ff4d4d;">Déconnexion</a></li>
    </ul>
</nav>

<main style="padding-top: 140px;">
    <h1 style="text-align: center; margin-bottom: 40px;">Mon Espace Personnel</h1>

    <?php if ($msg): ?>
        <p style="text-align:center; color:#00ffcc; margin-bottom:20px;"><?= $msg ?></p>
    <?php endif; ?>

    <div class="grid-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

        <!-- CARTE 1 : Infos personnelles — même structure HTML qu'avant, les value= viennent de la BDD -->
        <div class="card">
            <h3 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-top:0;">Infos Personnelles</h3>
            <form method="POST" action="Profil.php">
                <input type="hidden" name="action" value="modifier_profil">
                <div class="input-group">
                    <label style="color: var(--accent-color); font-size: 0.8rem;">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>">
                </div>
                <div class="input-group">
                    <label style="color: var(--accent-color); font-size: 0.8rem;">Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>">
                </div>
                <div class="input-group">
                    <label style="color: var(--accent-color); font-size: 0.8rem;">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                </div>
                <button class="btn" type="submit">Enregistrer les modifications</button>
            </form>
        </div>

        <!-- CARTE 2 : Véhicules — même tableau HTML, les lignes viennent de la BDD -->
        <div class="card">
            <h3 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-top:0;">Mes Véhicules</h3>
            <table id="vehiculeTable" style="width: 100%; text-align: left; margin-bottom: 20px; border-collapse: collapse;">
                <thead>
                    <tr style="color: var(--accent-color); border-bottom: 1px solid rgba(255,255,255,0.2);">
                        <th style="padding: 10px 0;">Immatriculation</th>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicules as $v): ?>
                    <tr>
                        <td style="padding: 10px 0;"><?= htmlspecialchars($v['immatriculation']) ?></td>
                        <td><?= htmlspecialchars($v['marque']) ?></td>
                        <td><?= htmlspecialchars($v['modele']) ?></td>
                        <td>
                            <form method="POST" action="Profil.php" style="display:inline;">
                                <input type="hidden" name="action" value="supprimer_vehicule">
                                <input type="hidden" name="vehicule_id" value="<?= $v['id'] ?>">
                                <button type="submit" style="padding: 5px 10px; background: #ff4d4d; color: white; border: none; border-radius: 4px; cursor: pointer; width:auto;">X</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($vehicules)): ?>
                        <tr><td colspan="4" style="color:#888; padding:10px 0;">Aucun véhicule enregistré.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h4 style="margin-top: 20px;">Ajouter un véhicule</h4>
            <form method="POST" action="Profil.php">
                <input type="hidden" name="action" value="ajouter_vehicule">
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <input type="text" name="immatriculation" placeholder="Immatriculation" style="margin: 0;">
                    <input type="text" name="marque" placeholder="Marque" style="margin: 0;">
                    <input type="text" name="modele" placeholder="Modèle" style="margin: 0;">
                </div>
                <button class="btn" type="submit" style="background: transparent; border: 1px solid white; color: white;">Ajouter</button>
            </form>
        </div>

        <!-- CARTE 3 : Capteurs — même liste HTML, les données viennent de la BDD -->
        <div class="card">
            <h3 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-top:0;">Accès aux capteurs</h3>
            <ul id="capteursList" style="list-style: none; padding: 0; line-height: 2;">
                <?php foreach ($capteurs as $c):
                    $couleur = $c['etat'] === 'En ligne' ? '#00ffcc' : ($c['etat'] === 'Maintenance' ? '#ffcc00' : '#ff4d4d'); ?>
                    <li>
                        <span style="color:<?= $couleur ?>; margin-right:10px;">●</span>
                        <?= htmlspecialchars($c['type_appareil']) ?>
                        <span style="float:right; color:#888; font-size:0.8rem;"><?= $c['etat'] ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- CARTE 4 : Activité — même structure HTML, les données viennent de la BDD -->
        <div class="card">
            <h3 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-top:0;">Dernière Activité</h3>
            <div id="activiteList" style="color: #ccc; font-size: 0.9rem; line-height: 1.8;">
                <?php foreach ($histo as $h): ?>
                    <div style="border-left: 2px solid var(--accent-color); padding-left: 10px; margin-bottom: 10px;">
                        <strong><?= date('d/m H:i', strtotime($h['date_action'])) ?></strong>
                        - <?= htmlspecialchars($h['action']) ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($histo)): ?>
                    <p style="color:#888;">Aucune activité récente.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4>Contact</h4>
            <p>contact@smartpark.com</p>
        </div>
        <div class="footer-section">
            <h4>Questions / Réponses</h4>
            <a href="FAQ.html">FAQ</a>
            <a href="AIDE.html">Centre d'aide</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 SMARTPARK — Gestion intelligente du stationnement urbain</div>
</footer>

</body>
</html>