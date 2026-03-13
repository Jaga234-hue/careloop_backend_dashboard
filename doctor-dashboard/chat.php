<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Careloop+</title>
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
        
        let currentChatUserId = null;
        let chatPollInterval = null;
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
                <a href="appointments.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-primary rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-calendar-check w-6 text-center mr-2"></i> Appointments
                </a>
                <a href="#" class="flex items-center px-4 py-3 text-primary bg-sky-50 rounded-xl font-medium transition-colors">
                    <i class="fa-solid fa-comments w-6 text-center mr-2"></i> Messages
                </a>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-100 flex items-center">
            <a href="dashboard.php" class="text-sm text-gray-500 hover:text-gray-800"><i class="fa-solid fa-arrow-left mr-2"></i> Back</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Chat List Sidebar -->
        <div class="w-1/3 max-w-sm bg-white border-r border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-lg mb-4">Conversations</h2>
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search patients..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-transparent focus:border-primary focus:bg-white rounded-lg text-sm outline-none transition-all">
                </div>
            </div>
            <div class="flex-1 overflow-y-auto" id="patientsList">
                <div class="p-8 text-center text-gray-400 text-sm">Loading contacts...</div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-gray-50/50">
            <!-- Chat Header -->
            <div class="h-20 bg-white border-b border-gray-100 flex items-center px-6 shadow-sm z-10 hidden" id="chatHeader">
                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold mr-4" id="chatAvatar">
                    P
                </div>
                <div>
                    <h3 class="font-bold text-gray-800" id="chatName">Patient Name</h3>
                    <p class="text-xs text-green-500">Online</p>
                </div>
            </div>
            
            <div class="h-20 bg-white border-b border-gray-100 flex items-center justify-center px-6 shadow-sm" id="emptyChatHeader">
                <p class="text-gray-400">Select a conversation</p>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chatBox">
                <!-- Messages populate here -->
            </div>

            <!-- Chat Input -->
            <div class="p-4 bg-white border-t border-gray-200 hidden" id="chatInputArea">
                <form id="sendMessageForm" class="flex gap-2">
                    <button type="button" class="w-10 h-10 rounded-full text-gray-400 hover:text-primary hover:bg-sky-50 transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>
                    <input type="text" id="messageInput" placeholder="Type your message..." class="flex-1 border border-gray-200 rounded-full px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none" autocomplete="off" required>
                    <button type="submit" class="w-10 h-10 rounded-full bg-primary text-white hover:bg-secondary transition-colors flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        async function loadContacts() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/patients`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    const patients = await response.json();
                    renderContacts(patients);
                }
            } catch(e) { console.error("Error loading contacts", e); }
        }

        function renderContacts(patients) {
            const list = document.getElementById('patientsList');
            if(patients.length === 0) {
                list.innerHTML = '<div class="p-8 text-center text-gray-400 text-sm">No patients available structure.</div>';
                return;
            }

            list.innerHTML = patients.map(p => `
                <div class="p-4 border-b border-gray-50 flex items-center cursor-pointer hover:bg-blue-50 transition-colors" onclick="openChat('${p.id}', '${p.name}')">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-200 to-gray-300 text-gray-600 flex items-center justify-center font-bold">
                            ${p.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="ml-3 flex-1 overflow-hidden">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="font-medium text-gray-800 text-sm truncate w-32">${p.name}</h4>
                            <span class="text-[10px] text-gray-400">Just now</span>
                        </div>
                        <p class="text-xs text-gray-500 truncate">Tap to view messages...</p>
                    </div>
                </div>
            `).join('');
        }

        function openChat(patientId, patientName) {
            currentChatUserId = parseInt(patientId);
            
            document.getElementById('emptyChatHeader').classList.add('hidden');
            document.getElementById('chatHeader').classList.remove('hidden');
            document.getElementById('chatInputArea').classList.remove('hidden');
            
            document.getElementById('chatName').textContent = patientName;
            document.getElementById('chatAvatar').textContent = patientName.charAt(0).toUpperCase();
            
            if(chatPollInterval) clearInterval(chatPollInterval);
            
            fetchMessages(); // initial load
            chatPollInterval = setInterval(fetchMessages, 3000); // Poll every 3 seconds
        }

        async function fetchMessages() {
            if(!currentChatUserId) return;
            try {
                const response = await fetch(`${API_BASE_URL}/api/messages/${currentChatUserId}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(response.ok) {
                    const messages = await response.json();
                    renderMessages(messages);
                }
            } catch(e) { console.error("Error fetching messages", e); }
        }

        function renderMessages(messages) {
            const box = document.getElementById('chatBox');
            
            if(messages.length === 0) {
                box.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="fa-regular fa-comments text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">No messages yet. Say hello!</p>
                    </div>
                `;
                return;
            }

            // Simple diff to avoid re-rendering entire list constantly (for demo purposes we overwrite)
            // But we will check length briefly to auto-scroll only on new msg
            const isNewMessage = box.children.length < messages.length;

            box.innerHTML = messages.map(m => {
                const isMine = m.sender_id === doctorId;
                const time = new Date(m.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                if(isMine) {
                    return `
                    <div class="flex justify-end mb-4">
                        <div class="max-w-[70%] bg-primary text-white rounded-2xl rounded-tr-sm px-4 py-2 shadow-sm relative group">
                            <p class="text-sm">${m.content}</p>
                            <p class="text-[10px] text-sky-100 text-right mt-1">${time}</p>
                        </div>
                    </div>
                    `;
                } else {
                    return `
                    <div class="flex mb-4">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0 flex items-center justify-center text-gray-500 font-medium text-xs mr-2 mt-1">P</div>
                        <div class="max-w-[70%] bg-white text-gray-800 rounded-2xl rounded-tl-sm px-4 py-2 shadow-sm border border-gray-100">
                            <p class="text-sm">${m.content}</p>
                            <p class="text-[10px] text-gray-400 mt-1">${time}</p>
                        </div>
                    </div>
                    `;
                }
            }).join('');
            
            if(isNewMessage || box.scrollTop === 0) {
                box.scrollTop = box.scrollHeight;
            }
        }

        document.getElementById('sendMessageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if(!currentChatUserId) return;
            
            const input = document.getElementById('messageInput');
            const content = input.value.trim();
            if(!content) return;
            
            input.value = ''; // clear input
            
            try {
                await fetch(`${API_BASE_URL}/api/messages`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: currentChatUserId,
                        content: content
                    })
                });
                
                fetchMessages(); // force refresh immediately
            } catch(e) { console.error("Error sending message", e); }
        });

        // Init
        loadContacts();
    </script>
</body>
</html>
