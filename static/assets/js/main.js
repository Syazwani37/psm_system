/**
 * PSM SYSTEM - Main JavaScript
 * Sistem Pengurusan Kesihatan Ibu dan Bayi
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('PSM System initialized');
    
    // Initialize components
    initSidebar();
    initAlerts();
    initForms();
});

/**
 * Initialize sidebar toggle for mobile
 */
function initSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
}

/**
 * Initialize auto-dismiss alerts
 */
function initAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    
    alerts.forEach(function(alert) {
        const duration = parseInt(alert.dataset.autoDismiss) || 5000;
        
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, duration);
    });
}

/**
 * Initialize form validation
 */
function initForms() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validate a form
 * @param {HTMLFormElement} form 
 * @returns {boolean}
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            isValid = false;
            showFieldError(field, 'Ruangan ini wajib diisi');
        } else {
            clearFieldError(field);
        }
    });
    
    return isValid;
}

/**
 * Show error message for a field
 * @param {HTMLElement} field 
 * @param {string} message 
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = 'var(--error)';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

/**
 * Clear error message for a field
 * @param {HTMLElement} field 
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

/**
 * Show notification toast
 * @param {string} message 
 * @param {string} type - 'success', 'error', 'warning', 'info'
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideIn 0.3s ease';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.style.opacity = '0';
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 3000);
}

/**
 * Format date to Malaysian format
 * @param {Date} date 
 * @returns {string}
 */
function formatDate(date) {
    const options = { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric' 
    };
    return new Date(date).toLocaleDateString('ms-MY', options);
}

/**
 * Format time to 12-hour format
 * @param {Date} date 
 * @returns {string}
 */
function formatTime(date) {
    const options = { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true
    };
    return new Date(date).toLocaleTimeString('ms-MY', options);
}
