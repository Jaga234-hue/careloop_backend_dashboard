<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Careloop+</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#0ea5e9', secondary: '#38bdf8', accent: '#f59e0b' }
                }
            }
        }
        
        const token = localStorage.getItem('careloop_token');
        const doctorId = parseInt(localStorage.getItem('careloop_user_id'));
        if(!token) window.location.href = 'index.php';
    </script>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-xl flex flex-col justify-between hidden md:flex z-10 transition-all duration-300">
        <div>
            <div class="h-20 flex items-center px-8 border-b border-gray-100">
                <h1 class="text-2xl font-bold tracking-tight text-gray-800">CARELOOP<span class="text-primary">+</span></h1>
            </div>
            <div class="p-4 mt-4 space-y-1">
                <a href="dashboard.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-primary rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-users w-6 text-center mr-2"></i> Patients View
                </a>
                <a href="appointments.php" class="flex items-center px-4 py-3 text-primary bg-sky-50 rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-calendar-check w-6 text-center mr-2"></i> Appointments
                </a>
                <a href="chat.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-primary rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-comments w-6 text-center mr-2"></i> Messages
                </a>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-100 flex items-center">
            <a href="dashboard.php" class="text-sm text-gray-500 hover:text-gray-800"><i class="fa-solid fa-arrow-left mr-2"></i> Back</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto relative p-8">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-sky-100/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto w-full">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Schedule & Appointments</h2>
                    <p class="text-gray-500 mt-1">Manage your video calls and in-person visits</p>
                </div>
                <button onclick="document.getElementById('addAppointmentModal').classList.remove('hidden')" class="px-5 py-2.5 bg-primary text-white rounded-xl hover:bg-secondary font-medium shadow-sm flex items-center transition-all hover:shadow-md transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus mr-2"></i> Schedule New
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Appointments List -->
                <div class="lg:col-span-2 space-y-4" id="appointmentsContainer">
                    <!-- Cards populated via JS -->
                    <div class="p-8 text-center text-gray-400 bg-white rounded-2xl border border-gray-100">Loading schedule...</div>
                </div>

                <!-- Calendar/Stats Column (Static visual) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 h-6 border-b border-gray-100 pb-2">Calendar Quick View</h3>
                        <div class="text-center text-gray-400 text-sm py-8"><i class="fa-regular fa-calendar-alt text-4xl opacity-50 mb-3 block"></i> Select dates to filter</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for New Appointment -->
    <div id="addAppointmentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl mx-4 transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Schedule Appointment</h3>
                <button onclick="document.getElementById('addAppointmentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form id="addAppointmentForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Patient</label>
                    <select id="patientSelect" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="">Loading patients...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Appointment Type</label>
                    <select id="apptType" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        <option value="Video Call">Video Call</option>
                        <option value="In Person Visit">In Person Visit</option>
                        <option value="Follow-up Call">Follow-up Call</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="apptDate" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <input type="time" id="apptTime" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    </div>
                </div>
                
                <div class="mt-8 pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-5 py-2 bg-primary text-white font-medium rounded-lg hover:bg-secondary transition-colors">Confirm Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load Patients for Dropdown
        let patientsMap = {};
        async function loadPatientsDropdown() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/patients`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    const patients = await response.json();
                    const select = document.getElementById('patientSelect');
                    select.innerHTML = '<option value="">Select a patient...</option>';
                    patients.forEach(p => {
                        patientsMap[p.id] = p.name;
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.name;
                        select.appendChild(opt);
                    });
                }
            } catch(e) { console.error("Error loading patients", e); }
        }

        // Load Appointments
        async function loadAppointments() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/appointments/${doctorId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    const appointments = await response.json();
                    renderAppointments(appointments);
                }
            } catch(e) { console.error("Error loading appointments", e); }
        }

        function renderAppointments(appointments) {
            const container = document.getElementById('appointmentsContainer');
            if(appointments.length === 0) {
                container.innerHTML = `
                    <div class="p-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
                        <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mb-4"><i class="fa-regular fa-calendar-xmark text-2xl"></i></div>
                        <h3 class="text-gray-800 font-medium">No Appointments Scheduled</h3>
                        <p class="text-sm text-gray-500 mt-1">You have no upcoming visits.</p>
                    </div>`;
                return;
            }

            // Sort by date ascending
            appointments.sort((a,b) => new Date(a.appointment_date) - new Date(b.appointment_date));

            container.innerHTML = appointments.map(apt => {
                const dt = new Date(apt.appointment_date);
                const month = dt.toLocaleString('en', { month: 'short' });
                const day = dt.getDate();
                const time = dt.toLocaleString('en', { hour: 'numeric', minute: '2-digit', hour12: true });
                const patientName = patientsMap[apt.patient_id] || "Patient ID: " + apt.patient_id;
                
                const icon = apt.appointment_type.includes('Video') ? 'fa-video text-blue-500 bg-blue-50' : 'fa-handshake text-purple-500 bg-purple-50';

                return `
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center group hover:shadow-md transition-all">
                    <!-- Date Badge -->
                    <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 flex flex-col items-center justify-center mr-5 flex-shrink-0">
                        <span class="text-xs text-red-500 font-bold uppercase tracking-wider">${month}</span>
                        <span class="text-xl font-bold text-gray-800 leading-none mt-1">${day}</span>
                    </div>
                    
                    <!-- Details -->
                    <div class="flex-1">
                        <div class="flex items-center text-xs text-gray-500 font-medium mb-1">
                            <i class="fa-regular fa-clock mr-1 text-primary"></i> ${time}
                            <span class="mx-2">•</span>
                            <span class="px-2 py-0.5 rounded-md ${icon}"><i class="fa-solid fa-fw mr-1"></i> ${apt.appointment_type}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 group-hover:text-primary transition-colors">${patientName}</h3>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="w-8 h-8 rounded-full border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors flex items-center justify-center" title="Cancel">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                `;
            }).join('');
        }

        // Handle Add Form
        document.getElementById('addAppointmentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const pId = document.getElementById('patientSelect').value;
            const type = document.getElementById('apptType').value;
            const date = document.getElementById('apptDate').value;
            const time = document.getElementById('apptTime').value;
            
            // combine date & time
            const dateTimeBlob = new Date(`${date}T${time}`).toISOString();
            
            try {
                const response = await fetch(`${API_BASE_URL}/api/appointments`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        doctor_id: doctorId,
                        patient_id: parseInt(pId),
                        appointment_type: type,
                        appointment_date: dateTimeBlob
                    })
                });
                
                if(response.ok) {
                    document.getElementById('addAppointmentModal').classList.add('hidden');
                    document.getElementById('addAppointmentForm').reset();
                    loadAppointments(); // refresh
                } else {
                    alert("Failed to create appointment");
                }
            } catch(err) { console.error("Error creating appointment", err); }
        });

        // Init
        async function boot() {
            await loadPatientsDropdown();
            await loadAppointments();
        }
        boot();
    </script>
</body>
</html>
