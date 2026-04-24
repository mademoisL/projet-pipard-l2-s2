<?php

//Fonctions liées au parking (places et réservations)


// Fonction avec Switch pour retourner la classe CSS du statut
function getStatutClasse($statut) {
    switch ($statut) {
        case 'Libre':        return 'statut-libre';
        case 'Occupee':      return 'statut-occupee';
        case 'Reservee':     return 'statut-reservee';
        case 'Hors Service': return 'statut-hors-service';
        default:             return 'statut-default';
    }
}

// Fonction pour récupérer toutes les places, retourne un tableau
function getAllPlaces($db) {
    $stmt = $db->prepare("SELECT * FROM places ORDER BY numero_place ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer une place par son ID
function getPlaceParId($db, $id) {
    $stmt = $db->prepare("SELECT * FROM places WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour changer le statut d'une place
function changerStatutPlace($db, $id, $statut) {
    $stmt = $db->prepare("UPDATE places SET statut = ? WHERE id = ?");
    return $stmt->execute([$statut, $id]);
}
?>