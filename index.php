<?php
require_once 'config/database.php';

$pageTitle = "Accueil - Gestion Étudiants ISEP-Thiès";

// Statistiques si l'utilisateur est connecté
$totalStudents = 0;
$totalUsers = 0;
$recentStudents = [];

if (isLoggedIn()) {
    // Compter les étudiants de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $totalStudents = $stmt->fetchColumn();
    
    // Compter tous les utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();
    
    // Récupérer les étudiants récents
    $stmt = $pdo->prepare("
        SELECT nom, prenom, filiere, created_at 
        FROM students 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recentStudents = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="index.php" class="logo">🎓 ISEP-Thiès</a>
            
            <?php if (isLoggedIn()): ?>
            <nav class="nav-links">
                <a href="index.php">Accueil</a>
                <a href="pages/dashboard.php">Étudiants</a>
                <a href="pages/add_student.php">Ajouter Étudiant</a>
                <span style="color: #667eea; font-weight: bold;">
                    👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a href="pages/logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
            </nav>
            <?php else: ?>
            <nav class="nav-links">
                <a href="pages/login.php">Connexion</a>
                <a href="pages/register.php">Inscription</a>
            </nav>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <?php if (isLoggedIn()): ?>
            <!-- Dashboard pour utilisateur connecté -->
            <div class="card">
                <h1 style="text-align: center; color: #667eea; margin-bottom: 2rem;">
                    🎯 Tableau de Bord
                </h1>
                <p style="text-align: center; font-size: 1.2rem; margin-bottom: 3rem;">
                    Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
                </p>
            </div>

            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalStudents; ?></div>
                    <div class="stat-label">👥 Mes Étudiants</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalUsers; ?></div>
                    <div class="stat-label">🌐 Utilisateurs Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($recentStudents); ?></div>
                    <div class="stat-label">📅 Ajouts Récents</div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <h2 style="color: #667eea; margin-bottom: 1.5rem;">⚡ Actions Rapides</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="pages/add_student.php" class="btn btn-success">➕ Ajouter Étudiant</a>
                    <a href="pages/dashboard.php" class="btn btn-primary">📋 Voir Tous les Étudiants</a>
                </div>
            </div>

            <!-- Étudiants récents -->
            <?php if (!empty($recentStudents)): ?>
            <div class="card">
                <h2 style="color: #667eea; margin-bottom: 1.5rem;">📚 Étudiants Récents</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Filière</th>
                                <th>Date d'ajout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentStudents as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['nom']); ?></td>
                                <td><?php echo htmlspecialchars($student['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($student['filiere']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($student['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Page d'accueil pour visiteur -->
            <div class="card" style="text-align: center;">
                <h1 style="color: #667eea; font-size: 3rem; margin-bottom: 1rem;">
                    🎓 ISEP-Thiès
                </h1>
                <h2 style="color: #764ba2; margin-bottom: 2rem;">
                    Système de Gestion des Étudiants
                </h2>
                <p style="font-size: 1.2rem; margin-bottom: 3rem; color: #666;">
                    Bienvenue dans l'application de gestion des étudiants de l'Institut Supérieur 
                    d'Enseignement Professionnel de Thiès. Gérez facilement vos étudiants avec notre 
                    interface moderne et intuitive.
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="pages/login.php" class="btn btn-primary">🔐 Se Connecter</a>
                    <a href="pages/register.php" class="btn btn-success">📝 S'Inscrire</a>
                </div>
            </div>

            <!-- Fonctionnalités -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                    <div class="stat-label">Gestion des Étudiants</div>
                    <p style="margin-top: 1rem; color: #666;">Ajoutez, modifiez et supprimez les informations des étudiants</p>
                </div>
                <div class="stat-card">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                    <div class="stat-label">Recherche Avancée</div>
                    <p style="margin-top: 1rem; color: #666;">Trouvez rapidement un étudiant par nom ou prénom</p>
                </div>
                <div class="stat-card">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔐</div>
                    <div class="stat-label">Sécurité</div>
                    <p style="margin-top: 1rem; color: #666;">Accès sécurisé avec authentification utilisateur</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer style="background: rgba(255,255,255,0.1); padding: 2rem; text-align: center; margin-top: 3rem; border-radius: 15px;">
        <div style="color: white; font-size: 1.1rem; margin-bottom: 1rem;">
            <strong>🎓 Institut Supérieur d'Enseignement Professionnel de Thiès</strong>
        </div>
        <div style="color: rgba(255,255,255,0.8);">
            <p>© 2025 ISEP-Thiès | Système de Gestion des Étudiants</p>
            <p>Développé pour l'excellence académique</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>