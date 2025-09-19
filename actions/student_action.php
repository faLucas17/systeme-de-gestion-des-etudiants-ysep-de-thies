<?php
require_once '../config/database.php';
requireLogin();

// Listes des départements et filières valides
$departements_valides = [
    'Informatique',
    'Télécommunications',
    'Électronique',
    'Génie Civil',
    'Mécanique',
    'Électrotechnique'
];

$filieres_valides = [
    'Génie Logiciel',
    'Cybersécurité',
    'Intelligence Artificielle',
    'Réseaux et Télécoms',
    'Systèmes Embarqués',
    'Développement Web',
    'Administration Systèmes',
    'Base de Données'
];

// Fonction de validation des données étudiant
function validateStudentData($nom, $prenom, $telephone, $departement, $filiere, $departements_valides, $filieres_valides) {
    $errors = [];
    
    if (empty($nom) || empty($prenom) || empty($telephone) || empty($departement) || empty($filiere)) {
        $errors[] = "Veuillez remplir tous les champs.";
    }
    
    if (strlen($nom) < 2) {
        $errors[] = "Le nom doit contenir au moins 2 caractères.";
    }
    
    if (strlen($prenom) < 2) {
        $errors[] = "Le prénom doit contenir au moins 2 caractères.";
    }
    
    if (strlen($telephone) < 9) {
        $errors[] = "Le numéro de téléphone doit contenir au moins 9 chiffres.";
    }
    
    if (!in_array($departement, $departements_valides)) {
        $errors[] = "Département non valide.";
    }
    
    if (!in_array($filiere, $filieres_valides)) {
        $errors[] = "Filière non valide.";
    }
    
    return $errors;
}

// Traitement de l'ajout d'étudiant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_student') {
    $nom = sanitize($_POST['nom']);
    $prenom = sanitize($_POST['prenom']);
    $telephone = sanitize($_POST['telephone']);
    $departement = sanitize($_POST['departement']);
    $filiere = sanitize($_POST['filiere']);
    
    $errors = validateStudentData($nom, $prenom, $telephone, $departement, $filiere, $departements_valides, $filieres_valides);
    
    if (empty($errors)) {
        try {
            // Vérifier si l'étudiant existe déjà
            $stmt = $pdo->prepare("SELECT id FROM students WHERE nom = ? AND prenom = ? AND user_id = ?");
            $stmt->execute([$nom, $prenom, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                $errors[] = "Cet étudiant existe déjà dans votre liste.";
            } else {
                // Ajouter l'étudiant
                $stmt = $pdo->prepare("
                    INSERT INTO students (nom, prenom, telephone, departement, filiere, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$nom, $prenom, $telephone, $departement, $filiere, $_SESSION['user_id']]);
                
                $_SESSION['message'] = "Étudiant " . htmlspecialchars($nom) . " " . htmlspecialchars($prenom) . " ajouté avec succès !";
                $_SESSION['message_type'] = "success";
                
                header('Location: ../pages/dashboard.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout de l'étudiant.";
        }
    }
    
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: ../pages/add_student.php');
    exit();
}

// Traitement de la modification d'étudiant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_student') {
    $student_id = (int)$_POST['student_id'];
    $nom = sanitize($_POST['nom']);
    $prenom = sanitize($_POST['prenom']);
    $telephone = sanitize($_POST['telephone']);
    $departement = sanitize($_POST['departement']);
    $filiere = sanitize($_POST['filiere']);
    
    $errors = validateStudentData($nom, $prenom, $telephone, $departement, $filiere, $departements_valides, $filieres_valides);
    
    if (empty($errors)) {
        try {
            // Vérifier que l'étudiant appartient à l'utilisateur
            $stmt = $pdo->prepare("SELECT id FROM students WHERE id = ? AND user_id = ?");
            $stmt->execute([$student_id, $_SESSION['user_id']]);
            
            if (!$stmt->fetch()) {
                $errors[] = "Étudiant non trouvé ou vous n'avez pas l'autorisation.";
            } else {
                // Vérifier si un autre étudiant a le même nom/prénom
                $stmt = $pdo->prepare("SELECT id FROM students WHERE nom = ? AND prenom = ? AND id != ? AND user_id = ?");
                $stmt->execute([$nom, $prenom, $student_id, $_SESSION['user_id']]);
                
                if ($stmt->fetch()) {
                    $errors[] = "Un autre étudiant avec ce nom et prénom existe déjà.";
                } else {
                    // Modifier l'étudiant
                    $stmt = $pdo->prepare("
                        UPDATE students 
                        SET nom = ?, prenom = ?, telephone = ?, departement = ?, filiere = ?
                        WHERE id = ? AND user_id = ?
                    ");
                    $stmt->execute([$nom, $prenom, $telephone, $departement, $filiere, $student_id, $_SESSION['user_id']]);
                    
                    $_SESSION['message'] = "Étudiant " . htmlspecialchars($nom) . " " . htmlspecialchars($prenom) . " modifié avec succès !";
                    $_SESSION['message_type'] = "success";
                    
                    header('Location: ../pages/dashboard.php');
                    exit();
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la modification de l'étudiant.";
        }
    }
    
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: ../pages/edit_student.php?id=' . $student_id);
    exit();
}

// Traitement de la suppression d'étudiant
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $student_id = (int)$_GET['id'];
    
    try {
        // Vérifier que l'étudiant appartient à l'utilisateur connecté
        $stmt = $pdo->prepare("SELECT nom, prenom FROM students WHERE id = ? AND user_id = ?");
        $stmt->execute([$student_id, $_SESSION['user_id']]);
        $student = $stmt->fetch();
        
        if (!$student) {
            $_SESSION['message'] = "Étudiant non trouvé ou vous n'avez pas l'autorisation.";
            $_SESSION['message_type'] = "danger";
        } else {
            // Supprimer l'étudiant
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ? AND user_id = ?");
            $stmt->execute([$student_id, $_SESSION['user_id']]);
            
            $_SESSION['message'] = "L'étudiant " . htmlspecialchars($student['nom']) . " " . htmlspecialchars($student['prenom']) . " a été supprimé avec succès.";
            $_SESSION['message_type'] = "success";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de la suppression de l'étudiant.";
        $_SESSION['message_type'] = "danger";
    }
    
    header('Location: ../pages/dashboard.php');
    exit();
}

// Redirection par défaut
header('Location: ../pages/dashboard.php');
exit();
?>