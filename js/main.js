// JavaScript pour le système de gestion des étudiants ISEP-Thiès
// Projet ASRI P13 - 2025

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎓 Système ISEP-Thiès initialisé');
    
    // Initialiser toutes les fonctionnalités
    initializeAnimations();
    initializeFormValidation();
    initializeTableFeatures();
    initializeAlerts();
    initializeSearchFeatures();
    initializeAccessibility();
});

// === ANIMATIONS ===
function initializeAnimations() {
    // Animation des cartes au chargement
    const cards = document.querySelectorAll('.card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Animation des éléments de navigation
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 200 + (index * 100));
    });

    // Animation hover améliorée pour les boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// === VALIDATION DES FORMULAIRES ===
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        // Validation en temps réel
        inputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('error')) {
                    validateField(input);
                }
            });
        });
        
        // Validation avant soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
                
                // Focus sur le premier champ avec erreur
                const firstError = form.querySelector('.error');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    const fieldName = field.name;
    
    // Nettoyer les erreurs précédentes
    clearFieldError(field);
    
    // Validation des champs requis
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'Ce champ est obligatoire');
        return false;
    }
    
    // Validation spécifique par type
    switch (fieldType) {
        case 'email':
            if (value && !isValidEmail(value)) {
                showFieldError(field, 'Format d\'email invalide');
                return false;
            }
            break;
            
        case 'tel':
            if (value && !isValidPhone(value)) {
                showFieldError(field, 'Numéro de téléphone invalide (9-15 chiffres)');
                return false;
            }
            break;
            
        case 'password':
            if (value && value.length < 6) {
                showFieldError(field, 'Le mot de passe doit contenir au moins 6 caractères');
                return false;
            }
            
            // Vérifier la confirmation de mot de passe
            const confirmField = document.querySelector('input[name="confirm_password"]');
            if (confirmField && fieldName === 'password') {
                validatePasswordConfirmation(field, confirmField);
            } else if (fieldName === 'confirm_password') {
                const passwordField = document.querySelector('input[name="password"]');
                validatePasswordConfirmation(passwordField, field);
            }
            break;
    }
    
    // Validation par nom de champ
    switch (fieldName) {
        case 'nom':
        case 'prenom':
            if (value && value.length < 2) {
                showFieldError(field, 'Minimum 2 caractères requis');
                return false;
            }
            break;
    }
    
    return true;
}

function validatePasswordConfirmation(passwordField, confirmField) {
    if (passwordField && confirmField) {
        const password = passwordField.value;
        const confirm = confirmField.value;
        
        if (confirm && password !== confirm) {
            showFieldError(confirmField, 'Les mots de passe ne correspondent pas');
            return false;
        } else if (confirm && password === confirm) {
            clearFieldError(confirmField);
        }
    }
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[0-9\s\-\+\(\)]{9,15}$/;
    const digitsOnly = phone.replace(/\D/g, '');
    return phoneRegex.test(phone) && digitsOnly.length >= 9 && digitsOnly.length <= 15;
}

function showFieldError(field, message) {
    field.classList.add('error');
    field.style.borderColor = '#DC143C';
    
    // Créer ou mettre à jour le message d'erreur
    let errorElement = field.parentNode.querySelector('.field-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.style.cssText = `
            color: #DC143C;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            font-weight: 500;
        `;
        field.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
    
    // Animation d'apparition
    errorElement.style.opacity = '0';
    errorElement.style.transform = 'translateY(-5px)';
    setTimeout(() => {
        errorElement.style.transition = 'all 0.3s ease';
        errorElement.style.opacity = '1';
        errorElement.style.transform = 'translateY(0)';
    }, 10);
}

function clearFieldError(field) {
    field.classList.remove('error');
    field.style.borderColor = '';
    
    const errorElement = field.parentNode.querySelector('.field-error');
    if (errorElement) {
        errorElement.style.transition = 'all 0.3s ease';
        errorElement.style.opacity = '0';
        errorElement.style.transform = 'translateY(-5px)';
        setTimeout(() => errorElement.remove(), 300);
    }
}

// === FONCTIONNALITÉS DU TABLEAU ===
function initializeTableFeatures() {
    const tableRows = document.querySelectorAll('.table tbody tr');
    
    // Animation et interactions des lignes
    tableRows.forEach((row, index) => {
        // Animation d'entrée
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 50);
        
        // Effets hover améliorés
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.zIndex = '10';
            this.style.boxShadow = '0 5px 15px rgba(139, 69, 19, 0.2)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = 'auto';
            this.style.boxShadow = 'none';
        });
    });
    
    // Améliorer les boutons d'action
    const actionButtons = document.querySelectorAll('.actions .btn');
    actionButtons.forEach(button => {
        if (button.textContent.includes('Supprimer')) {
            button.addEventListener('click', handleDeleteConfirmation);
        }
    });
}

function handleDeleteConfirmation(e) {
    e.preventDefault();
    
    const button = e.currentTarget;
    const url = button.href;
    const row = button.closest('tr');
    const studentName = row.cells[0].textContent + ' ' + row.cells[1].textContent;
    
    // Créer une boîte de dialogue personnalisée
    const modal = createConfirmationModal(
        'Confirmation de suppression',
        `Êtes-vous vraiment sûr de vouloir supprimer définitivement l'étudiant :\n\n"${studentName.trim()}"\n\nCette action est irréversible !`,
        () => {
            // Animation de suppression
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(-100%)';
            row.style.backgroundColor = '#ffebee';
            
            setTimeout(() => {
                window.location.href = url;
            }, 500);
        }
    );
    
    document.body.appendChild(modal);
}

function createConfirmationModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    const dialog = document.createElement('div');
    dialog.style.cssText = `
        background: white;
        padding: 2rem;
        border-radius: 15px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(139, 69, 19, 0.3);
        transform: scale(0.7);
        transition: transform 0.3s ease;
    `;
    
    dialog.innerHTML = `
        <h3 style="color: #8B4513; margin-bottom: 1rem;">${title}</h3>
        <p style="color: #666; margin-bottom: 2rem; white-space: pre-line;">${message}</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button class="btn btn-danger confirm-btn">Oui, supprimer</button>
            <button class="btn btn-primary cancel-btn">Annuler</button>
        </div>
    `;
    
    modal.appendChild(dialog);
    
    // Événements
    dialog.querySelector('.confirm-btn').addEventListener('click', () => {
        closeModal(modal);
        onConfirm();
    });
    
    dialog.querySelector('.cancel-btn').addEventListener('click', () => {
        closeModal(modal);
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    // Animation d'ouverture
    setTimeout(() => {
        modal.style.opacity = '1';
        dialog.style.transform = 'scale(1)';
    }, 10);
    
    return modal;
}

function closeModal(modal) {
    modal.style.opacity = '0';
    modal.querySelector('div').style.transform = 'scale(0.7)';
    setTimeout(() => modal.remove(), 300);
}

// === SYSTÈME D'ALERTES ===
function initializeAlerts() {
    // Auto-masquer les alertes existantes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            hideAlert(alert);
        }, 5000);
    });
}

function hideAlert(alert) {
    alert.style.transition = 'all 0.5s ease';
    alert.style.opacity = '0';
    alert.style.transform = 'translateY(-20px)';
    alert.style.maxHeight = '0';
    alert.style.padding = '0';
    alert.style.margin = '0';
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 500);
}

function showNotification(message, type = 'info', duration = 4000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 400px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.4s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animation d'apparition
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-masquer
    setTimeout(() => {
        hideAlert(notification);
    }, duration);
    
    return notification;
}

// === FONCTIONNALITÉS DE RECHERCHE ===
function initializeSearchFeatures() {
    const searchInputs = document.querySelectorAll('input[name="search"]');
    
    searchInputs.forEach(input => {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            const value = this.value.trim();
            
            // Recherche en temps réel pour les tableaux locaux
            if (this.id === 'search') {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performLocalSearch(value);
                }, 300);
            }
        });
        
        // Ajout d'un indicateur de recherche
        addSearchIndicator(input);
    });
}

function performLocalSearch(searchTerm) {
    const tableRows = document.querySelectorAll('.table tbody tr');
    const searchLower = searchTerm.toLowerCase();
    let visibleCount = 0;
    
    tableRows.forEach(row => {
        const nom = row.cells[0].textContent.toLowerCase();
        const prenom = row.cells[1].textContent.toLowerCase();
        const isMatch = nom.includes(searchLower) || prenom.includes(searchLower);
        
        if (isMatch || !searchTerm) {
            row.style.display = '';
            visibleCount++;
            
            // Highlight des termes trouvés
            if (searchTerm) {
                highlightText(row.cells[0], searchTerm);
                highlightText(row.cells[1], searchTerm);
            } else {
                removeHighlight(row.cells[0]);
                removeHighlight(row.cells[1]);
            }
        } else {
            row.style.display = 'none';
        }
    });
    
    // Afficher le nombre de résultats
    updateSearchResults(visibleCount, tableRows.length);
}

function highlightText(element, searchTerm) {
    const originalText = element.textContent;
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    const highlightedText = originalText.replace(regex, '<mark style="background: #FFD700; padding: 2px 4px; border-radius: 3px;">$1</mark>');
    
    if (highlightedText !== originalText) {
        element.innerHTML = highlightedText;
    }
}

function removeHighlight(element) {
    const marks = element.querySelectorAll('mark');
    marks.forEach(mark => {
        mark.outerHTML = mark.textContent;
    });
}

function addSearchIndicator(input) {
    const indicator = document.createElement('div');
    indicator.className = 'search-indicator';
    indicator.style.cssText = `
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.85rem;
        color: #8B4513;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    indicator.innerHTML = '🔍';
    
    const parent = input.parentNode;
    parent.style.position = 'relative';
    parent.appendChild(indicator);
    
    input.addEventListener('focus', () => {
        indicator.style.opacity = '1';
    });
    
    input.addEventListener('blur', () => {
        if (!input.value) {
            indicator.style.opacity = '0';
        }
    });
}

function updateSearchResults(visible, total) {
    let resultsInfo = document.querySelector('.search-results-info');
    
    if (!resultsInfo) {
        resultsInfo = document.createElement('div');
        resultsInfo.className = 'search-results-info';
        resultsInfo.style.cssText = `
            text-align: center;
            margin: 1rem 0;
            color: #8B4513;
            font-weight: 500;
            transition: all 0.3s ease;
        `;
        
        const table = document.querySelector('.table');
        if (table) {
            table.parentNode.insertBefore(resultsInfo, table);
        }
    }
    
    if (visible < total) {
        resultsInfo.textContent = `${visible} résultat(s) trouvé(s) sur ${total}`;
        resultsInfo.style.opacity = '1';
    } else {
        resultsInfo.style.opacity = '0';
    }
}

// === ACCESSIBILITÉ ===
function initializeAccessibility() {
    // Navigation au clavier
    document.addEventListener('keydown', function(e) {
        // Échap pour fermer les modales
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('[style*="position: fixed"]');
            modals.forEach(modal => {
                if (modal.style.zIndex >= 1000) {
                    closeModal(modal);
                }
            });
        }
        
        // Raccourcis clavier
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'f':
                case 'F':
                    e.preventDefault();
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                    break;
            }
        }
    });
    
    // Amélioration du focus
    const focusableElements = document.querySelectorAll('input, select, textarea, button, a[href]');
    focusableElements.forEach(element => {
        element.addEventListener('focus', function() {
            this.style.outline = '3px solid rgba(139, 69, 19, 0.5)';
            this.style.outlineOffset = '2px';
        });
        
        element.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
    });
}

// === UTILITAIRES ===
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function formatPhoneNumber(phone) {
    const digits = phone.replace(/\D/g, '');
    if (digits.startsWith('221')) {
        return digits.replace(/(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
    }
    return digits.replace(/(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
}

// Fonction globale pour les tests
window.ISEPSystem = {
    showNotification,
    validateField,
    formatPhoneNumber
};

console.log('✅ Système ISEP-Thiès entièrement chargé');