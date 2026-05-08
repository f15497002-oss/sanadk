/**
 * SANADK Patient Dashboard - Dynamic Version
 * Real-time monitoring and interaction
 */

// Initialize charts
let eegChart, emgChart, hrvChart;
let socket;

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadUserProfile();
    initializeCharts();
    loadDashboardData();
    setupSocketIO();
    
    // Start periodic data polling as fallback for Socket.IO
    setInterval(pollPhysiologicalData, 5000);
});

// ============================================================================
// USER PROFILE
// ============================================================================

function loadUserProfile() {
    const user = JSON.parse(localStorage.getItem('currentUser')) || { full_name: 'أحمد محمد', username: 'patient1' };
    const userNameElements = document.querySelectorAll('#userName, .user-name');
    userNameElements.forEach(el => {
        el.textContent = user.full_name || user.username;
    });
}

// ============================================================================
// CHARTS INITIALIZATION
// ============================================================================

function initializeCharts() {
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 0 },
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    };

    const createChart = (id, label, color) => {
        const ctx = document.getElementById(id).getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array(20).fill(''),
                datasets: [{
                    label: label,
                    data: Array(20).fill(0),
                    borderColor: color,
                    backgroundColor: color + '20',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0
                }]
            },
            options: chartOptions
        });
    };

    if (document.getElementById('eegChart')) eegChart = createChart('eegChart', 'EEG', '#4A90E2');
    if (document.getElementById('emgChart')) emgChart = createChart('emgChart', 'EMG', '#50C878');
    if (document.getElementById('hrvChart')) hrvChart = createChart('hrvChart', 'HRV', '#F39C12');
}

function updateChart(chart, newValue) {
    if (!chart) return;
    chart.data.datasets[0].data.push(newValue);
    chart.data.datasets[0].data.shift();
    chart.update();
}

// ============================================================================
// DATA FETCHING & SOCKET.IO
// ============================================================================

function setupSocketIO() {
    socket = io();
    
    socket.on('connect', () => {
        console.log('✅ Connected to SANADK Real-time Server');
    });

    socket.on('data_update', (data) => {
        updateUIWithData(data);
    });

    socket.on('alert_notification', (alert) => {
        showNotification(alert.title, alert.message, alert.severity);
        loadAlerts(); // Refresh alerts list
    });
}

function pollPhysiologicalData() {
    fetch('/api/physiological-data')
        .then(response => response.json())
        .then(data => {
            updateUIWithData(data);
        })
        .catch(err => console.error('Error polling data:', err));
}

function updateUIWithData(data) {
    // Update numeric values
    if (document.getElementById('heartRateValue')) document.getElementById('heartRateValue').textContent = Math.round(data.heart_rate);
    if (document.getElementById('oxygenValue')) document.getElementById('oxygenValue').textContent = data.oxygen_level + '%';
    if (document.getElementById('tempValue')) document.getElementById('tempValue').textContent = data.temperature + '°C';
    if (document.getElementById('riskValue')) document.getElementById('riskValue').textContent = data.risk_level + '%';
    
    // Update circular indicators if they exist
    const healthCircle = document.querySelector('.health-circle .percentage');
    if (healthCircle) healthCircle.textContent = Math.round(100 - data.risk_level) + '%';
    
    const hrCircle = document.querySelector('.hr-circle .percentage');
    if (hrCircle) hrCircle.textContent = Math.round(data.heart_rate);

    // Update charts
    if (data.eeg) updateChart(eegChart, data.eeg.value);
    if (data.emg) updateChart(emgChart, data.emg.value);
    if (data.hrv) updateChart(hrvChart, data.hrv.value);
}

function loadDashboardData() {
    loadSeizures();
    loadStats();
}

function loadSeizures() {
    fetch('/api/seizures')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('seizuresList') || document.querySelector('.seizures-list');
            if (!list) return;
            
            list.innerHTML = '';
            data.forEach(s => {
                const item = document.createElement('div');
                item.className = 'seizure-card';
                item.innerHTML = `
                    <div class="seizure-info">
                        <span class="seizure-date">${s.date} | ${s.time}</span>
                        <span class="seizure-type">${s.detected_by}</span>
                    </div>
                    <div class="seizure-meta">
                        <span class="badge ${s.severity}">${getSeverityAr(s.severity)}</span>
                        <span class="duration">${s.duration} ثانية</span>
                    </div>
                `;
                list.appendChild(item);
            });
        });
}

function loadStats() {
    fetch('/api/stats')
        .then(response => response.json())
        .then(data => {
            // Update stats in UI if elements exist
            const elements = {
                'totalUsers': data.total_users,
                'activePatients': data.active_patients,
                'accuracyVal': (data.average_detection_accuracy * 100).toFixed(0) + '%'
            };
            for (const [id, val] of Object.entries(elements)) {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            }
        });
}

function getSeverityAr(sev) {
    const map = { 'mild': 'خفيفة', 'moderate': 'متوسطة', 'severe': 'شديدة' };
    return map[sev] || sev;
}

function showNotification(title, message, type) {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<strong>${title}</strong><p>${message}</p>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}
