<!-- Fichier : gps.php -->
<script>
    // Objet global pour stocker la position de l'appareil
    const Appareil = {
        lat: null,
        lng: null,
        status: "recherche...",

        // Fonction pour localiser l'utilisateur
        localiser: function() {
            if (!navigator.geolocation) {
                this.status = "Non supporté par le navigateur";
                console.error(this.status);
                return;
            }

            // Option de haute précision pour mobile
            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            };

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.lat = position.coords.latitude;
                    this.lng = position.coords.longitude;
                    this.status = "Localisé";
                    
                    // On peut envoyer un événement pour dire que la position est prête
                    console.log("📍 Appareil situé : " + this.lat + ", " + this.lng);
                    
                    // Optionnel : Si une carte existe, on peut y placer un marqueur "Moi"
                    if (typeof map !== 'undefined') {
                        L.marker([this.lat, this.lng])
                         .addTo(map)
                         .bindPopup("<b>Vous êtes ici</b>")
                         .openPopup();
                    }
                },
                (error) => {
                    this.status = "Erreur : " + error.message;
                    console.warn(this.status);
                },
                options
            );
        }
    };

    // On lance la localisation automatiquement
    Appareil.localiser();
</script>