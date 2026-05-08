/**
 * SANADK - Seizure Detection System
 * Main Frontend JavaScript
 */

// ============================================================================
// SOCKET.IO CONNECTION
// ============================================================================

const socket = io({
    reconnection: true,
    reconnectionDelay: 1000,
    reconnectionDelayMax: 5000,
    reconnectionAttempts: 5
});

socket.on('connect', () => {
    console.log('Connected to SANADK server');
    showNotification('متصل بنظام SANADK', 'success');
});

socket.on('disconnect', () => {
    console.log('Disconnected from server');
    showNotification('قطع الاتصال بالنظام', 'warning');
});

socket.on('error', (error) => {
    console.error('Socket error:', error);
    showNotification('خطأ في الاتصال: ' + error.message, 'error');
});

// ============================================================================
// AUTHENTICATION
// ============================================================================

let currentUser = null;
let authToken = null;

// Check if user is already logged in
document.addEventListener('DOMContentLoaded', () => {
    // Try both 'access_token' and 'authToken' for backward compatibility
    const savedToken = localStorage.getItem('access_token') || localStorage.getItem('authToken');
    const savedUser = localStorage.getItem('currentUser');
    
    if (savedToken && savedUser) {
        authToken = savedToken;
        currentUser = JSON.parse(savedUser);
        updateUIForLoggedInUser();
    }
});

function showLoginModal() {
    document.getElementById('loginModal').style.display = 'block';
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

function showRegisterModal() {
    document.getElementById('registerModal').style.display = 'block';
}

function closeRegisterModal() {
    document.getElementById('registerModal').style.display = 'none';
}

async function loginUser(username, password) {
    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });

        if (!response.ok) {
            const error = await response.json();
            showNotification(error.error || 'فشل الدخول', 'error');
            return null;
        }

        const data = await response.json();
        authToken = data.access_token;
        currentUser = {
            id: data.user_id,
            username: data.username,
            email: data.email,
            role: data.role,
            full_name: data.full_name || data.username
        };

        localStorage.setItem('access_token', authToken);
        localStorage.setItem('authToken', authToken);
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        updateUIForLoggedInUser();

        if (socket.connected) {
            socket.emit('user_connected', {
                user_id: currentUser.id,
                role: currentUser.role
            });
        }

        return currentUser;
    } catch (error) {
        console.error('Login error:', error);
        showNotification('خطأ في الدخول', 'error');
        return null;
    }
}

// Handle login form submission
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const username = document.getElementById('loginUsername').value;
        const password = document.getElementById('loginPassword').value;
        
        const user = await loginUser(username, password);
        if (user) {
            closeLoginModal();
            showNotification('تم الدخول بنجاح', 'success');
            redirectBasedOnRole(user.role);
        }
    });
}

// Handle register form submission
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const username = document.getElementById('registerUsername').value;
        const full_name = document.getElementById('registerFullName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const role = document.getElementById('registerRole').value;
        
        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, full_name, email, password, role })
            });
            
            if (response.ok) {
                const data = await response.json();
                showNotification('تم التسجيل بنجاح. جاري تسجيل الدخول تلقائياً...', 'success');
                closeRegisterModal();
                registerForm.reset();

                const user = await loginUser(username, password);
                if (user) {
                    redirectBasedOnRole(user.role);
                } else {
                    showLoginModal();
                }
            } else {
                const error = await response.json();
                showNotification(error.error || 'فشل التسجيل', 'error');
            }
        } catch (error) {
            console.error('Register error:', error);
            showNotification('خطأ في التسجيل', 'error');
        }
    });
}

function updateUIForLoggedInUser() {
    const authButtons = document.querySelector('.auth-buttons');
    if (authButtons && currentUser) {
        authButtons.innerHTML = `
            <div class="user-menu">
                <span class="user-name">${currentUser.full_name || currentUser.username}</span>
                <button class="btn btn-secondary" onclick="logout()">تسجيل الخروج</button>
            </div>
        `;
    }
}

function logout() {
    localStorage.removeItem('authToken');
    localStorage.removeItem('access_token');
    localStorage.removeItem('currentUser');
    authToken = null;
    currentUser = null;
    socket.disconnect();
    location.reload();
}

function redirectBasedOnRole(role) {
    switch(role) {
        case 'patient':
            window.location.href = '/patient-dashboard';
            break;
        case 'doctor':
            window.location.href = '/doctor-dashboard';
            break;
        case 'admin':
            window.location.href = '/admin-dashboard';
            break;
        case 'family':
            window.location.href = '/family-dashboard';
            break;
        default:
            window.location.href = '/';
    }
}

// ============================================================================
// NOTIFICATIONS
// ============================================================================

function showNotification(message, type = 'info') {
    const notificationDiv = document.createElement('div');
    notificationDiv.className = `notification notification-${type}`;
    notificationDiv.textContent = message;
    
    document.body.appendChild(notificationDiv);
    
    // Animate in
    setTimeout(() => {
        notificationDiv.classList.add('show');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notificationDiv.classList.remove('show');
        setTimeout(() => {
            notificationDiv.remove();
        }, 300);
    }, 5000);
}

// ============================================================================
// SOCKET.IO EVENT HANDLERS
// ============================================================================

// Handle real-time data updates
socket.on('eeg_data_update', (data) => {
    console.log('EEG data received:', data);
    updateEEGChart(data);
});

socket.on('emg_data_update', (data) => {
    console.log('EMG data received:', data);
    updateEMGChart(data);
});

socket.on('hrv_data_update', (data) => {
    console.log('HRV data received:', data);
    updateHRVChart(data);
});

// Handle alerts
socket.on('alert_notification', (alert) => {
    console.log('Alert received:', alert);
    showAlertNotification(alert);
});

// Handle device status
socket.on('device_status', (status) => {
    console.log('Device status:', status);
    updateDeviceStatus(status);
});

// ============================================================================
// CHART FUNCTIONS (Placeholder)
// ============================================================================

function updateEEGChart(data) {
    // TODO: Update EEG chart with real-time data
    console.log('Updating EEG chart with:', data);
}

function updateEMGChart(data) {
    // TODO: Update EMG chart with real-time data
    console.log('Updating EMG chart with:', data);
}

function updateHRVChart(data) {
    // TODO: Update HRV chart with real-time data
    console.log('Updating HRV chart with:', data);
}

function updateDeviceStatus(status) {
    // TODO: Update device status display
    console.log('Updating device status:', status);
}

// ============================================================================
// ALERT HANDLING
// ============================================================================

function showAlertNotification(alert) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-notification alert-${alert.severity}`;
    alertDiv.innerHTML = `
        <div class="alert-header">
            <h3>${alert.title}</h3>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
        <div class="alert-body">
            <p>${alert.message}</p>
            ${alert.location ? `<p>الموقع: ${alert.location}</p>` : ''}
            <p class="alert-time">${new Date(alert.timestamp).toLocaleString('ar-SA')}</p>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Play alert sound if critical
    if (alert.severity === 'critical') {
        playAlertSound();
    }
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 10000);
}

function playAlertSound() {
    // TODO: Play alert sound for critical alerts
    console.log('Playing alert sound');
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    if (event.target === loginModal) {
        loginModal.style.display = 'none';
    }
    if (event.target === registerModal) {
        registerModal.style.display = 'none';
    }
}

// ============================================================================
// API HELPER FUNCTIONS
// ============================================================================

async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (authToken) {
        options.headers['Authorization'] = `Bearer ${authToken}`;
    }
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(endpoint, options);
        
        if (response.status === 401) {
            // Token expired
            logout();
            return null;
        }
        
        return await response.json();
    } catch (error) {
        console.error('API call error:', error);
        return null;
    }
}

// ============================================================================
// INITIALIZATION
// ============================================================================

console.log('SANADK Frontend loaded successfully');
