<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>MAP Pro - Explorer & Gérer</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <style>

        :root { --main: #2c3e50; --accent: #3498db; --bg: #f4f7f6; --danger: #e74c3c; --success: #2ecc71; --gold: #f1c40f; }

        body { font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; flex-direction: column; height: 100vh; background: var(--bg); overflow: hidden; }

        .nav { background: var(--main); color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); z-index: 1000; }

        .nav a { color: white; text-decoration: none; margin-left: 15px; font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.9em; }

        .nav a:hover { background: rgba(255,255,255,0.1); }

        .main-map { display: flex; flex: 1; overflow: hidden; position: relative; }

        .side-list { width: 350px; background: white; border-right: 1px solid #ddd; display: flex; flex-direction: column; z-index: 10; }

        #map { flex: 1; z-index: 1; }

        .ent-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; transition: 0.2s; }

        .ent-item:hover { background: #f9f9f9; border-left: 4px solid var(--accent); }

        .tag-metier { font-size: 0.75em; color: var(--accent); font-weight: bold; }

        #entreprisePanel { position: fixed; top: 0; right: -420px; width: 400px; height: 100%; background: white; box-shadow: -5px 0 20px rgba(0,0,0,0.2); transition: 0.3s; z-index: 2000; overflow-y: auto; }

        #entreprisePanel.active { right: 0; }

        .panel-content { padding: 30px; }

        .close-btn { float: right; cursor: pointer; font-size: 24px; color: #ccc; }

        .panel-top { display: flex; gap: 20px; align-items: flex-start; margin-bottom: 20px; }

        .panel-logo { width: 120px; height: 120px; object-fit: contain; border-radius: 8px; background: #f5f5f5; padding: 5px; border: 1px solid #eee; }

        .admin-wrap { display: flex; flex: 1; overflow: hidden; }

        .admin-menu { width: 200px; background: #34495e; color: white; padding-top: 20px; }

        .admin-menu a { display: block; color: #bdc3c7; padding: 12px 20px; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); }

        .admin-menu a.active { background: var(--accent); color: white; }

        .admin-body { flex: 1; padding: 30px; overflow-y: auto; }

        .form-card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

        input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }

        .btn { background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; text-align: center; }

        .btn-success { background: var(--success); }

        .btn-magic { background: var(--gold); color: #333; margin-bottom: 15px; font-size: 0.85em; }

        .optional-hidden { display: none !important; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin-top: 10px; }

        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9em; }

        th { background: #f8f9fa; color: #7f8c8d; text-transform: uppercase; font-size: 0.8em; }

        .h-row { display: grid; grid-template-columns: 100px 1fr 1fr; gap: 10px; align-items: center; padding: 5px 0; }

        .login-box { width: 350px; margin: 100px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; }

        @media (max-width: 768px) { .panel-top { flex-direction: column; align-items: center; } }

    </style>

</head>

<body>



<div class="nav">

    <div style="font-size:1.4em"><b>MAP</b> Pro</div>

    <div>

        <a href="index.php?page=carte">Carte</a>

        <a href="index.php?page=admin">Administration</a>

        <?php if(isset($_SESSION['id_utilisateur'])): ?>

            <span style="margin-left:10px;">👤 <?= htmlspecialchars($_SESSION['nom']) ?></span>

            <a href="index.php?logout=1" style="color:red;">Déconnexion</a>

        <?php else: ?>

            <a href="index.php?page=connexion">Se connecter</a>

        <?php endif; ?>

    </div>

</div>