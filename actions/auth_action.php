<?php
require_once '../config/database.php';

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $nom = sanitize($_POST['nom']);
    $email = sanitize($_POST['email']);
    $telephone = sanitize($_POST['telephone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validation
    if (empty($nom) || empty($email) || empty($telephone) || empty($password)) {
        $errors[] = "Veuillez remplir tous les champs.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    
    if (empty($errors)) {
        try {
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé.";
            } else {
                // Créer le compte
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (nom, email, telephone, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nom, $email, $telephone, $hashedPassword]);
                
                $_SESSION['success_message'] = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                header('Location: ../pages/login.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la création du compte.";
        }
    }
    
    // Retourner les erreurs
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: ../pages/register.php');
    exit();
}

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        $_SESSION['form_data'] = $_POST;
        header('Location: ../pages/login.php');
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, nom, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            
            $_SESSION['success_message'] = "Connexion réussie ! Bienvenue " . $user['nom'];
            header('Location: ../index.php');
            exit();
        } else {
            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ../pages/login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la connexion.";
        $_SESSION['form_data'] = $_POST;
        header('Location: ../pages/login.php');
        exit();
    }
}

// Si aucune action valide n'est trouvée
header('Location: ../pages/login.php');
exit();
?>