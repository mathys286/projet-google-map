<?php
/* =========================================================
   COMMENTAIRES PUBLICS
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_commentaire'])) {

    $id_ent = intval($_POST['id_entreprise']);
    $contenu = trim($_POST['contenu']);
    $nom_invite = trim($_POST['nom_invite']);
    $note = !empty($_POST['note']) ? intval($_POST['note']) : null;

    if (!empty($contenu)) {
        $pdo->prepare("
            INSERT INTO Commentaire 
            (id_entreprise, nom_invite, contenu, note, statut) 
            VALUES (?, ?, ?, ?, 'en_attente')
        ")->execute([
            $id_ent,
            $nom_invite ?: null,
            htmlspecialchars($contenu),
            $note
        ]);
    }

    header("Location: index.php?page=carte");
    exit;
}
?>