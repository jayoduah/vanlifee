<x-layout>
    <div class="w-full max-w-md mx-auto p-8 my-auto text-center flex flex-col justify-center h-[600px]">
        <h1 class="text-3xl font-bold mb-8">Sign in to your account</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST" class="flex flex-col gap-0 shadow-sm border border-gray-200 rounded-md overflow-hidden mb-6">
            @csrf
            <input type="email" name="email" placeholder="Email address" class="p-4 border-b border-gray-200 focus:outline-none focus:bg-gray-50" required>
            <input type="password" name="password" placeholder="Password" class="p-4 focus:outline-none focus:bg-gray-50" required>
            
            <!-- We visually separate the button from the inputs, unlike standard forms -->
        </form>
        
        <!-- We trigger the form submission via standard submit or JS if preferred, but placing it outside the visual box as per Figma is easier to just style normally: -->
        <button onclick="document.querySelector('form').submit()" class="bg-brand-orange text-white font-bold py-4 px-8 rounded w-full shadow-md hover:bg-orange-500 transition-colors mb-6">
            Sign in
        </button>
        
        <p class="font-medium text-gray-800">
            Don't have an account? <a href="/register" class="text-brand-orange hover:underline font-bold">Create one now</a>
        </p>
    </div>
</x-layout>
