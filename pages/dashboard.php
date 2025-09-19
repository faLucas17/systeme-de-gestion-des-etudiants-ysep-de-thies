<?php
require_once '../config/database.php';
requireLogin();

$pageTitle = "Liste des Étudiants - ISEP-Thiès";

// Paramètres de pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Recherche
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Filtres additionnels
$departement_filter = isset($_GET['departement']) ? sanitize($_GET['departement']) : '';
$filiere_filter = isset($_GET['filiere']) ? sanitize($_GET['filiere']) : '';

// Construire la requête
$whereClause = "WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if (!empty($search)) {
    $whereClause .= " AND (nom LIKE ? OR prenom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($departement_filter)) {
    $whereClause .= " AND departement = ?";
    $params[] = $departement_filter;
}

if (!empty($filiere_filter)) {
    $whereClause .= " AND filiere = ?";
    $params[] = $filiere_filter;
}

// Compter le total
$countQuery = "SELECT COUNT(*) FROM students $whereClause";
$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalStudents = $stmt->fetchColumn();
$totalPages = ceil($totalStudents / $limit);

// Récupérer les étudiants - CORRECTION ICI
$query = "SELECT * FROM students $whereClause ORDER BY nom ASC, prenom ASC LIMIT $limit OFFSET $offset";
// OU en utilisant les paramètres PDO avec des entiers explicites


$stmt = $pdo->prepare($query);
$stmt->execute($params);


$students = $stmt->fetchAll();

// Récupérer les départements et filières uniques pour les filtres
$depts = $pdo->prepare("SELECT DISTINCT departement FROM students WHERE user_id = ? ORDER BY departement");
$depts->execute([$_SESSION['user_id']]);
$departements = $depts->fetchAll(PDO::FETCH_COLUMN);

$fils = $pdo->prepare("SELECT DISTINCT filiere FROM students WHERE user_id = ? ORDER BY filiere");
$fils->execute([$_SESSION['user_id']]);
$filieres = $fils->fetchAll(PDO::FETCH_COLUMN);
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
        <!-- En-tête et statistiques -->
        <div class="card">
            <h1 style="text-align: center; color: #8B4513; margin-bottom: 1rem;">
                📚 Gestion des Étudiants ISEP-Thiès
            </h1>
            <div style="text-align: center;">
                <p style="font-size: 1.2rem; color: #A0522D;">
                    Total : <strong style="color: #8B4513;"><?php echo $totalStudents; ?></strong> étudiant(s)
                    <?php if ($search || $departement_filter || $filiere_filter): ?>
                        | Résultats filtrés
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="card">
            <form method="GET" style="display: grid; gap: 1rem;">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
                    <!-- Recherche -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="search">🔍 Rechercher par nom ou prénom</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               class="form-control" 
                               placeholder="Tapez le nom ou prénom..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <!-- Filtre département -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="departement">🏛️ Département</label>
                        <select name="departement" id="departement" class="form-control">
                            <option value="">Tous les départements</option>
                            <?php foreach ($departements as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>" 
                                        <?php echo ($departement_filter === $dept) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Filtre filière -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="filiere">🎓 Filière</label>
                        <select name="filiere" id="filiere" class="form-control">
                            <option value="">Toutes les filières</option>
                            <?php foreach ($filieres as $fil): ?>
                                <option value="<?php echo htmlspecialchars($fil); ?>" 
                                        <?php echo ($filiere_filter === $fil) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($fil); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
                    <?php if ($search || $departement_filter || $filiere_filter): ?>
                        <a href="dashboard.php" class="btn btn-warning">🗑️ Effacer les filtres</a>
                    <?php endif; ?>
                    <a href="add_student.php" class="btn btn-success">➕ Ajouter un Étudiant</a>
                </div>
            </form>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message'], $_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques rapides -->
        <?php if ($totalStudents > 0): ?>
            <div class="stats-grid" style="margin-bottom: 1.5rem;">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_unique(array_column($students, 'departement'))); ?></div>
                    <div class="stat-label">🏛️ Départements</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count(array_unique(array_column($students, 'filiere'))); ?></div>
                    <div class="stat-label">🎓 Filières</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($students); ?></div>
                    <div class="stat-label">📄 Cette page</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Liste des étudiants -->
        <?php if (empty($students)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">😔</div>
                <h3 style="color: #8B4513; margin-bottom: 1rem;">Aucun étudiant trouvé</h3>
                <?php if ($search || $departement_filter || $filiere_filter): ?>
                    <p style="color: #A0522D; margin-bottom: 1.5rem;">
                        Aucun résultat pour les critères sélectionnés.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="dashboard.php" class="btn btn-primary">Voir tous les étudiants</a>
                        <a href="add_student.php" class="btn btn-success">Ajouter un étudiant</a>
                    </div>
                <?php else: ?>
                    <p style="color: #A0522D; margin-bottom: 1.5rem;">
                        Commencez par ajouter votre premier étudiant !
                    </p>
                    <a href="add_student.php" class="btn btn-success">Ajouter un étudiant</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>👤 Nom</th>
                                <th>👤 Prénom</th>
                                <th>📱 Téléphone</th>
                                <th>🏛️ Département</th>
                                <th>🎓 Filière</th>
                                <th>📅 Ajouté le</th>
                                <th>⚙️ Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                            <tr style="animation-delay: <?php echo $index * 0.1; ?>s;">
                                <td><strong><?php echo htmlspecialchars($student['nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($student['prenom']); ?></td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($student['telephone']); ?>" 
                                       style="color: #8B4513; text-decoration: none;">
                                        <?php echo htmlspecialchars($student['telephone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span style="background: rgba(139, 69, 19, 0.1); padding: 0.25rem 0.5rem; border-radius: 15px; font-size: 0.85rem; color: #8B4513;">
                                        <?php echo htmlspecialchars($student['departement']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="background: rgba(210, 180, 140, 0.3); padding: 0.25rem 0.5rem; border-radius: 15px; font-size: 0.85rem; color: #A0522D;">
                                        <?php echo htmlspecialchars($student['filiere']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($student['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" 
                                           class="btn btn-warning btn-sm"
                                           title="Modifier cet étudiant">
                                           ✏️ Modifier
                                        </a>
                                        <a href="../actions/student_action.php?action=delete&id=<?php echo $student['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           title="Supprimer cet étudiant"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer <?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?> ?')">
                                           🗑️ Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                $query_params = [];
                if ($search) $query_params['search'] = $search;
                if ($departement_filter) $query_params['departement'] = $departement_filter;
                if ($filiere_filter) $query_params['filiere'] = $filiere_filter;
                $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
                ?>
                
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1) . $query_string; ?>" 
                       class="btn btn-primary btn-sm">
                        ⬅️ Précédent
                    </a>
                <?php endif; ?>

                <?php
                // Afficher les numéros de pages (max 5)
                $start_page = max(1, $page - 2);
                $end_page = min($totalPages, $start_page + 4);
                
                if ($end_page - $start_page < 4) {
                    $start_page = max(1, $end_page - 4);
                }
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i . $query_string; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo ($page + 1) . $query_string; ?>" 
                       class="btn btn-primary btn-sm">
                        Suivant ➡️
                    </a>
                <?php endif; ?>
                
                <div style="margin-left: 1rem; color: #8B4513; font-weight: bold;">
                    Page <?php echo $page; ?> sur <?php echo $totalPages; ?>
                </div>
            </div>
        <?php endif; ?>
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
        // Recherche en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const departementSelect = document.getElementById('departement');
            const filiereSelect = document.getElementById('filiere');
            let searchTimeout;
            
            // Auto-submit sur changement des filtres
            [departementSelect, filiereSelect].forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
            
            // Recherche avec délai pour éviter trop de requêtes
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 2 || this.value.length === 0) {
                        this.form.submit();
                    }
                }, 500);
            });
            
            // Amélioration visuelle des lignes du tableau
            const tableRows = document.querySelectorAll('.table tbody tr');
            tableRows.forEach((row, index) => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                    this.style.zIndex = '1';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.zIndex = 'auto';
                });
            });
            
            // Confirmation personnalisée pour la suppression
            const deleteButtons = document.querySelectorAll('a[onclick*="confirm"]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const studentName = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    
                    if (confirm(`ATTENTION \n\nÊtes-vous vraiment sûr de vouloir supprimer définitivement l'étudiant :\n\n"${studentName}"\n\nCette action est irréversible !`)) {
                        window.location.href = this.href;
                    }
                });
            });
        });
    </script>
</body>
</html>