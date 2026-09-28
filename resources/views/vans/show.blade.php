<x-layout>
    <div class="w-full p-8">
        <a href="/vans" class="inline-flex items-center text-gray-700 hover:text-black mb-8 font-medium">
            &larr; <span class="ml-2 underline">Back to all vans</span>
        </a>
        
        <img src="{{ $van->images->first()?->image_path ?? 'https://placehold.co/800x500' }}" alt="{{ $van->name }}" class="w-full h-96 object-cover rounded-lg mb-8">
        
        @php
            $tagColor = match(strtolower($van->tag ?? 'simple')) {
                'simple' => 'bg-tag-simple',
                'rugged' => 'bg-tag-rugged',
                'luxury' => 'bg-tag-luxury',
                default => 'bg-tag-simple',
            };
        @endphp
        <span class="{{ $tagColor }} text-white text-sm font-semibold px-6 py-2 rounded capitalize mb-4 inline-block">
            {{ $van->tag ?? 'Simple' }}
        </span>
        
        <h1 class="text-4xl font-bold mb-4">{{ $van->name }}</h1>
        <p class="text-2xl font-bold mb-6">${{ $van->price_per_day }}<span class="text-lg font-normal text-gray-600">/day</span></p>
        
        <p class="text-gray-800 font-medium leading-relaxed mb-8">
            {{ $van->description }}
        </p>
        
        <button class="bg-brand-orange text-white font-bold py-4 px-8 rounded w-full shadow-lg hover:bg-orange-500 transition-colors">
            Rent this van
        </button>
    </div>
</x-layout>
