<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TutorApp')</title> 
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Tùy chỉnh thanh cuộn */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    
    <div class="flex min-h-screen">
        @include('components.web.sidebar')

        <main class="flex-1 p-8 h-screen overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>

</html>