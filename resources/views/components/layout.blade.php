<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#VANLIFE</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-brand-cream text-brand-dark flex flex-col min-h-screen m-0 p-0">
    
    <!-- Header Navigation -->
    <header class="flex justify-between items-center p-6 max-w-4xl mx-auto w-full">
        <a href="/" class="text-xl font-black uppercase tracking-tighter">#VANLIFE</a>
        <nav class="flex gap-4 font-semibold text-sm items-center text-gray-700">
            <a href="#" class="hover:text-brand-dark hover:underline">Host</a>
            <a href="/about" class="hover:text-brand-dark hover:underline">About</a>
            <a href="/vans" class="hover:text-brand-dark hover:underline">Vans</a>
            <a href="/login" class="hover:text-brand-dark">
                <!-- User Profile Icon Placeholder -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col max-w-4xl mx-auto w-full items-center">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-brand-dark text-brand-gray text-center p-6 text-sm font-semibold mt-auto w-full">
        &copy; 2022 #VANLIFE
    </footer>

</body>
</html>
