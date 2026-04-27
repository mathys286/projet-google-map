<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function copyMonday() {
        const oL = document.getElementById('ouv_Lundi').value, fL = document.getElementById('fer_Lundi').value;
        if(!oL || !fL) { alert("Remplis le Lundi !"); return; }
        ['Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'].forEach(j => {
            if(document.getElementById('ouv_'+j)) document.getElementById('ouv_'+j).value = oL;
            if(document.getElementById('fer_'+j)) document.getElementById('fer_'+j).value = fL;
        });
    }

    if (document.getElementById('map')) {
        var map = L.map('map').setView([46.5, 2], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var markers = [];
        var points = <?= json_encode($entreprises ?? []) ?>;
        points.forEach(p => {
            if (p.gps && p.gps.includes(',')) {
                let c = p.gps.split(','), m = L.marker([c[0], c[1]]).addTo(map);
                m.on('click', () => openPanel(p));
                markers.push({ leafletMarker: m, nom: p.nom_entreprise.toLowerCase(), metiers: (p.metiers || "").toLowerCase() });
            }
        });

        function openPanel(data) {
            document.getElementById("panelNom").innerText = data.nom_entreprise || "";
            document.getElementById("panelMetiers").innerText = "🛠 " + (data.metiers || "Non classé");
            document.getElementById("panelAdresse").innerText = "📍 " + (data.adresse || "") + " " + (data.code_postale || "") + " " + (data.ville || "");
            document.getElementById("panelTel").innerText = "📞 " + (data.telephone || "--");
            document.getElementById("panelDesc").innerText = data.description || "Aucune description.";
           
            let img = document.getElementById("panelLogo");
            if(data.logo) { img.src = data.logo; img.style.display = "block"; } else { img.style.display = "none"; }

            let hDiv = document.getElementById("panelHoraires"); hDiv.innerHTML = "";
            if(data.horaires && data.horaires.length > 0) {
                data.horaires.forEach(h => {
                    let o = h.ouverture ? h.ouverture.substring(0,5) : "--", f = h.fermeture ? h.fermeture.substring(0,5) : "--";
                    hDiv.innerHTML += `<div><strong>${h.jour}</strong> : ${o} - ${f}</div>`;
                });
            } else { hDiv.innerHTML = "<em>Horaires non renseignés.</em>"; }

            let web = document.getElementById("panelWeb");
            if (data.site_web) { web.href = data.site_web; web.style.display = "block"; } else { web.style.display = "none"; }

            document.getElementById("entreprisePanel").classList.add("active");
            if(data.gps) { let co = data.gps.split(','); map.setView([co[0], co[1]], 14); }
            
            // --- Commentaires ---
            let cDiv = document.getElementById("panelCommentaires");
            cDiv.innerHTML = "";
            if(data.commentaires && data.commentaires.length > 0) {
                data.commentaires.forEach(c => {
                    let auteur = c.utilisateur_nom || c.nom_invite || "Anonyme";
                    let note = c.note ? "⭐".repeat(c.note) : "";
                    cDiv.innerHTML += `
                         <div style="margin-bottom:15px; padding:10px; background:#f9f9f9; border-radius:5px;">
                            <strong>${auteur}</strong> ${note}<br>
                            <small>${c.date_creation}</small>
                            <p style="margin-top:5px;">${c.contenu}</p>
                         </div>
                    `;
                });
            } else {
                cDiv.innerHTML = "<em>Aucun avis pour le moment.</em>";
            }
            document.getElementById("formEntrepriseId").value = data.id_entreprise;
        }

        function closePanel() { document.getElementById("entreprisePanel").classList.remove("active"); }
       
        function filtrerTout() {
            closePanel();
            let s = document.getElementById('searchInput').value.toLowerCase(), c = document.getElementById('catFiltre').value;
            document.querySelectorAll('.ent-item').forEach((item, i) => {
                let match = item.dataset.nom.includes(s) && (c === 'all' || item.dataset.metiers.includes(c));
                item.style.display = match ? "block" : "none";
                if(markers[i]) { if(match) map.addLayer(markers[i].leafletMarker); else map.removeLayer(markers[i].leafletMarker); }
            });
        }
    }

    if (document.getElementById('toggleDetails')) {
        document.getElementById('toggleDetails').addEventListener('change', function() {
            document.querySelectorAll('.optional-field').forEach(el => el.classList.toggle('optional-hidden', !this.checked));
        });
    }
</script>
<?php include 'views/gps.php'; ?>
</body>
</html>