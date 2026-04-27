<div class="main-map">
    <div class="side-list">
        <div style="padding:15px; border-bottom:1px solid #eee">
            <input type="text" id="searchInput" placeholder="Rechercher..." onkeyup="filtrerTout()" style="margin-bottom:10px;">
            <select id="catFiltre" onchange="filtrerTout()">
                <option value="all">Tous les métiers</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= htmlspecialchars(strtolower($c['nom_metier'])) ?>"><?= htmlspecialchars($c['nom_metier']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="entrepriseList" style="overflow-y:auto; flex:1">
            <?php foreach($entreprises as $e): ?>
                <div class="ent-item" data-nom="<?= strtolower($e['nom_entreprise']) ?>" data-metiers="<?= strtolower($e['metiers'] ?? '') ?>" onclick='openPanel(<?= json_encode($e) ?>)'>
                    <strong><?= htmlspecialchars($e['nom_entreprise']) ?></strong><br>
                    <p>📍 <?= htmlspecialchars(($e['ville'] ?: 'Non renseigné')) ?></p>
                    <span class="tag-metier"><?= htmlspecialchars($e['metiers'] ?? '') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="map"></div>
</div>

<div id="entreprisePanel">
    <div class="panel-content">
        <span class="close-btn" onclick="closePanel()">×</span>
        <h2 id="panelNom"></h2><hr>
        <div class="panel-top">
            <img id="panelLogo" class="panel-logo" src="" alt="Logo">
            <div style="flex:1">
                <p id="panelMetiers" style="font-weight:bold; color:var(--accent); margin-top:0;"></p>
                <p id="panelAdresse" style="font-size:0.9em;"></p>
                <p id="panelTel" style="font-size:0.9em;"></p>
            </div>
        <carte.php/div>
        <p id="panelDesc" style="color:#555; background:#f9f9f9; padding:15px; border-radius:5px; font-size:0.9em;"></p>
        <h4 style="margin-top:20px; border-bottom:1px solid #eee; padding-bottom:5px;">🕒 Horaires</h4>
        <div id="panelHoraires" style="line-height:1.6; margin-bottom:20px; font-size: 0.9em;"></div>
        <h4 style="margin-top:20px; border-bottom:1px solid #eee; padding-bottom:5px;">💬 Avis</h4>
        <div id="panelCommentaires" style="margin-bottom:20px;"></div>

        <h4>📝 Laisser un avis</h4>
        <form method="POST">
            <input type="hidden" name="id_entreprise" id="formEntrepriseId">
            <input type="text" name="nom_invite" placeholder="Votre nom">
            <select name="note">
                <option value="">Note</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="5">5 ⭐</option>
            </select>
            <textarea name="contenu" placeholder="Votre commentaire..." required></textarea>
            <button type="submit" name="save_commentaire" class="btn">Envoyer</button>
        </form>
        <a id="panelWeb" href="#" target="_blank" class="btn" style="width:100%">Consulter le site Web</a>
    </div>
</div>