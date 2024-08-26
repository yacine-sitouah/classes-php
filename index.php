<?php
require 'User.php';

// Démarrer la session pour garder les informations de connexion
session_start();

// Connexion à la base de données
$user = new User('localhost', 'root', '', 'classes');

// Vérifier si un formulaire a été soumis
$message = '';

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'register':
            $userInfo = $user->register($_POST['login'], $_POST['password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
            if ($userInfo) {
                $message = "Utilisateur enregistré avec succès.";
            } else {
                $message = "Erreur lors de l'enregistrement.";
            }
            break;

        case 'login':
            if ($user->connect($_POST['login'], $_POST['password'])) {
                $_SESSION['user'] = $user->getAllInfos();
                $message = "Connexion réussie.";
            } else {
                $message = "Login ou mot de passe incorrect.";
            }
            break;

        case 'logout':
            $user->disconnect();
            unset($_SESSION['user']);
            $message = "Déconnexion réussie.";
            break;

        case 'update':
            if (isset($_SESSION['user'])) {
                $user->connect($_SESSION['user']['login'], $_POST['current_password']); // Reconnecter l'utilisateur pour vérifier l'identité
                $user->update($_POST['login'], $_POST['new_password'], $_POST['email'], $_POST['firstname'], $_POST['lastname']);
                $_SESSION['user'] = $user->getAllInfos();
                $message = "Informations mises à jour.";
            } else {
                $message = "Vous devez être connecté pour mettre à jour vos informations.";
            }
            break;

        case 'delete':
            if (isset($_SESSION['user'])) {
                $user->delete();
                unset($_SESSION['user']);
                $message = "Compte supprimé avec succès.";
            } else {
                $message = "Vous devez être connecté pour supprimer votre compte.";
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateur</title>
</head>
<body>

<h1>Gestion Utilisateur</h1>
<p><?php echo $message; ?></p>

<?php if (!isset($_SESSION['user'])): ?>
    <h2>Inscription</h2>
    <form method="post">
        <input type="hidden" name="action" value="register">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Prénom: <input type="text" name="firstname" required></label><br>
        <label>Nom: <input type="text" name="lastname" required></label><br>
        <button type="submit">S'inscrire</button>
    </form>

    <h2>Connexion</h2>
    <form method="post">
        <input type="hidden" name="action" value="login">
        <label>Login: <input type="text" name="login" required></label><br>
        <label>Mot de passe: <input type="password" name="password" required></label><br>
        <button type="submit">Se connecter</button>
    </form>

<?php else: ?>
    <h2>Bienvenue, <?php echo $_SESSION['user']['firstname']; ?> !</h2>

    <h3>Mettre à jour les informations</h3>
    <form method="post">
        <input type="hidden" name="action" value="update">
        <label>Nouveau login: <input type="text" name="login" value="<?php echo $_SESSION['user']['login']; ?>" required></label><br>
        <label>Nouveau mot de passe: <input type="password" name="new_password"></label><br>
        <label>Nouvel email: <input type="email" name="email" value="<?php echo $_SESSION['user']['email']; ?>" required></label><br>
        <label>Nouveau prénom: <input type="text" name="firstname" value="<?php echo $_SESSION['user']['firstname']; ?>" required></label><br>
        <label>Nouveau nom: <input type="text" name="lastname" value="<?php echo $_SESSION['user']['lastname']; ?>" required></label><br>
        <label>Mot de passe actuel: <input type="password" name="current_password" required></label><br>
        <button type="submit">Mettre à jour</button>
    </form>

    <h3>Supprimer le compte</h3>
    <form method="post">
        <input type="hidden" name="action" value="delete">
        <button type="submit">Supprimer mon compte</button>
    </form>

    <h3>Déconnexion</h3>
    <form method="post">
        <input type="hidden" name="action" value="logout">
        <button type="submit">Se déconnecter</button>
    </form>

<?php endif; ?>

</body>
</html>
