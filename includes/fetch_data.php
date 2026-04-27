<?php
/* =========================================================
   2. RÉCUPÉRATION DES DONNÉES
========================================================= */
$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_metier")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT E.*, L.gps, L.ville, L.adresse, L.code_postale, GROUP_CONCAT(C.nom_metier SEPARATOR ', ') as metiers
        FROM Entreprise E
        LEFT JOIN Localisation L ON E.id_entreprise = L.id_entreprise
        LEFT JOIN Entreprise_Categorie EC ON E.id_entreprise = EC.id_entreprise
        LEFT JOIN Categorie C ON EC.id_categorie = C.id_categorie
        GROUP BY E.id_entreprise";
$entreprises = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$horairesRaw = $pdo->query("SELECT id_entreprise, jour, heure_ouverture, heure_fermeture FROM Horaires ORDER BY FIELD(jour, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche')")->fetchAll(PDO::FETCH_ASSOC);
$horaires = [];
foreach ($horairesRaw as $h) { $horaires[$h['id_entreprise']][] = ['jour' => $h['jour'], 'ouverture' => $h['ouverture'] ?? $h['heure_ouverture'], 'fermeture' => $h['fermeture'] ?? $h['heure_fermeture']]; }

// --- COMMENTAIRES VALIDÉS ---
$commentairesRaw = $pdo->query("
    SELECT c.*, u.nom AS utilisateur_nom
    FROM Commentaire c
    LEFT JOIN Utilisateur u ON c.id_utilisateur = u.id_utilisateur
    WHERE c.statut = 'valide'
    ORDER BY c.date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);

$commentaires = [];
foreach ($commentairesRaw as $com) {
    $commentaires[$com['id_entreprise']][] = $com;
}

// Association aux entreprises
foreach ($entreprises as &$e) { 
    $e['horaires'] = $horaires[$e['id_entreprise']] ?? []; 
    $e['commentaires'] = $commentaires[$e['id_entreprise']] ?? [];
}
unset($e);

// Données d'édition admin
$selectedEntHoraires = [];
$currentIdEnt = isset($_GET['id_ent']) ? intval($_GET['id_ent']) : null;
if($activeTable == 'Horaires' && $currentIdEnt) {
    $stmt = $pdo->prepare("SELECT * FROM Horaires WHERE id_entreprise = ?");
    $stmt->execute([$currentIdEnt]);
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) { $selectedEntHoraires[$row['jour']] = $row; }
}

$editData = null; $currentMetiers = [];
if (isset($_SESSION['admin_auth']) && isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($activeTable == 'Entreprise') {
        $stmt = $pdo->prepare("SELECT E.*, L.adresse, L.code_postale, L.ville FROM Entreprise E LEFT JOIN Localisation L ON E.id_entreprise = L.id_entreprise WHERE E.id_entreprise = ?");
        $stmt->execute([$editId]); $editData = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmtM = $pdo->prepare("SELECT id_categorie FROM Entreprise_Categorie WHERE id_entreprise = ?");
        $stmtM->execute([$editId]); $currentMetiers = $stmtM->fetchAll(PDO::FETCH_COLUMN);
    } elseif ($activeTable == 'Categorie') {
        $stmt = $pdo->prepare("SELECT * FROM Categorie WHERE id_categorie = ?");
        $stmt->execute([$editId]); $editData = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>