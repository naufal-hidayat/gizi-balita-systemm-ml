{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - Sistem Prediksi Gizi Balita</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(209, 213, 219, 0.3);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
            <g fill="none" fill-rule="evenodd">
                <g fill="#ffffff" fill-opacity="0.4">
                    <circle cx="7" cy="7" r="1"/>
                    <circle cx="27" cy="7" r="1"/>
                    <circle cx="47" cy="7" r="1"/>
                    <circle cx="7" cy="27" r="1"/>
                    <circle cx="27" cy="27" r="1"/>
                    <circle cx="47" cy="27" r="1"/>
                    <circle cx="7" cy="47" r="1"/>
                    <circle cx="27" cy="47" r="1"/>
                    <circle cx="47" cy="47" r="1"/>
                </g>
            </g>
        </svg>
    </div>

    <!-- Main Content -->
    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-white rounded-2xl shadow-xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Sistem Prediksi Gizi Balita</h1>
            {{-- <p class="text-white/80 text-sm">Fuzzy-AHP untuk Pencegahan Stunting di Pedesaan</p> --}}
        </div>

        <!-- Auth Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white/60 text-sm">
            <p>&copy; {{ date('Y') }} Sistem Prediksi Gizi Balita. All rights reserved.</p>
        </div>
    </div>
</body>
</html>