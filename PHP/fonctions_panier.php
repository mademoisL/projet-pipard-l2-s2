<?php
//Fonctions liées au panier (session)

// Fonction avec Switch pour retourner le prix selon l'offre
function getPrixOffre($nomOffre) {
    switch ($nomOffre) {
        case 'Standard': return '5,99 €';
        case 'Premium':  return '12,99 €';
        default:         return '0,00 €';
    }
}

// Fonction pour ajouter une offre au panier
function ajouterAuPanier($nomOffre, $produitId) {
    $_SESSION['panier'] = [
        'nom'  => $nomOffre,
        'prix' => getPrixOffre($nomOffre),
        'id'   => $produitId
    ];
}

// Fonction pour vider le panier
function viderPanier() {
    unset($_SESSION['panier']);
}

// Fonction retournant true si le panier est vide
function panierEstVide() {
    return empty($_SESSION['panier']);
}
?>