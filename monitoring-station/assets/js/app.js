// /**
//  * VayuDarpan - Main Application
//  * Global utilities, modals, and notifications
//  */

// /**
//  * Show Modal Dialog
//  */
// function showModal(title, content, actions = []) {
//     const container = document.getElementById('modal-container');
    
//     const modal = document.createElement('div');
//     modal.className = 'modal active';
    
//     const footer = actions.length > 0 ? `
//         <div class="modal-footer">
//             ${actions.map((action, idx) => 
//                 `<button class="btn ${idx === 0 ? 'btn-primary' : 'btn-secondary'}" data-action="${idx}">
//                     ${action.text}
//                 </button>`
//             ).join('')}
//         </div>
//     ` : '';
    
//     modal.innerHTML = `
//         <div class="modal-content">
//             <div class="modal-header">
//                 <h2>${title}</h2>
//                 <button class="modal-close" onclick="closeModal()">&times;</button>
//             </div>
//             <div class="modal-body">
//                 ${content}
//             </div>
//             ${footer}
//         </div>
//     `;
    
//     container.innerHTML = '';
//     container.appendChild(modal);
    
//     // Attach action handlers
//     actions.forEach((action, idx) => {
//         const btn = modal.querySelector(`[data-action="${idx}"]`);
//         if (btn) {
//             btn.addEventListener('click', action.action);
//         }
//     });
    
//     // Close on backdrop click
//     modal.addEventListener('click', (e) => {
//         if (e.target === modal) {
//             closeModal();
//         }
//     });
    
//     // Close on Escape key
//     const handleEscape = (e) => {
//         if (e.key === 'Escape') {
//             closeModal();
//             document.removeEventListener('keydown', handleEscape);
//         }
//     };
//     document.addEventListener('keydown', handleEscape);
// }

// /**
//  * Close Modal
//  */
// function closeModal() {
//     const container = document.getElementById('modal-container');
//     const modal = container.querySelector('.modal');
//     if (modal) {
//         modal.classList.remove('active');
//         setTimeout(() => container.innerHTML = '', 300);
//     }
// }

// /**
//  * Show Notification Toast
//  */
// function showNotification(message, type = 'info', duration = 4000) {
//     const notification = document.createElement('div');
//     notification.className = `notification ${type}`;
//     notification.textContent = message;
    
//     document.body.appendChild(notification);
    
//     setTimeout(() => {
//         notification.style.animation = 'slideInRight 0.3s ease-out reverse';
//         setTimeout(() => notification.remove(), 300);
//     }, duration);
// }

// /**
//  * Format timestamp to readable date
//  */
// function formatDate(isoString) {
//     const date = new Date(isoString);
//     return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {
//         hour: '2-digit',
//         minute: '2-digit'
//     });
// }

// /**
//  * Format date only
//  */
// function formatDateOnly(isoString) {
//     const date = new Date(isoString);
//     return date.toLocaleDateString();
// }

// /**
//  * Get AQI color class
//  */
// function getAqiColorClass(value) {
//     if (value <= 50) return 'aqi-good';
//     if (value <= 100) return 'aqi-moderate';
//     if (value <= 200) return 'aqi-poor';
//     return 'aqi-severe';
// }

// /**
//  * Get AQI status text
//  */
// function getAqiStatusText(value) {
//     if (value <= 50) return 'GOOD';
//     if (value <= 100) return 'MODERATE';
//     if (value <= 200) return 'POOR';
//     return 'SEVERE';
// }

// /**
//  * Create AQI card HTML
//  */
// function createAqiCard(title, value, status = null) {
//     const calculatedStatus = status || ApiService.getAqiStatus(value);
//     const colorClass = getAqiColorClass(value);
//     const statusText = getAqiStatusText(value);
    
//     return `
//         <div class="card aqi-card ${colorClass}">
//             <div class="aqi-card-content">
//                 <div class="aqi-value">${value}</div>
//                 <div class="aqi-label">${statusText}</div>
//                 <p style="margin-top: 0.5rem; font-size: 0.85rem; opacity: 0.8;">${title}</p>
//             </div>
//         </div>
//     `;
// }

// /**
//  * Validate form inputs
//  */
// function validateForm(formData, requiredFields) {
//     const errors = {};
    
//     requiredFields.forEach(field => {
//         if (!formData[field] || formData[field].toString().trim() === '') {
//             errors[field] = `${field} is required`;
//         }
//     });
    
//     return {
//         isValid: Object.keys(errors).length === 0,
//         errors
//     };
// }

// /**
//  * Show validation errors in modal
//  */
// function showValidationErrors(errors) {
//     const errorHtml = `
//         <div style="color: var(--color-danger);">
//             <strong>Please fix the following errors:</strong>
//             <ul style="margin-top: 1rem; margin-left: 1.5rem;">
//                 ${Object.values(errors).map(error => `<li>${error}</li>`).join('')}
//             </ul>
//         </div>
//     `;
    
//     showModal('Validation Error', errorHtml, [
//         { text: 'OK', action: closeModal }
//     ]);
// }

// /**
//  * File input handler with preview
//  */
// function handleFileInputChange(inputId, onFilesSelected) {
//     const input = document.getElementById(inputId);
//     if (!input) return;
    
//     input.addEventListener('change', (e) => {
//         const files = Array.from(e.target.files);
//         onFilesSelected(files);
//     });
// }

// /**
//  * Create file preview container
//  */
// function createFilePreviewContainer(files) {
//     const container = document.createElement('div');
//     container.className = 'file-preview-container';
//     container.style.cssText = `
//         display: grid;
//         grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
//         gap: 1rem;
//         margin-top: 1rem;
//     `;
    
//     files.forEach((file, idx) => {
//         const reader = new FileReader();
        
//         reader.onload = (e) => {
//             const preview = document.createElement('div');
//             preview.style.cssText = `
//                 position: relative;
//                 border-radius: var(--radius-md);
//                 overflow: hidden;
//                 aspect-ratio: 1;
//                 background: var(--color-surface-alt);
//             `;
            
//             if (file.type.startsWith('image/')) {
//                 const img = document.createElement('img');
//                 img.src = e.target.result;
//                 img.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';
//                 preview.appendChild(img);
//             } else {
//                 preview.innerHTML = `
//                     <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 0.8rem; text-align: center; padding: 0.5rem;">
//                         ${file.name}
//                     </div>
//                 `;
//             }
            
//             const removeBtn = document.createElement('button');
//             removeBtn.innerHTML = '&times;';
//             removeBtn.style.cssText = `
//                 position: absolute;
//                 top: 0;
//                 right: 0;
//                 background: rgba(0, 0, 0, 0.7);
//                 color: white;
//                 border: none;
//                 width: 24px;
//                 height: 24px;
//                 cursor: pointer;
//                 font-size: 1.2rem;
//             `;
//             removeBtn.onclick = (e) => {
//                 e.preventDefault();
//                 preview.remove();
//             };
            
//             preview.appendChild(removeBtn);
//             container.appendChild(preview);
//         };
        
//         reader.readAsDataURL(file);
//     });
    
//     return container;
// }

// /**
//  * Navigation helper
//  */
// function navigateTo(page) {
//     window.location.href = `?page=${page}`;
// }

// /**
//  * Create filter dropdown
//  */
// function createFilterDropdown(options, onSelect) {
//     const select = document.createElement('select');
//     select.className = 'form-select';
    
//     const defaultOption = document.createElement('option');
//     defaultOption.value = '';
//     defaultOption.textContent = 'Select...';
//     select.appendChild(defaultOption);
    
//     options.forEach(opt => {
//         const option = document.createElement('option');
//         option.value = opt.value;
//         option.textContent = opt.label;
//         select.appendChild(option);
//     });
    
//     select.addEventListener('change', (e) => {
//         if (onSelect) onSelect(e.target.value);
//     });
    
//     return select;
// }

// /**
//  * Debounce function for input handlers
//  */
// function debounce(func, wait) {
//     let timeout;
//     return function executedFunction(...args) {
//         const later = () => {
//             clearTimeout(timeout);
//             func(...args);
//         };
//         clearTimeout(timeout);
//         timeout = setTimeout(later, wait);
//     };
// }

// /**
//  * Get unique values from array
//  */
// function getUniqueValues(array, key) {
//     return [...new Set(array.map(item => item[key]))];
// }

// /**
//  * Initialize app
//  */
// document.addEventListener('DOMContentLoaded', () => {
//     // Set initial theme
//     const theme = localStorage.getItem('theme') || 'light';
//     document.documentElement.setAttribute('data-theme', theme);
    
//     // Log app initialization
//     console.log('%cVayuDarpan Monitoring Station', 'color: #00bcd4; font-size: 16px; font-weight: bold;');
//     console.log('%cAir Pollution Management System', 'color: #00bcd4; font-size: 12px;');
// });

// /**
//  * Export for use in modules
//  */
// const AppUtils = {
//     showModal,
//     closeModal,
//     showNotification,
//     formatDate,
//     formatDateOnly,
//     getAqiColorClass,
//     getAqiStatusText,
//     createAqiCard,
//     validateForm,
//     showValidationErrors,
//     navigateTo
// };
/**
 * VayuDarpan - Main Application
 * Global utilities, modals, and notifications
 */

/**
 * Initialize Theme Toggle (FIXED)
 * Uses .light class on body element for proper CSS variable switching
 */
(function initTheme() {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
    
    // Set initial theme
    setTheme(savedTheme);
    
    // Update theme toggle listener in navbar
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        });
    }
})();

function setTheme(theme) {
    if (theme === 'light') {
        document.body.classList.add('light');
    } else {
        document.body.classList.remove('light');
    }
    localStorage.setItem('theme', theme);
}

/**
 * Show Modal Dialog
 */
function showModal(title, content, actions = []) {
    const container = document.getElementById('modal-container');
    
    const modal = document.createElement('div');
    modal.className = 'modal active';
    
    const footer = actions.length > 0 ? `
        <div class="modal-footer">
            ${actions.map((action, idx) => 
                `<button class="btn ${idx === 0 ? 'btn-primary' : 'btn-secondary'}" data-action="${idx}">
                    ${action.text}
                </button>`
            ).join('')}
        </div>
    ` : '';
    
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>${title}</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
            ${footer}
        </div>
    `;
    
    container.innerHTML = '';
    container.appendChild(modal);
    
    // Attach action handlers
    actions.forEach((action, idx) => {
        const btn = modal.querySelector(`[data-action="${idx}"]`);
        if (btn) {
            btn.addEventListener('click', action.action);
        }
    });
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
}

/**
 * Close Modal Dialog
 */
function closeModal() {
    const modal = document.querySelector('.modal.active');
    if (modal) {
        modal.remove();
    }
}

/**
 * Show Notification Toast
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 4000);
}

/**
 * Format Date
 */
function formatDate(date) {
    if (typeof date === 'string') date = new Date(date);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

function formatDateOnly(date) {
    if (typeof date === 'string') date = new Date(date);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
}

/**
 * Get AQI Color Class
 */
function getAqiColorClass(value) {
    if (value <= 50) return 'aqi-good';
    if (value <= 100) return 'aqi-moderate';
    if (value <= 150) return 'aqi-poor';
    return 'aqi-severe';
}

function getAqiStatusText(value) {
    if (value <= 50) return 'Good';
    if (value <= 100) return 'Moderate';
    if (value <= 150) return 'Poor';
    return 'Severe';
}

/**
 * Create AQI Card HTML
 */
function createAqiCard(value, label) {
    const colorClass = getAqiColorClass(value);
    const statusText = getAqiStatusText(value);
    
    return `
        <div class="aqi-card ${colorClass}">
            <div class="aqi-card-content">
                <div class="aqi-value">${value}</div>
                <div class="aqi-label">${statusText}</div>
            </div>
        </div>
    `;
}

/**
 * Validate Form
 */
function validateForm(formData, rules) {
    const errors = {};
    
    for (const [field, rule] of Object.entries(rules)) {
        const value = formData[field];
        
        if (rule.required && !value) {
            errors[field] = `${field} is required`;
        }
        
        if (rule.minLength && value && value.length < rule.minLength) {
            errors[field] = `${field} must be at least ${rule.minLength} characters`;
        }
        
        if (rule.email && value && !value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            errors[field] = `${field} must be a valid email`;
        }
    }
    
    return errors;
}

function showValidationErrors(errors) {
    Object.entries(errors).forEach(([field, message]) => {
        showNotification(message, 'error');
    });
}

/**
 * Navigation
 */
function navigateTo(page) {
    window.location.href = `?page=${page}`;
}

/**
 * Debounce Helper
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
}

/**
 * App Utils Export
 */
const AppUtils = {
    showModal,
    closeModal,
    showNotification,
    formatDate,
    formatDateOnly,
    getAqiColorClass,
    getAqiStatusText,
    createAqiCard,
    validateForm,
    showValidationErrors,
    navigateTo,
    debounce,
    setTheme
};