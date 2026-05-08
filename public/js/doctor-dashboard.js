/**
 * SANADK Doctor Dashboard - Dynamic Version
 * Patient management and monitoring
 */

let currentPatientId = null;
let patientCharts = {};

document.addEventListener('DOMContentLoaded', () => {
    loadDoctorProfile();
    loadPatients();
    loadAnalytics();
    setupSocketIO();
});

function loadDoctorProfile() {
    const user = JSON.parse(localStorage.getItem('currentUser')) || { full_name: 'د. فاطمة علي', username: 'doctor1' };
    const userNameElements = document.querySelectorAll('#userName, .user-name');
    userNameElements.forEach(el => {
        el.textContent = user.full_name || user.username;
    });
}

function loadPatients() {
    fetch('/api/users')
        .then(response => response.json())
        .then(users => {
            const patients = users.filter(u => u.role === 'patient');
            displayPatients(patients);
        })
        .catch(err => console.error('Error loading patients:', err));
}

function displayPatients(patients) {
    const patientsList = document.getElementById('patientsList');
    if (!patientsList) return;
    
    patientsList.innerHTML = '';
    patients.forEach(patient => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${patient.full_name}</td>
            <td>30</td>
            <td>عامة</td>
            <td><span class="status-badge active">نشط</span></td>
            <td>2024-04-22</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="viewPatientDetails(${patient.id}, '${patient.full_name}')">
                    عرض التفاصيل
                </button>
            </td>
        `;
        patientsList.appendChild(row);
    });
}

function loadAnalytics() {
    fetch('/api/stats')
        .then(response => response.json())
        .then(data => {
            const elements = {
                'totalPatients': data.active_patients,
                'totalSeizures': data.total_seizures_detected,
                'avgAccuracy': (data.average_detection_accuracy * 100).toFixed(1) + '%'
            };
            for (const [id, val] of Object.entries(elements)) {
                const el = document.getElementById(id);
                if (el) el.textContent = val;
            }
        });
}

function setupSocketIO() {
    const socket = io();
    socket.on('alert_notification', (alert) => {
        // Show alert in doctor dashboard
        console.log('🚨 Emergency Alert:', alert);
    });
}

function viewPatientDetails(id, name) {
    currentPatientId = id;
    const modal = document.getElementById('patientDetailsModal');
    if (modal) {
        document.getElementById('patientName').textContent = name;
        modal.style.display = 'block';
        // Load live data for this patient
        startPatientLiveMonitoring(id);
    }
}

function startPatientLiveMonitoring(id) {
    // Simulate live data for the selected patient in the modal
    setInterval(() => {
        if (currentPatientId === id) {
            fetch('/api/physiological-data')
                .then(res => res.json())
                .then(data => {
                    // Update modal charts/values
                    if (document.getElementById('modalHeartRate')) 
                        document.getElementById('modalHeartRate').textContent = Math.round(data.heart_rate);
                });
        }
    }, 3000);
}

function closePatientDetailsModal() {
    const modal = document.getElementById('patientDetailsModal');
    if (modal) modal.style.display = 'none';
    currentPatientId = null;
}
