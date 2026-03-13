<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Requests - Careloop+</title>
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
                <a href="requests.php" class="flex items-center px-4 py-3 text-primary bg-sky-50 rounded-xl font-medium transition-colors">
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
        
        <div class="p-4 border-t border-gray-100 flex items-center">
            <a href="dashboard.php" class="text-sm text-gray-500 hover:text-gray-800"><i class="fa-solid fa-arrow-left mr-2"></i> Back</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto relative p-8">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-sky-100/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto w-full">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Patient Requests</h2>
                    <p class="text-gray-500 mt-1">Accept or decline patients trying to connect.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Pending & Accepted Links</h3>
                </div>
                
                <div class="p-6 space-y-4" id="requestsContainer">
                    <div class="text-center py-8 text-gray-400">Loading requests...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadRequests() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/links/doctor`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                
                if (response.ok) {
                    const links = await response.json();
                    renderRequests(links);
                } else {
                    console.error("Failed to load requests");
                }
            } catch (err) {
                console.error("Error", err);
            }
        }

        function renderRequests(links) {
            const container = document.getElementById('requestsContainer');
            
            if (links.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                            <i class="fa-solid fa-inbox"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No connection requests yet.</p>
                    </div>
                `;
                return;
            }

            // Sort so pending is at the top
            links.sort((a,b) => {
                if (a.status === 'pending' && b.status !== 'pending') return -1;
                if (a.status !== 'pending' && b.status === 'pending') return 1;
                return new Date(b.created_at) - new Date(a.created_at);
            });

            container.innerHTML = links.map(link => {
                const date = new Date(link.created_at).toLocaleDateString();
                
                let actionButtons = '';
                let statusBadge = '';
                
                if (link.status === 'pending') {
                    statusBadge = '<span class="px-3 py-1 bg-amber-100 text-amber-600 border border-amber-200 rounded-full text-xs font-bold">Pending</span>';
                    actionButtons = `
                        <button onclick="updateStatus(${link.id}, 'accepted')" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-secondary transition-colors mr-2 shadow-sm">Accept</button>
                        <button onclick="updateStatus(${link.id}, 'rejected')" class="px-4 py-2 bg-white border border-gray-200 text-red-500 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors shadow-sm">Reject</button>
                    `;
                } else if (link.status === 'accepted') {
                    statusBadge = '<span class="px-3 py-1 bg-green-100 text-green-600 border border-green-200 rounded-full text-xs font-bold">Connected</span>';
                } else {
                    statusBadge = '<span class="px-3 py-1 bg-gray-100 text-gray-500 border border-gray-200 rounded-full text-xs font-bold">Rejected</span>';
                }

                return `
                <div class="flex items-center justify-between p-5 border border-gray-100 rounded-xl hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-tr from-sky-400 to-blue-500 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-sm mr-4">
                            ${link.patient_name.charAt(0)}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">${link.patient_name}</h4>
                            <div class="flex items-center mt-1">
                                ${statusBadge}
                                <span class="text-xs text-gray-400 ml-3"><i class="fa-regular fa-clock mr-1"></i> Requested on ${date}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        ${actionButtons}
                    </div>
                </div>
                `;
            }).join('');
        }

        async function updateStatus(linkId, newStatus) {
            try {
                const response = await fetch(`${API_BASE_URL}/api/links/${linkId}/status?status=${newStatus}`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (response.ok) {
                    loadRequests(); // refresh list
                }
            } catch(e) { console.error('Error updating status', e); }
        }

        // Init
        loadRequests();
    </script>
</body>
</html>
