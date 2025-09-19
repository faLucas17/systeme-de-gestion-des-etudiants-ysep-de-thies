<?php
require_once '../config/database.php';
requireLogin();

$pageTitle = "Modifier un Étudiant - ISEP-Thiès";
$error = '';
$student = null;

// Vérifier l'ID de l'étudiant
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID d'étudiant manquant.";
    $_SESSION['message_type'] = "danger";
    header('Location: dashboard.php');
    exit();
}

$studentId = (int)$_GET['id'];

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

// Récupérer les informations de l'étudiant
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? AND user_id = ?");
    $stmt->execute([$studentId, $_SESSION['user_id']]);
    $student = $stmt->fetch();
    
    if (!$student) {
        $_SESSION['message'] = "Étudiant non trouvé ou vous n'avez pas l'autorisation.";
        $_SESSION['message_type'] = "danger";
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur lors de la récupération des données.";
    $_SESSION['message_type'] = "danger";
    header('Location: dashboard.php');
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom']);
    $prenom = sanitize($_POST['prenom']);
    $telephone = sanitize($_POST['telephone']);
    $departement = sanitize($_POST['departement']);
    $filiere = sanitize($_POST['filiere']);
    
    // Validation
    if (empty($nom) || empty($prenom) || empty($telephone) || empty($departement) || empty($filiere)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif (strlen($telephone) < 9) {
        $error = "Le numéro de téléphone doit contenir au moins 9 chiffres.";
    } else {
        try {
            // Vérifier si un autre étudiant a le même nom/prénom
            $stmt = $pdo->prepare("SELECT id FROM students WHERE nom = ? AND prenom = ? AND id != ? AND user_id = ?");
            $stmt->execute([$nom, $prenom, $studentId, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                $error = "Un autre étudiant avec ce nom et prénom existe déjà.";
            } else {
                // Modifier l'étudiant
                $stmt = $pdo->prepare("
                    UPDATE students 
                    SET nom = ?, prenom = ?, telephone = ?, departement = ?, filiere = ?
                    WHERE id = ? AND user_id = ?
                ");
                $stmt->execute([$nom, $prenom, $telephone, $departement, $filiere, $studentId, $_SESSION['user_id']]);
                
                $_SESSION['message'] = "Étudiant modifié avec succès !";
                $_SESSION['message_type'] = "success";
                
                header('Location: dashboard.php');
                exit();
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification de l'étudiant.";
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
                <a href="dashboard.php">Étudiants</a>
                <a href="add_student.php">Ajouter Étudiant</a>
                <span style="color: #667eea; font-weight: bold;">
                    👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 800px; margin: 2rem auto;">
            <h1 style="text-align: center; color: #667eea; margin-bottom: 2rem;">
                ✏️ Modifier l'Étudiant
            </h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nom">👤 Nom</label>
                        <input type="text" 
                               id="nom" 
                               name="nom" 
                               class="form-control" 
                               required 
                               value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($student['nom']); ?>"
                               placeholder="Nom de famille">
                    </div>

                    <div class="form-group">
                        <label for="prenom">👤 Prénom</label>
                        <input type="text" 
                               id="prenom" 
                               name="prenom" 
                               class="form-control" 
                               required 
                               value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : htmlspecialchars($student['prenom']); ?>"
                               placeholder="Prénom">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone">📱 Téléphone</label>
                    <input type="tel" 
                           id="telephone" 
                           name="telephone" 
                           class="form-control" 
                           required 
                           value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : htmlspecialchars($student['telephone']); ?>"
                           placeholder="77 123 45 67">
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="departement">🏛️ Département</label>
                        <select id="departement" name="departement" class="form-control" required>
                            <option value="">Choisir un département</option>
                            <?php foreach ($departements as $dept): 
                                $selected = (isset($_POST['departement']) && $_POST['departement'] === $dept) || 
                                          (!isset($_POST['departement']) && $student['departement'] === $dept);
                            ?>
                                <option value="<?php echo $dept; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                    <?php echo $dept; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filiere">🎓 Filière</label>
                        <select id="filiere" name="filiere" class="form-control" required>
                            <option value="">Choisir une filière</option>
                            <?php foreach ($filieres as $fil): 
                                $selected = (isset($_POST['filiere']) && $_POST['filiere'] === $fil) || 
                                          (!isset($_POST['filiere']) && $student['filiere'] === $fil);
                            ?>
                                <option value="<?php echo $fil; ?>" <?php echo $selected ? 'selected' : ''; ?>>
                                    <?php echo $fil; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="background: rgba(102, 126, 234, 0.1); padding: 1rem; border-radius: 10px; margin: 1rem 0;">
                    <p style="margin: 0; color: #667eea;">
                        <strong>📅 Informations:</strong><br>
                        Ajouté le: <?php echo date('d/m/Y à H:i', strtotime($student['created_at'])); ?>
                    </p>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-warning">💾 Modifier l'Étudiant</button>
                    <a href="dashboard.php" class="btn btn-primary">⬅️ Retour à la liste</a>
                </div>
            </form>
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
</body>
</html>