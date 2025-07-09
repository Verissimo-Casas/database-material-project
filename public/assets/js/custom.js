// FILE: public/assets/js/custom.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Form validation
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Auto-hide alerts after 5 seconds
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-success')) {
            setTimeout(function() {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });

    // CPF mask
    var cpfInputs = document.querySelectorAll('input[name="cpf"]');
    cpfInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 11) {
                this.value = this.value.substring(0, 11);
            }
        });
    });

    // Phone mask
    var phoneInputs = document.querySelectorAll('input[name="contato"]');
    phoneInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 11) {
                this.value = this.value.substring(0, 11);
            }
        });
    });

    // Confirm dialogs
    var confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            var message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Auto-refresh notifications (every 5 minutes)
    if (document.querySelector('.notification-area')) {
        setInterval(function() {
            loadNotifications();
        }, 300000); // 5 minutes
    }

    // Dark mode toggle (if implemented)
    var darkModeToggle = document.querySelector('#darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });

        // Load saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }

    // Table search functionality
    var searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            var searchTerm = this.value.toLowerCase();
            var table = this.closest('.card').querySelector('table tbody');
            var rows = table.querySelectorAll('tr');

            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Auto-calculate IMC
    var pesoInput = document.querySelector('input[name="peso"]');
    var alturaInput = document.querySelector('input[name="altura"]');
    var imcInput = document.querySelector('input[name="imc"]');

    if (pesoInput && alturaInput && imcInput) {
        function calculateIMC() {
            var peso = parseFloat(pesoInput.value);
            var altura = parseFloat(alturaInput.value);

            if (peso && altura) {
                var imc = peso / (altura * altura);
                imcInput.value = imc.toFixed(2);
            }
        }

        pesoInput.addEventListener('input', calculateIMC);
        alturaInput.addEventListener('input', calculateIMC);
    }

    // Enhanced dropdown menus
    var dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('show.bs.dropdown', function() {
            this.setAttribute('aria-expanded', 'true');
        });
        dropdown.addEventListener('hide.bs.dropdown', function() {
            this.setAttribute('aria-expanded', 'false');
        });
    });

    // Progress bars animation
    var progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(function(bar) {
        var width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(function() {
            bar.style.width = width;
        }, 500);
    });

    // Custom file upload
    var fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : 'Escolher arquivo';
            this.nextElementSibling.textContent = fileName;
        });
    });
});

// Utility functions
function showAlert(message, type = 'info') {
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
    alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    
    var container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(function() {
        var bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 5000);
}

function loadNotifications() {
    // Implementation for loading notifications via AJAX
    fetch(BASE_URL + 'api/notifications')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
}

function updateNotificationBadge(count) {
    var badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Form utilities
function resetForm(formId) {
    var form = document.getElementById(formId);
    if (form) {
        form.reset();
        form.classList.remove('was-validated');
    }
}

function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    var sum = 0;
    for (var i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    var digit1 = ((sum * 10) % 11) % 10;
    
    sum = 0;
    for (var i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    var digit2 = ((sum * 10) % 11) % 10;
    
    return digit1 === parseInt(cpf.charAt(9)) && digit2 === parseInt(cpf.charAt(10));
}

// Export functions for global use
window.AcademiaUtils = {
    showAlert: showAlert,
    loadNotifications: loadNotifications,
    validateEmail: validateEmail,
    validateCPF: validateCPF,
    resetForm: resetForm
};
