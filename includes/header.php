<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Gestion Étudiants ISEP-Thiès'; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Arial:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="../index.php" class="logo">🎓 ISEP-Thiès</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <nav class="nav-links">
                <a href="../index.php">Tableau de bord</a>
                <a href="dashboard.php">Étudiants</a>
                <a href="add_student.php">Ajouter Étudiant</a>
                <span style="color: #667eea; font-weight: bold;">
                    👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
            </nav>
            <?php else: ?>
            <nav class="nav-links">
                <a href="pages/login.php">Connexion</a>
                <a href="pages/register.php">Inscription</a>
            </nav>
            <?php endif; ?>
        </div>
    </header>

    <div class="container"><?php echo isset($pageTitle) ? $pageTitle : 'Gestion Étudiants ISEP-Thiès'; ?>