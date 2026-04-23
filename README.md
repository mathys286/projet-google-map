<b> Projet Google Maps :<b/>
/!\ Pour voir le site il faut avoir Wampserveur /!\

Il faut que les deux dossier soient sur le dossier "www" puis dans localhost creer un projet 
sous le nom de "Carte map" et pour finir coller le code SQL :

CREATE TABLE commentaires (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_entreprise INT NOT NULL,
    id_utilisateur INT NULL,
    nom_invite VARCHAR(255),
    contenu TEXT NOT NULL,
    note INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50)
);

Pour voir le site : https://http://localhost/index.php
