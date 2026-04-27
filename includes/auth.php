<?php
// --- LOGIQUE DE CONNEXION ---
$login_error = false;

// Authentification Admin simple
if (isset($_POST['login_action'])) {
    if ($_POST['password'] === '123admin') {
        $_SESSION['admin_auth'] = true;
    } else {
        $login_error = "Mot de passe incorrect.";
    }
}

// Authentification Utilisateur depuis la BDD
if (isset($_POST['login_action']) && isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['mdp'])) {
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            $_SESSION['admin_auth'] = true;
        }
        header("Location: index.php?page=carte");
        exit;
    } else {
        $login_error = "Email ou mot de passe incorrect";
    }
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php?page=carte");
    exit;
}

// --- INSCRIPTION UTILISATEUR ---
if (isset($_POST['register_action'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email_inscription']);
    $mdp = password_hash($_POST['mdp_inscription'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO Utilisateur (nom,email,mdp,role) VALUES (?,?,?,?)");
    $stmt->execute([$nom, $email, $mdp, 'user']);

    header("Location: index.php?page=connexion");
    exit;
}
?>