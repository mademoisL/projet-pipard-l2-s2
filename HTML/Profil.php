<?php
session_start();

require_once '../PHP/connexion_bdd.php';

require_once '../PHP/fonctions_utilisateurs.php';
 
//Sécurité : redirection si non connecté
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
        // Chap 4 — Validation avant insertion
        if ($immat && $marque && $modele) {
            ajouterVehicule($pdo, $id, $immat, $marque, $modele);
            ajouterHistorique($pdo, $id, "Ajout du véhicule $immat");
        }
        break;
 
    case 'supprimer_vehicule':
        $vehicule_id = (int) ($_POST['vehicule_id'] ?? 0);
        supprimerVehicule($pdo, $vehicule_id);
        ajouterHistorique($pdo, $id, "Suppression d'un véhicule");
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
            <h3 style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:10px; margin-top:0;">Accès aux capteurs</h3>
 
            <?php
            // srand() initialise le générateur avec l'ID utilisateur comme graine.
            // Même utilisateur = toujours les mêmes chiffres fictifs stables entre les rechargements.
            srand($id);
 
            //Tableau associatif : mesures fictives adaptées à chaque type de capteur
            $mesures_par_type = [
                'Capteur de présence au sol'      => ['label' => "Détections aujourd'hui", 'valeur' => rand(0, 40),   'unite' => 'évén.'],
                'Capteur de saturation de zone'   => ['label' => 'Taux de saturation',      'valeur' => rand(20, 95),  'unite' => '%'],
                'Capteur de dépassement de durée' => ['label' => 'Dépassements détectés',   'valeur' => rand(0, 8),    'unite' => 'ce mois'],
                'Signalétique lumineuse LED'       => ['label' => 'Consommation',            'valeur' => rand(1, 5),    'unite' => 'W'],
                "Scanner d'obstacles"            => ['label' => "Obstacles détectés",     'valeur' => rand(0, 3),    'unite' => "aujourd'hui"],
                'Arceau de parking motorisé'      => ['label' => "Ouvertures aujourd'hui", 'valeur' => rand(0, 20),   'unite' => 'fois'],
                'Caméra LAPI'                     => ['label' => 'Plaques scannées',        'valeur' => rand(10, 120), 'unite' => "aujourd'hui"],
                'Borne de recharge intelligente'  => ['label' => 'Énergie délivrée',        'valeur' => rand(5, 80),   'unite' => 'kWh'],
                'Borne NFC / RFID'                => ['label' => 'Validations',             'valeur' => rand(0, 15),   'unite' => "aujourd'hui"],
                'Localisation Bluetooth'          => ['label' => 'Guidages effectués',      'valeur' => rand(0, 10),   'unite' => "aujourd'hui"],
            ];
 
            //Tableau des messages de panne fictifs
            $messages_panne = [
                'Signal intermittent détecté — vérification planifiée.',
                'Calibration requise sous 7 jours.',
                'Température interne élevée — surveillance active.',
                'Mise à jour firmware en attente.',
            ];
 
            //foreach sur le tableau $capteurs retourné par getCapteurs()
            foreach ($capteurs as $i => $c):
                $couleur = getCouleurCapteur($c['etat']);
                $type    = $c['type_appareil'];
                $mesure  = $mesures_par_type[$type] ?? ['label' => 'Activité', 'valeur' => rand(0, 100), 'unite' => ''];
 
                // Batterie et délai fictifs stables par capteur (graine unique par capteur)
                srand($id + $i * 17);
                $batterie = rand(55, 100);
                $derniere = rand(1, 59);
 
                //Message de panne uniquement si etat != 'En ligne'
                $panne_msg = '';
                if ($c['etat'] !== 'En ligne') {
                    srand($id + $i);
                    $panne_msg = $messages_panne[rand(0, count($messages_panne) - 1)];
                }
            ?>
                <div style="border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:15px; margin-bottom:15px;">
 
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <strong style="font-size:0.95rem;">
                            <span style="color:<?= $couleur ?>; margin-right:8px;">●</span>
                            <?= htmlspecialchars($type) ?>
                        </strong>
                        <span style="font-size:0.75rem; color:<?= $couleur ?>; background:rgba(255,255,255,0.05); padding:2px 8px; border-radius:20px;">
                            <?= $c['etat'] ?>
                        </span>
                    </div>
 
                    <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#aaa; margin-bottom:6px;">
                        <span><?= $mesure['label'] ?></span>
                        <span style="color:var(--accent-color); font-weight:bold;">
                            <?= $mesure['valeur'] ?> <?= $mesure['unite'] ?>
                        </span>
                    </div>
 
                    <div style="display:flex; justify-content:space-between; font-size:0.8rem; color:#666;">
                        <span>🔋 Batterie : <?= $batterie ?>%</span>
                        <span>Dernière remontée : il y a <?= $derniere ?> min</span>
                    </div>
 
                    <?php if ($panne_msg): ?>
                        <div style="margin-top:8px; font-size:0.78rem; color:#ffcc00; background:rgba(255,204,0,0.05); padding:6px 10px; border-radius:6px; border-left:3px solid #ffcc00;">
                            ⚠ <?= $panne_msg ?>
                        </div>
                    <?php endif; ?>
 
                </div>
            <?php endforeach; ?>
 
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