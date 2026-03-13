<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careloop+ Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#0ea5e9', secondary: '#38bdf8', accent: '#f59e0b', dark: '#0f172a' }
                }
            }
        }
        
        // Auth Check
        const token = localStorage.getItem('careloop_token');
        if(!token) {
            window.location.href = 'index.php';
        }
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
                <a href="dashboard.php" class="flex items-center px-4 py-3 text-primary bg-sky-50 rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-users w-6 text-center mr-2"></i> Patients View
                </a>
                <a href="requests.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-primary rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-user-plus w-6 text-center mr-2"></i> Requests
                </a>
                <a href="appointments.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-primary rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-calendar-check w-6 text-center mr-2"></i> Appointments
                </a>
                <a href="chat.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-primary rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-comments w-6 text-center mr-2"></i> Messages
                </a>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center px-4 py-3 cursor-pointer hover:bg-gray-50 rounded-xl transition-colors" onclick="logout()">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold mr-3" id="doctorInitials">D</div>
                <div>
                    <p class="text-sm font-medium text-gray-800" id="doctorNameLabel">Dr. Loading...</p>
                    <p class="text-xs text-gray-400">Sign Out</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-8 z-0">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Patient Directory</h2>
                <p class="text-sm text-gray-500">Monitor your patients remotely</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <button class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-bell"></i>
                </button>
            </div>
        </header>
        
        <!-- Content Scrollable -->
        <main class="flex-1 overflow-y-auto p-8 relative">
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Total Patients</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2" id="totalPatientsCount">-</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-user-injured"></i>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Critical Alerts</p>
                        <h3 class="text-3xl font-bold text-red-500 mt-2" id="criticalPatientsCount">-</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-red-100 text-red-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Appts Today</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">0</h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Patient List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">My Patients</h3>
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Search patients..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary w-64 bg-white transition-all">
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white">
                                <th class="py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">Patient</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">Diagnosis</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">Latest Vitals</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100">Status</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody" class="divide-y divide-gray-100">
                            <!-- Populated via JS -->
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">Loading patients...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Init user details
        const docName = localStorage.getItem('careloop_user_name') || 'Doctor';
        document.getElementById('doctorNameLabel').textContent = docName;
        document.getElementById('doctorInitials').textContent = docName.charAt(0).toUpperCase();

        function logout() {
            localStorage.clear();
            window.location.href = 'index.php';
        }

        async function fetchPatients() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/patients`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    const patients = await response.json();
                    renderPatients(patients);
                } else if(response.status === 401) {
                    logout();
                } else {
                    console.error('Failed to fetch patients');
                }
            } catch (err) {
                console.error("Error fetching patients", err);
            }
        }

        function renderPatients(patients) {
            const tbody = document.getElementById('patientsTableBody');
            document.getElementById('totalPatientsCount').textContent = patients.length;
            
            let criticalCount = 0;
            
            if(patients.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-gray-400">No patients assigned yet.</td></tr>';
                return;
            }

            tbody.innerHTML = patients.map(p => {
                const isCritical = p.status === 'Critical';
                if(isCritical) criticalCount++;
                
                const statusBadge = isCritical 
                    ? '<span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold ring-1 ring-red-200">Critical</span>'
                    : '<span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold ring-1 ring-green-200">Stable</span>';
                    
                const trClass = "hover:bg-gray-50/80 transition-colors group";
                
                return `
                <tr class="${trClass}">
                    <td class="py-4 px-6">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary to-secondary text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                ${p.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="ml-4">
                                <div class="font-semibold text-gray-800 group-hover:text-primary transition-colors">${p.name}</div>
                                <div class="text-xs text-gray-500">ID: PT-${1000 + p.id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-600">${p.condition}</td>
                    <td class="py-4 px-6">
                        <div class="text-sm text-gray-800"><i class="fa-solid fa-heart-pulse text-red-400 w-4"></i> ${p.latest_hr} bpm</div>
                        <div class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-tint text-blue-400 w-4"></i> BP: ${p.latest_bp}</div>
                    </td>
                    <td class="py-4 px-6">${statusBadge}</td>
                    <td class="py-4 px-6 text-right">
                        <a href="patient.php?id=${p.id}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-400 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                `;
            }).join('');
            
            document.getElementById('criticalPatientsCount').textContent = criticalCount;
        }

        // Alerts Logic
        let allAlerts = [];
        async function fetchAlerts() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/alerts/doctor`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    allAlerts = await response.json();
                    updateAlertsUI();
                }
            } catch(e) { console.error("Error fetching alerts", e); }
        }

        function updateAlertsUI() {
            const unreadCount = allAlerts.filter(a => !a.is_read).length;
            const bell = document.querySelector('.fa-bell');
            if (unreadCount > 0) {
                bell.parentElement.innerHTML = `<i class="fa-solid fa-bell text-red-500 animate-pulse"></i><span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold border-2 border-white">${unreadCount}</span>`;
            } else {
                bell.parentElement.innerHTML = `<i class="fa-solid fa-bell text-gray-400"></i>`;
            }
            
            // Sync with Stats
            const existingCritical = parseInt(document.getElementById('criticalPatientsCount').textContent) || 0;
            document.getElementById('criticalPatientsCount').textContent = existingCritical + unreadCount;
        }

        async function markAlertRead(id) {
            try {
                await fetch(`${API_BASE_URL}/api/alerts/${id}/read`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                fetchAlerts();
            } catch(e) {}
        }

        // Fetch on load
        async function init() {
            await fetchPatients();
            await fetchAlerts();
        }
        init();
        
        // Poll for alerts every 10 seconds
        setInterval(fetchAlerts, 10000);
    </script>
</body>
</html>
