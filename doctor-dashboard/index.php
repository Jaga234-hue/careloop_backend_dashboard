<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careloop+ Doctor Dashboard - Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#0ea5e9', // Sky 500
                        secondary: '#38bdf8', // Sky 400
                        accent: '#f59e0b', // Amber 500
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f0fdfa; /* Teal 50 */
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Background decorations -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 rounded-full bg-primary/20 blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 rounded-full bg-accent/20 blur-3xl"></div>

    <div class="glass-panel w-full max-w-md p-8 rounded-2xl z-10 mx-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">CARELOOP<span class="text-primary">+</span></h1>
            <p class="text-gray-500 mt-2">Doctor Portal</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" id="email" name="email" required 
                        class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 px-4 outline-none border transition-colors" 
                        placeholder="doctor@hospital.com">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" required 
                        class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 px-4 outline-none border transition-colors" 
                        placeholder="••••••••">
                </div>
            </div>

            <div id="errorMessage" class="hidden text-red-500 text-sm font-medium text-center"></div>

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors hover:shadow-lg hover:-translate-y-0.5 transform duration-150">
                    Sign In
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="register.php" class="text-sm text-primary hover:underline">Register New Doctor Account</a>
            </div>
        </form>
    </div>

    <!-- API Logic -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            
            errorDiv.classList.add('hidden');
            
            try {
                // Formatting payload as x-www-form-urlencoded matching OAuth2PasswordRequestForm
                const formData = new URLSearchParams();
                formData.append('username', email);
                formData.append('password', password);

                const response = await fetch(`${API_BASE_URL}/api/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData.toString()
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    if(data.role !== 'doctor') {
                       errorDiv.textContent = 'Account exists but is not registered as a doctor.';
                       errorDiv.classList.remove('hidden');
                       return;
                    }
                    // Save token and user info to localStorage
                    localStorage.setItem('careloop_token', data.access_token);
                    localStorage.setItem('careloop_user_id', data.user_id);
                    localStorage.setItem('careloop_user_name', data.name);
                    
                    // Redirect to dashboard
                    window.location.href = 'dashboard.php';
                } else {
                    errorDiv.textContent = data.detail || 'Login failed. Please check your credentials.';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Login error:', error);
                errorDiv.textContent = 'Unable to connect to the server. Please try again later.';
                errorDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
