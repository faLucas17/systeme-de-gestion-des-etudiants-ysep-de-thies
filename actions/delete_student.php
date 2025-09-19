<?php
require_once '../config/database.php';
requireLogin();

// Vérifier l'ID de l'étudiant
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID d'étudiant manquant.";
    $_SESSION['message_type'] = "danger";
    header('Location: ../pages/dashboard.php');
    exit();
}

$studentId = (int)$_GET['id'];

try {
    // Vérifier que l'étudiant appartient à l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT nom, prenom FROM students WHERE id = ? AND user_id = ?");
    $stmt->execute([$studentId, $_SESSION['user_id']]);
    $student = $stmt->fetch();
    
    if (!$student) {
        $_SESSION['message'] = "Étudiant non trouvé ou vous n'avez pas l'autorisation.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Supprimer l'étudiant
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ? AND user_id = ?");
        $stmt->execute([$studentId, $_SESSION['user_id']]);
        
        $_SESSION['message'] = "L'étudiant " . htmlspecialchars($student['nom']) . " " . htmlspecialchars($student['prenom']) . " a été supprimé avec succès.";
        $_SESSION['message_type'] = "success";
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur lors de la suppression de l'étudiant.";
    $_SESSION['message_type'] = "danger";
}

header('Location: ../pages/dashboard.php');
exit();
?>