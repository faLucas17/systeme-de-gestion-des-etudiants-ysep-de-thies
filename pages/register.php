<?php
require_once '../config/database.php';

$pageTitle = "Inscription - ISEP-Thiès";
$error = '';
$success = '';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom']);
    $email = sanitize($_POST['email']);
    $telephone = sanitize($_POST['telephone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($nom) || empty($email) || empty($telephone) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Cet email est déjà utilisé.";
            } else {
                // Créer le compte
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (nom, email, telephone, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $email, $telephone, $hashedPassword]);
                
                $_SESSION['success_message'] = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                header('Location: login.php');
                exit();
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la création du compte.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎓 ISEP-Thiès</a>
            <nav class="nav-links">
                <a href="../index.php">Accueil</a>
                <a href="login.php">Connexion</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 600px; margin: 2rem auto;">
            <h1 style="text-align: center; color: #667eea; margin-bottom: 2rem;">
                📝 Inscription
            </h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nom">👤 Nom complet</label>
                    <input type="text" 
                           id="nom" 
                           name="nom" 
                           class="form-control" 
                           required 
                           value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>"
                           placeholder="Votre nom complet">
                </div>

                <div class="form-group">
                    <label for="email">📧 Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="votre.email@example.com">
                </div>

                <div class="form-group">
                    <label for="telephone">📱 Téléphone</label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone" 
                           class="form-control" 
                           required 
                           value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>"
                           placeholder="77 123 45 67">
                </div>

                <div class="form-group">
                    <label for="password">🔒 Mot de passe</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required
                           minlength="6"
                           placeholder="Au moins 6 caractères">
                </div>

                <div class="form-group">
                    <label for="confirm_password">🔒 Confirmer le mot de passe</label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           class="form-control" 
                           required
                           placeholder="Répétez le mot de passe">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; margin-bottom: 1rem;">
                    Créer mon compte
                </button>
            </form>

            <div style="text-align: center; padding-top: 1rem; border-top: 1px solid #eee;">
                <p>Déjà un compte ?</p>
                <a href="login.php" class="btn btn-primary">Se connecter</a>
            </div>
        </div>
    </div>

    <footer style="background: rgba(255,255,255,0.1); padding: 2rem; text-align: center; margin-top: 3rem; border-radius: 15px;">
        <div style="color: white; font-size: 1.1rem; margin-bottom: 1rem;">
            <strong>🎓 Institut Supérieur d'Enseignement Professionnel de Thiès</strong>
        </div>
        <div style="color: rgba(255,255,255,0.8);">
            <p>© 2025 ISEP-Thiès | Système de Gestion des Étudiants</p>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
        // Validation en temps réel des mots de passe
        document.getElementById('confirm_password').addEventListener('keyup', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.style.borderColor = '#ff416c';
            } else {
                this.style.borderColor = '#56ab2f';
            }
        });
    </script>
</body>
</html>