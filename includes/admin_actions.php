<?php
/* =========================================================
   1. TRAITEMENT DES ACTIONS (INSERT / UPDATE / DELETE)
========================================================= */
if (isset($_SESSION['admin_auth'])) {
   
    // --- SUPPRESSION ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete') {
        $table = $_GET['table'];
        $id_col = $_GET['id_col'];
        $id_val = intval($_GET['id_val']);
        if (in_array($table, $allowedTables)) {
            $pdo->prepare("DELETE FROM `$table` WHERE `$id_col` = ?")->execute([$id_val]);
        }
        header("Location: index.php?page=admin&active=$table"); exit;
    }

    // --- SAUVEGARDE DES HORAIRES ---
    if (isset($_POST['save_horaires'])) {
        $id_ent = intval($_POST['id_entreprise']);
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $pdo->prepare("DELETE FROM Horaires WHERE id_entreprise = ?")->execute([$id_ent]);
        $stmt = $pdo->prepare("INSERT INTO Horaires (id_entreprise, jour, heure_ouverture, heure_fermeture) VALUES (?, ?, ?, ?)");
        foreach ($jours as $j) {
            $ouv = $_POST['ouv_' . $j];
            $fer = $_POST['fer_' . $j];
            if (!empty($ouv) && !empty($fer)) { $stmt->execute([$id_ent, $j, $ouv, $fer]); }
        }
        header("Location: index.php?page=admin&active=Horaires&id_ent=$id_ent"); exit;
    }

    // --- SAUVEGARDE MÉTIER ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_metier'])) {
        $nom = $_POST['nom_metier'];
        $id_cat = $_POST['id_categorie'];
        if (!empty($id_cat)) {
            $pdo->prepare("UPDATE Categorie SET nom_metier = ? WHERE id_categorie = ?")->execute([$nom, $id_cat]);
        } else {
            $pdo->prepare("INSERT INTO Categorie (nom_metier) VALUES (?)")->execute([$nom]);
        }
        header("Location: index.php?page=admin&active=Categorie"); exit;
    }

    // --- SAUVEGARDE ENTREPRISE (AVEC LOGO & GÉO) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_entreprise'])) {
        $id = $_POST['id_entreprise'];
        $logoPath = null;

        // Gestion Upload Logo
        if (!empty($_FILES['logo']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['logo']['tmp_name']);
            if (in_array($fileType, $allowedTypes)) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                $newName = uniqid('logo_') . '.' . $ext;
                $uploadDir = __DIR__ . '/../uploads/logos/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $newName)) {
                    $logoPath = 'uploads/logos/' . $newName;
                }
            }
        }

        // Géocodage
        $adresseBrute = $_POST['adresse_complete'];
        $rue = $adresseBrute; $cp = ""; $ville = "";
        if (preg_match('/^(.*?)(\d{5})\s+(.*)$/', $adresseBrute, $m)) {
            $rue = trim(str_replace(',', '', $m[1])); $cp = $m[2]; $ville = trim($m[3]);
        }
        $gps = null;
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=".urlencode($adresseBrute)."&limit=1";
        $opts = ['http' => ['header' => "User-Agent: MapApp/1.0\r\n"]];
        $res = @file_get_contents($url, false, stream_context_create($opts));
        if ($res) {
            $d = json_decode($res, true);
            if (!empty($d)) { $gps = $d[0]['lat'] . ',' . $d[0]['lon']; }
        }

        $site_web = trim($_POST['site_web']);
        if (!empty($site_web) && !preg_match("~^(?:f|ht)tps?://~i", $site_web)) { $site_web = "https://" . $site_web; }

        $params = [$_POST['nom'], $_POST['desc'], $_POST['tel'], $site_web];

        if (!empty($id)) {
            $sql = "UPDATE Entreprise SET nom_entreprise=?, description=?, telephone=?, site_web=?";
            if ($logoPath) { $sql .= ", logo=?"; $params[] = $logoPath; }
            $sql .= " WHERE id_entreprise=?";
            $params[] = $id;
            $pdo->prepare($sql)->execute($params);
            $pdo->prepare("UPDATE Localisation SET adresse=?, code_postale=?, ville=?, gps=? WHERE id_entreprise=?")->execute([$rue, $cp, $ville, $gps, $id]);
            $target_id = $id;
        } else {
            $sql = "INSERT INTO Entreprise (nom_entreprise, description, telephone, site_web" . ($logoPath ? ", logo" : "") . ") VALUES (?,?,?,?" . ($logoPath ? ",?" : "") . ")";
            if ($logoPath) { $params[] = $logoPath; }
            $pdo->prepare($sql)->execute($params);
            $target_id = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO Localisation (adresse, code_postale, ville, gps, id_entreprise) VALUES (?,?,?,?,?)")->execute([$rue, $cp, $ville, $gps, $target_id]);
        }

        // Catégories
        $pdo->prepare("DELETE FROM Entreprise_Categorie WHERE id_entreprise = ?")->execute([$target_id]);
        if (!empty($_POST['metiers'])) {
            foreach ($_POST['metiers'] as $m_id) { $pdo->prepare("INSERT INTO Entreprise_Categorie (id_entreprise, id_categorie) VALUES (?,?)")->execute([$target_id, $m_id]); }
        }
        header("Location: index.php?page=admin&active=Entreprise"); exit;
    }
    
    // --- VALIDATION COMMENTAIRE ---
    if (isset($_GET['action']) && $_GET['action'] == 'validate' && $activeTable == 'Commentaire') {
        $id = intval($_GET['id']);
        $pdo->prepare("UPDATE Commentaire SET statut='valide' WHERE id_commentaire=?")->execute([$id]);
        header("Location: index.php?page=admin&active=Commentaire");
        exit;
    }
}
?>