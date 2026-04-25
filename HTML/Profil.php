<?php
session_start();
require_once '../PHP/connexion_bdd.php';
require_once '../PHP/fonctions_utilisateurs.php';
 
// Sécurité : redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php");
    exit();
}
 
$id  = $_SESSION['user_id'];
$msg = "";
 
//Switch sur l'action POST reçue
$action = $_POST['action'] ?? '';
switch ($action) {
 
    case 'modifier_profil':
        mettreAJourProfil(
            $pdo, $id,
            $_POST['prenom']      ?? '',
            $_POST['nom']         ?? '',
            $_POST['email']       ?? '',
            $_POST['telephone']   ?? null,
            $_POST['adresse']     ?? null,
            $_POST['code_postal'] ?? null,
            $_POST['ville']       ?? null
        );
        $_SESSION['user_prenom'] = $_POST['prenom'];
        $msg = "Modifications enregistrées !";
        break;
 
    case 'ajouter_vehicule':
        $immat  = trim($_POST['immatriculation'] ?? '');
        $marque = trim($_POST['marque']          ?? '');
        $modele = trim($_POST['modele']          ?? '');
        //Validation avant insertion
        if ($immat && $marque && $modele) {
            ajouterVehicule($pdo, $id, $immat, $marque, $modele);
            ajouterHistorique($pdo, $id, "Ajout du véhicule $immat");
        }
        break;
 
    case 'supprimer_vehicule':
        $vehicule_id = (int) ($_POST['vehicule_id'] ?? 0);
        supprimerVehicule($pdo, $vehicule_id);
        ajouterHistorique($pdo, $id, "Suppression d'un véhicule"
        );
        break;
 }
//Lecture BDD via fonctions (retournent des tableaux)
$user     = getUtilisateur($pdo, $id);
$vehicules = getVehicules($pdo, $id);
$capteurs  = getCapteurs($pdo, $user['abonnement_id']);
$histo     = getHistorique($pdo, $id, 5);
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
        <li><a href="Abonnements.php">Nos abonnements</a></li>
        <li><a href="Presentation_des_appareils.html">Nos appareils</a></li>
        <li><a href="Nous.html">À propos de nous</a></li>
        <li><a href="Contacts.php">Contacts</a></li>
        <li><a href="Places.php">Disponibilité des places</a></li>
        <li><a href="Panier.php">Mon panier</a></li>
        <li><a href="../PHP/logout.php" style="color:#ff4d4d;">Déconnexion</a></li>
    </ul>
</nav>
 
<main style="padding-top: 140px;">
    <h1 style="text-align: center; margin-bottom: 40px;">Mon Espace Personnel</h1>
 
    <?php if ($msg): ?>
        <p style="text-align:center; color:#00ffcc; margin-bottom:20px;"><?= $msg ?></p>
    <?php endif; ?>
 
    <div style="max-width:1200px; margin:0 auto; padding:0 20px; display:grid; grid-template-columns:1fr 1fr; gap:30px;">
 
        <!-- CARTE 1 : Infos personnelles -->
        <div class="card">
            <h3 style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px; margin-top:0;">Infos Personnelles</h3>
            <form method="POST" action="Profil.php">
                <input type="hidden" name="action" value="modifier_profil">
                <?php
                //Tableau associatif pour les champs du formulaire
                $champs = [
                    'prenom'      => 'Prénom',
                    'nom'         => 'Nom',
                    'email'       => 'Email',
                    'telephone'   => 'Tél',
                    'adresse'     => 'Adresse',
                    'code_postal' => 'Code Postal',
                    'ville'       => 'Ville'
                ];
                //foreach pour générer les champs dynamiquement
                foreach ($champs as $name => $label):
                    $type = ($name === 'email') ? 'email' : 'text';
                    $val  = htmlspecialchars($user[$name] ?? '');
                ?>
                    <div class="input-group">
                        <label style="color:var(--accent-color); font-size:0.8rem;"><?= $label ?></label>
                        <input type="<?= $type ?>" name="<?= $name ?>" value="<?= $val ?>">
                    </div>
                <?php endforeach; ?>
                <button class="btn" type="submit">Enregistrer les modifications</button>
            </form>
        </div>
 
        <!-- CARTE 2 : Véhicules -->
        <div class="card">
            <h3 style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px; margin-top:0;">Mes Véhicules</h3>
            <table style="width:100%; text-align:left; margin-bottom:20px; border-collapse:collapse;">
                <thead>
                    <tr style="color:var(--accent-color); border-bottom:1px solid rgba(255,255,255,0.2);">
                        <th style="padding:10px 0;">Immatriculation</th>
                        <th>Marque</th>
                        <th>Modèle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    //foreach sur le tableau de véhicules retourné par getVehicules()
                    if (empty($vehicules)):
                    ?>
                        <tr><td colspan="4" style="color:#888; padding:10px 0;">Aucun véhicule enregistré.</td></tr>
                    <?php else: ?>
                        <?php foreach ($vehicules as $v): ?>
                        <tr>
                            <td style="padding:10px 0;"><?= htmlspecialchars($v['immatriculation']) ?></td>
                            <td><?= htmlspecialchars($v['marque']) ?></td>
                            <td><?= htmlspecialchars($v['modele']) ?></td>
                            <td>
                                <form method="POST" action="Profil.php" style="display:inline;">
                                    <input type="hidden" name="action" value="supprimer_vehicule">
                                    <input type="hidden" name="vehicule_id" value="<?= $v['id'] ?>">
                                    <button type="submit" style="padding:5px 10px; background:#ff4d4d; color:white; border:none; border-radius:4px; cursor:pointer; width:auto;">X</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
 
            <h4 style="margin-top:20px;">Ajouter un véhicule</h4>
            <form method="POST" action="Profil.php">
                <input type="hidden" name="action" value="ajouter_vehicule">
                <div style="display:flex; gap:10px; margin-bottom:15px;">
                    <input type="text" name="immatriculation" placeholder="Immatriculation" style="margin:0;">
                    <input type="text" name="marque" placeholder="Marque" style="margin:0;">
                    <input type="text" name="modele" placeholder="Modèle" style="margin:0;">
                </div>
                <button class="btn" type="submit" style="background:transparent; border:1px solid white; color:white;">Ajouter</button>
            </form>
        </div>
 
        <!-- CARTE 3 : Capteurs -->
        <div class="card">
            <h3>État du Système</h3>
            <div class="capteurs-list">
                <?php foreach ($capteurs as $capteur): ?>
    <div class="capteur-item">
        <span><?= htmlspecialchars($capteur['type_appareil']) ?></span>
        
        <span class="status-dot" style="background-color: <?= getCouleurCapteur($capteur['etat']) ?>;"></span>
        
        <span style="color: <?= getCouleurCapteur($capteur['etat']) ?>;">
            <?= htmlspecialchars($capteur['etat']) ?>
        </span>
    </div>
<?php endforeach; ?>
            </div>
        </div>
 
        <!-- CARTE 4 : Activité -->
        <div class="card">
            <h3 style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px; margin-top:0;">Dernière Activité</h3>
            <div style="color:#ccc; font-size:0.9rem; line-height:1.8;">
                <?php if (empty($histo)): ?>
                    <p style="color:#888;">Aucune activité récente.</p>
                <?php else: ?>
                    <?php foreach ($histo as $h): ?>
                        <div style="border-left:2px solid var(--accent-color); padding-left:10px; margin-bottom:10px;">
                            <strong><?= date('d/m H:i', strtotime($h['date_action'])) ?></strong>
                            — <?= htmlspecialchars($h['action']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
 
    </div>
</main>
 
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h4>Contact</h4>
            <p>contact@smartpark.com</p>
        </div>
        <div class="footer-section">
            <h4>Questions / Réponses</h4>
            <a href="FAQ.php">FAQ</a>
            <a href="AIDE.php">Centre d'aide</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 SMARTPARK — Gestion intelligente du stationnement urbain</div>
</footer>
 
</body>
</html>