<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile - Careloop+</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#0ea5e9', secondary: '#38bdf8', accent: '#f59e0b', dark: '#0f172a' }
                }
            }
        }
        
        const token = localStorage.getItem('careloop_token');
        if(!token) window.location.href = 'index.php';
        
        const urlParams = new URLSearchParams(window.location.search);
        const patientId = urlParams.get('id');
        if(!patientId) window.location.href = 'dashboard.php';
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
                <a href="#" class="flex items-center px-4 py-3 text-primary bg-sky-50 rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-user-injured w-6 text-center mr-2"></i> Patient Profile
                </a>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-100">
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto relative p-8">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-6xl mx-auto w-full space-y-6">
            
            <!-- Header Profile -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-primary to-secondary text-white flex items-center justify-center font-bold text-3xl shadow-sm" id="pAvatar">
                        P
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800" id="pName">Loading...</h2>
                        <p class="text-sm text-gray-500" id="pDetails">Age: -- | Blood: -- | ID: PT-${patientId}</p>
                        <div class="mt-2 text-sm font-medium text-gray-700 bg-gray-100 px-3 py-1 rounded-full inline-block" id="pCondition">
                            Condition: Loading...
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm flex items-center shadow-sm">
                        <i class="fa-solid fa-video text-primary mr-2"></i> Video Call
                    </button>
                    <button class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition-colors font-medium text-sm flex items-center shadow-sm">
                        <i class="fa-solid fa-comment mr-2"></i> Send Message
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Vitals Chart Column -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-gray-800">Vitals Trend (Heart Rate & Oxygen)</h3>
                        </div>
                        <div class="h-72">
                            <canvas id="vitalsChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Latest Readings Log</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="text-gray-400 border-b border-gray-100">
                                        <th class="pb-3 font-semibold">Date & Time</th>
                                        <th class="pb-3 font-semibold">Blood Pressure</th>
                                        <th class="pb-3 font-semibold">Heart Rate</th>
                                        <th class="pb-3 font-semibold">Oxygen (SpO2)</th>
                                        <th class="pb-3 font-semibold">Temp</th>
                                    </tr>
                                </thead>
                                <tbody id="vitalsHistoryTable" class="divide-y divide-gray-50">
                                    <!-- populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Prescriptions Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-800">Prescriptions</h3>
                        </div>
                        
                        <div id="medicinesList" class="space-y-3 mb-6">
                            <p class="text-sm text-gray-400">Loading medicines...</p>
                        </div>
                        
                        <hr class="border-gray-100 mb-4">
                        
                        <h4 class="font-semibold text-gray-700 text-sm mb-3">Add New Medicine</h4>
                        <form id="addMedForm" class="space-y-3">
                            <input type="text" id="medName" placeholder="Medicine Name (e.g. Aspirin)" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" id="medDosage" placeholder="Dosage (50mg)" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                <input type="time" id="medTime" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none text-gray-500">
                            </div>
                            <button type="submit" class="w-full py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors text-sm font-medium">Add Prescription</button>
                        </form>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Allergies & Notes</h3>
                        <p class="text-sm text-gray-600 bg-amber-50 p-3 rounded-lg border border-amber-100" id="pAllergies">Loading...</p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        async function fetchPatientData() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/reports/${patientId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    const data = await response.json();
                    renderPatientData(data);
                }
            } catch (err) {
                console.error("Error fetching patient profile", err);
            }
        }

        function renderPatientData(data) {
            document.getElementById('pAvatar').textContent = data.name.charAt(0).toUpperCase();
            document.getElementById('pName').textContent = data.name;
            document.getElementById('pDetails').textContent = `Age: ${data.age} | Blood: ${data.blood_group} | ID: PT-${data.patient_id}`;
            document.getElementById('pCondition').textContent = `Condition: ${data.latest_diagnosis}`;
            document.getElementById('pAllergies').textContent = data.allergies[0] || "No known allergies.";
            
            renderMedicines(data.prescriptions);
            renderVitalsHistory(data.recent_vitals);
            renderChart(data.recent_vitals);
        }

        function renderMedicines(meds) {
            const list = document.getElementById('medicinesList');
            if(meds.length === 0) {
                list.innerHTML = '<p class="text-sm text-gray-400">No active prescriptions.</p>';
                return;
            }
            list.innerHTML = meds.map(m => `
                <div class="p-3 border border-gray-100 rounded-xl bg-gray-50 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">${m.medicine}</p>
                        <p class="text-xs text-gray-500">${m.dosage}</p>
                    </div>
                    <div class="text-xs font-medium text-primary bg-sky-100 px-2 py-1 rounded-md">
                        <i class="fa-regular fa-clock mr-1"></i> ${m.frequency}
                    </div>
                </div>
            `).join('');
        }

        function renderVitalsHistory(vitals) {
            const table = document.getElementById('vitalsHistoryTable');
            if(vitals.length === 0) {
                table.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-gray-400">No vitals recorded yet.</td></tr>';
                return;
            }
            
            table.innerHTML = vitals.map(v => {
                const date = new Date(v.date).toLocaleString([], {month:'short', day:'numeric', hour:'2-digit', minute:'2-digit'});
                return `
                <tr>
                    <td class="py-3 text-gray-500">${date}</td>
                    <td class="py-3 font-medium text-gray-700">${v.bp}</td>
                    <td class="py-3 font-medium ${v.pulse > 100 || v.pulse < 60 ? 'text-red-500' : 'text-gray-700'}">${v.pulse} bpm</td>
                    <td class="py-3 font-medium ${v.oxygen < 95 ? 'text-red-500' : 'text-green-600'}">${v.oxygen}%</td>
                    <td class="py-3 text-gray-600">${v.temperature}°F</td>
                </tr>
                `;
            }).join('');
        }

        function renderChart(vitalsData) {
            if(vitalsData.length === 0) return;
            // reverse them so chronological is left-to-right
            const sorted = [...vitalsData].reverse();
            
            const labels = sorted.map(v => {
                const d = new Date(v.date);
                return `${d.getMonth()+1}/${d.getDate()} ${d.getHours()}:${d.getMinutes().toString().padStart(2,'0')}`;
            });
            const hrData = sorted.map(v => v.pulse);
            const oxData = sorted.map(v => v.oxygen);
            
            const ctx = document.getElementById('vitalsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Heart Rate (bpm)',
                            data: hrData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Oxygen (SpO2 %)',
                            data: oxData,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { beginAtZero: false }
                    }
                }
            });
        }

        // Add Medicine Form
        document.getElementById('addMedForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const name = document.getElementById('medName').value;
            const dosage = document.getElementById('medDosage').value;
            const time = document.getElementById('medTime').value;
            
            try {
                const response = await fetch(`${API_BASE_URL}/api/medicines?patient_id=${patientId}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        dosage: dosage,
                        time_to_take: time
                    })
                });
                
                if(response.ok) {
                    document.getElementById('addMedForm').reset();
                    fetchPatientData(); // Refresh list
                } else {
                    alert("Failed to add prescription.");
                }
            } catch(e) {
                console.error(e);
            }
        });

        // Init
        fetchPatientData();
    </script>
</body>
</html>
