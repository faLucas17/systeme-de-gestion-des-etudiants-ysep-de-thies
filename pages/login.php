<?php
require_once '../config/database.php';

$pageTitle = "Connexion - ISEP-Thiès";

// Rediriger si déjà connecté
if (isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

// Récupérer les messages et données depuis la session
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Nettoyer la session
unset($_SESSION['error'], $_SESSION['success_message'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎓 ISEP-Thiès</a>
            <nav class="nav-links">
                <a href="../index.php">Accueil</a>
                <a href="register.php">Inscription</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 500px; margin: 2rem auto;">
            <h1 style="text-align: center; color: #8B4513; margin-bottom: 2rem;">
                🔐 Connexion
            </h1>
            
            <div style="text-align: center; margin-bottom: 2rem;">
                <p style="color: #A0522D; font-size: 1.1rem;">
                    Accédez à votre espace de gestion des étudiants
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="../actions/auth_action.php" id="loginForm">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="email">📧 Email *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           required 
                           value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>"
                           placeholder="votre.email@example.com"
                           autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">🔒 Mot de passe *</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required
                           placeholder="Votre mot de passe"
                           autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Se connecter
                </button>
            </form>

            <div style="text-align: center; padding-top: 1rem; border-top: 1px solid #D2B48C;">
                <p style="color: #8B4513;">Pas encore de compte ?</p>
                <a href="register.php" class="btn btn-success">Créer un compte</a>
            </div>

            

    <footer style="background: rgba(139, 69, 19, 0.9); padding: 2rem; text-align: center; margin-top: 3rem; border-radius: 15px;">
        <div style="color: white; font-size: 1.1rem; margin-bottom: 1rem;">
            <strong>🎓 Institut Supérieur d'Enseignement Professionnel de Thiès</strong>
        </div>
        <div style="color: rgba(255,255,255,0.8);">
            <p>© 2025 ISEP-Thiès | Système de Gestion des Étudiants</p>
            <p>Formation d'excellence pour l'avenir du Sénégal</p>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
        // Fonction pour remplir automatiquement le compte de test
        function fillTestAccount() {
            document.getElementById('email').value = 'admin@isep.edu';
            document.getElementById('password').value = 'admin123';
            
            // Animation visuelle
            const form = document.getElementById('loginForm');
            form.style.transform = 'scale(1.02)';
            form.style.transition = 'all 0.2s ease';
            
            setTimeout(() => {
                form.style.transform = 'scale(1)';
            }, 200);
        }

        // Validation du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            
            // Validation en temps réel
            emailField.addEventListener('blur', function() {
                validateEmail(this);
            });
            
            passwordField.addEventListener('blur', function() {
                validatePassword(this);
            });
            
            // Validation avant soumission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                if (!validateEmail(emailField)) {
                    isValid = false;
                }
                
                if (!validatePassword(passwordField)) {
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showMessage('Veuillez corriger les erreurs dans le formulaire.', 'error');
                }
            });
            
            function validateEmail(field) {
                const email = field.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (!email) {
                    showFieldError(field, 'L\'email est requis');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    showFieldError(field, 'Format d\'email invalide');
                    return false;
                }
                
                clearFieldError(field);
                return true;
            }
            
            function validatePassword(field) {
                const password = field.value;
                
                if (!password) {
                    showFieldError(field, 'Le mot de passe est requis');
                    return false;
                }
                
                if (password.length < 6) {
                    showFieldError(field, 'Le mot de passe doit contenir au moins 6 caractères');
                    return false;
                }
                
                clearFieldError(field);
                return true;
            }
            
            function showFieldError(field, message) {
                field.style.borderColor = '#DC143C';
                
                let errorDiv = field.parentNode.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error';
                    errorDiv.style.color = '#DC143C';
                    errorDiv.style.fontSize = '0.85rem';
                    errorDiv.style.marginTop = '0.25rem';
                    field.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
            }
            
            function clearFieldError(field) {
                field.style.borderColor = '';
                
                const errorDiv = field.parentNode.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
            
            function showMessage(message, type) {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'}`;
                alertDiv.textContent = message;
                
                const form = document.getElementById('loginForm');
                form.parentNode.insertBefore(alertDiv, form);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        });
    </script>
</body>
</html>