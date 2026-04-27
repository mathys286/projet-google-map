<?php if (!isset($_SESSION['admin_auth'])): ?>
    <div class="login-box">
        <h3>Accès Sécurisé</h3>
        <?php if($login_error): ?><p style="color:red;"><?= $login_error ?></p><?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Code d'accès" required autofocus>
            <button type="submit" name="login_action" class="btn" style="width:100%; margin-top:10px;">Entrer</button>
        </form>
    </div>
<?php else: ?>
    <div class="admin-wrap">
        <div class="admin-menu">
            <?php foreach($allowedTables as $t): ?>
                <a href="index.php?page=admin&active=<?= $t ?>" class="<?= $activeTable == $t ? 'active' : '' ?>">● <?= $t ?></a>
            <?php endforeach; ?>
        </div>
        <div class="admin-body">

            <?php if($activeTable == 'Horaires'): ?>
            <div class="form-card">
                <h3>Gérer les horaires</h3>
                <form method="GET">
                    <input type="hidden" name="page" value="admin"><input type="hidden" name="active" value="Horaires">
                    <select name="id_ent" onchange="this.form.submit()">
                        <option value="">-- Sélectionner une entreprise --</option>
                        <?php foreach($entreprises as $ent): ?>
                            <option value="<?= $ent['id_entreprise'] ?>" <?= $currentIdEnt == $ent['id_entreprise'] ? 'selected' : '' ?>><?= htmlspecialchars($ent['nom_entreprise']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php if($currentIdEnt): ?>
                <div style="margin-top:20px; padding-top:20px; border-top:1px solid #eee;">
                    <button type="button" class="btn btn-magic" onclick="copyMonday()">✨ Copier le Lundi sur toute la semaine</button>
                    <form method="POST">
                        <input type="hidden" name="id_entreprise" value="<?= $currentIdEnt ?>">
                        <div class="h-row"><strong>Jour</strong><strong>Ouverture</strong><strong>Fermeture</strong></div>
                        <?php $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                        foreach($jours as $j):
                            $o = isset($selectedEntHoraires[$j]) ? $selectedEntHoraires[$j]['heure_ouverture'] : '';
                            $f = isset($selectedEntHoraires[$j]) ? $selectedEntHoraires[$j]['heure_fermeture'] : '';
                        ?>
                        <div class="h-row">
                            <span><?= $j ?></span>
                            <input type="time" name="ouv_<?= $j ?>" id="ouv_<?= $j ?>" value="<?= $o ?>">
                            <input type="time" name="fer_<?= $j ?>" id="fer_<?= $j ?>" value="<?= $f ?>">
                        </div>
                        <?php endforeach; ?>
                        <button type="submit" name="save_horaires" class="btn btn-success" style="margin-top:15px;">Enregistrer les horaires</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($activeTable == 'Entreprise'): ?>
            <div class="form-card">
                <h3><?= $editData ? 'Modifier' : 'Ajouter' ?> une Entreprise</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_entreprise" value="<?= $editData['id_entreprise'] ?? '' ?>">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px">
                        <input type="text" name="nom" placeholder="Nom" value="<?= $editData['nom_entreprise'] ?? '' ?>" required>
                        <input type="text" name="adresse_complete" placeholder="Adresse, CP Ville" value="<?= $editData ? ($editData['adresse'].', '.$editData['code_postale'].' '.$editData['ville']) : '' ?>" required>
                        <div class="optional-field"><input type="text" name="tel" placeholder="Téléphone" value="<?= $editData['telephone'] ?? '' ?>"></div>
                        <div class="optional-field"><input type="text" name="site_web" placeholder="Site Web" value="<?= $editData['site_web'] ?? '' ?>"></div>
                        <div style="grid-column: span 2;">
                            <select name="metiers[]" multiple style="height: 80px;">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>" <?= in_array($cat['id_categorie'], $currentMetiers) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nom_metier']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="optional-field" style="grid-column: span 2">
                            <label style="font-size:0.8em; color:#666;">Logo (PNG, JPG, WEBP)</label>
                            <input type="file" name="logo" accept="image/*">
                            <textarea name="desc" placeholder="Description"><?= $editData['description'] ?? '' ?></textarea>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px;">
                        <button type="submit" name="save_entreprise" class="btn">Enregistrer</button>
                        <label><input type="checkbox" id="toggleDetails" checked> Options avancées</label>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <?php if($activeTable == 'Categorie'): ?>
            <div class="form-card">
                <h3><?= $editData ? 'Modifier le métier' : 'Ajouter un métier' ?></h3>
                <form method="POST">
                    <input type="hidden" name="id_categorie" value="<?= $editData['id_categorie'] ?? '' ?>">
                    <input type="text" name="nom_metier" placeholder="Nom du métier" value="<?= $editData['nom_metier'] ?? '' ?>" required>
                    <button type="submit" name="save_metier" class="btn"><?= $editData ? 'Mettre à jour' : 'Ajouter' ?></button>
                </form>
            </div>
            <?php endif; ?>

            <?php if($activeTable != 'Horaires'): ?>
            <h3>Table : <?= $activeTable ?></h3>
            <?php $data = $pdo->query("SELECT * FROM `$activeTable` LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
            if($data): $pk = array_keys($data[0])[0]; ?>
            <table>
                <thead><tr><?php foreach(array_keys($data[0]) as $h): ?><th><?= $h ?></th><?php endforeach; ?><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($data as $r): ?>
                    <tr><?php foreach($r as $v): ?><td><?= htmlspecialchars($v ?? '') ?></td><?php endforeach; ?>
                        <td>
                        <?php if($activeTable == 'Commentaire'): ?>
                            <?php if($r['statut'] == 'en_attente'): ?>
                                <a href="index.php?page=admin&active=Commentaire&action=validate&id=<?= $r['id_commentaire'] ?>" style="color:green; font-weight:bold;">✔ Valider</a> |
                            <?php else: ?>
                                <span style="color:gray;">Déjà validé</span> |
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="index.php?page=admin&active=<?= $activeTable ?>&action=delete&table=<?= $activeTable ?>&id_col=<?= $pk ?>&id_val=<?= $r[$pk] ?>" style="color:red;" onclick="return confirm('Supprimer ?')">🗑 Suppr</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?><p>Aucune donnée.</p><?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>