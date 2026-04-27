<div class="login-box">
    <h3>Connexion</h3>
    <?php if($login_error): ?>
    <p style="color:red"><?= $login_error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit" name="login_action" class="btn" style="width:100%">Se connecter</button>
    </form>

    <hr style="margin:20px 0">

    <h3>Créer un compte</h3>
    <form method="POST">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email_inscription" placeholder="Email" required>
        <input type="password" name="mdp_inscription" placeholder="Mot de passe" required>
        <button type="submit" name="register_action" class="btn btn-success" style="width:100%">Créer un compte</button>
    </form>
</div>