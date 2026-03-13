<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careloop+ Doctor Registration</title>
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
                        primary: '#0ea5e9',
                        secondary: '#38bdf8',
                        accent: '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f0fdfa; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative py-12 px-4 sm:px-6 lg:px-8">
    <div class="absolute top-[-10%] right-[-10%] w-96 h-96 rounded-full bg-primary/20 blur-3xl"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-96 h-96 rounded-full bg-accent/20 blur-3xl"></div>

    <div class="glass-panel w-full max-w-md p-8 rounded-2xl z-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">CARELOOP<span class="text-primary">+</span></h1>
            <p class="text-gray-500 mt-2">Doctor Registration</p>
        </div>

        <form id="registerForm" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="name" name="name" required 
                    class="mt-1 focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-lg py-3 px-4 outline-none border" 
                    placeholder="Dr. John Doe">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="email" name="email" required 
                    class="mt-1 focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-lg py-3 px-4 outline-none border" 
                    placeholder="doctor@hospital.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required 
                    class="mt-1 focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-lg py-3 px-4 outline-none border" 
                    placeholder="••••••••">
            </div>

            <div id="errorMessage" class="hidden text-red-500 text-sm font-medium text-center"></div>
            <div id="successMessage" class="hidden text-green-500 text-sm font-medium text-center">Registration successful! Redirecting...</div>

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors hover:shadow-lg">
                    Register
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="text-sm text-primary hover:underline">Already have an account? Sign in</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            const successDiv = document.getElementById('successMessage');
            
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            
            try {
                const response = await fetch('http://192.168.1.20:8000/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        password: password,
                        role: 'doctor'
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    successDiv.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = 'index.php'; // Redirect to login
                    }, 2000);
                } else {
                    errorDiv.textContent = data.detail || 'Registration failed.';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Registration error:', error);
                errorDiv.textContent = 'Unable to connect to the server. Please try again later.';
                errorDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
