<?php
require_once '../config/database.php';
requireLogin();

$pageTitle = "Ajouter un Étudiant - ISEP-Thiès";

// Listes des départements et filières
$departements = [
    'Informatique',
    'Télécommunications',
    'Électronique',
    'Génie Civil',
    'Mécanique',
    'Électrotechnique'
];

$filieres = [
    'Génie Logiciel',
    'Cybersécurité',
    'Intelligence Artificielle',
    'Réseaux et Télécoms',
    'Systèmes Embarqués',
    'Développement Web',
    'Administration Systèmes',
    'Base de Données'
];

// Récupérer les erreurs et données du formulaire depuis la session
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['errors'], $_SESSION['form_data']);
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
                <a href="dashboard.php">Étudiants</a>
                <a href="add_student.php">Ajouter Étudiant</a>
                <span style="color: #8B4513; font-weight: bold;">
                    👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 800px; margin: 2rem auto;">
            <h1 style="text-align: center; color: #8B4513; margin-bottom: 2rem;">
                ➕ Ajouter un Étudiant
            </h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="../actions/student_action.php">
                <input type="hidden" name="action" value="add_student">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nom">👤 Nom *</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               class="form-control" 
                               required 
                               value="<?php echo isset($form_data['nom']) ? htmlspecialchars($form_data['nom']) : ''; ?>"
                               placeholder="Nom de famille"
                               minlength="2"
                               maxlength="50">
                    </div>

                    <div class="form-group">
                        <label for="prenom">👤 Prénom *</label>
                        <input type="text" 
                               id="prenom" 
                               name="prenom" 
                               class="form-control" 
                               required 
                               value="<?php echo isset($form_data['prenom']) ? htmlspecialchars($form_data['prenom']) : ''; ?>"
                               placeholder="Prénom"
                               minlength="2"
                               maxlength="50">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone">📱 Téléphone *</label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone" 
                           class="form-control" 
                           required 
                           value="<?php echo isset($form_data['telephone']) ? htmlspecialchars($form_data['telephone']) : ''; ?>"
                           placeholder="77 123 45 67"
                           pattern="[0-9]{9,15}">
                    <small style="color: #A0522D;">Format: 9 à 15 chiffres</small>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="departement">🏛️ Département *</label>
                        <select id="departement" name="departement" class="form-control" required>
                            <option value="">Choisir un département</option>
                            <?php foreach ($departements as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>" 
                                        <?php echo (isset($form_data['departement']) && $form_data['departement'] === $dept) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filiere">🎓 Filière *</label>
                        <select id="filiere" name="filiere" class="form-control" required>
                            <option value="">Choisir une filière</option>
                            <?php foreach ($filieres as $fil): ?>
                                <option value="<?php echo htmlspecialchars($fil); ?>" 
                                        <?php echo (isset($form_data['filiere']) && $form_data['filiere'] === $fil) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($fil); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="background: rgba(139, 69, 19, 0.1); padding: 1rem; border-radius: 10px; margin: 1rem 0; border: 2px solid rgba(139, 69, 19, 0.2);">
                    <p style="margin: 0; color: #8B4513;">
                        <strong>📌 Information :</strong><br>
                        Tous les champs marqués d'un astérisque (*) sont obligatoires.
                    </p>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-success">💾 Ajouter l'Étudiant</button>
                    <a href="dashboard.php" class="btn btn-warning">❌ Annuler</a>
                </div>
            </form>
        </div>
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
        // Validation en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required], select[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('error')) {
                        validateField(this);
                    }
                });
            });
            
            function validateField(field) {
                const value = field.value.trim();
                
                if (field.hasAttribute('required') && !value) {
                    showFieldError(field, 'Ce champ est obligatoire');
                    return false;
                }
                
                if (field.name === 'nom' || field.name === 'prenom') {
                    if (value.length < 2) {
                        showFieldError(field, 'Minimum 2 caractères requis');
                        return false;
                    }
                }
                
                if (field.name === 'telephone') {
                    const phoneRegex = /^[0-9]{9,15}$/;
                    if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                        showFieldError(field, 'Format de téléphone invalide (9-15 chiffres)');
                        return false;
                    }
                }
                
                clearFieldError(field);
                return true;
            }
            
            function showFieldError(field, message) {
                field.classList.add('error');
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
                field.classList.remove('error');
                field.style.borderColor = '';
                
                const errorDiv = field.parentNode.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
            
            // Validation du formulaire avant soumission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Veuillez corriger les erreurs dans le formulaire.');
                }
            });
        });
    </script>
</body>
</html>