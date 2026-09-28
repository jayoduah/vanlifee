<x-layout>
    <div class="w-full p-8">
        <h1 class="text-3xl font-black mb-6">Explore our van options</h1>
        
        <!-- Filters -->
        <div class="flex gap-4 mb-10 items-center text-sm">
            <a href="?tag=simple" class="bg-[#ffead1] text-gray-700 px-6 py-2 rounded font-semibold hover:bg-tag-simple hover:text-white transition-colors">Simple</a>
            <a href="?tag=luxury" class="bg-[#ffead1] text-gray-700 px-6 py-2 rounded font-semibold hover:bg-tag-luxury hover:text-white transition-colors">Luxury</a>
            <a href="?tag=rugged" class="bg-[#ffead1] text-gray-700 px-6 py-2 rounded font-semibold hover:bg-tag-rugged hover:text-white transition-colors">Rugged</a>
            <a href="/vans" class="ml-auto text-gray-600 underline font-medium hover:text-gray-900">Clear filters</a>
        </div>

        <!-- Vans Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            @foreach($vans as $van)
                <a href="/vans/{{ $van->id }}" class="block group">
                    <img src="{{ $van->images->first()?->image_path ?? 'https://placehold.co/400x400' }}" alt="{{ $van->name }}" class="w-full h-64 object-cover rounded-lg mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <h2 class="text-xl font-bold">{{ $van->name }}</h2>
                        <div class="text-right">
                            <p class="text-xl font-bold">${{ $van->price_per_day }}</p>
                            <p class="text-sm text-gray-600">/day</p>
                        </div>
                    </div>
                    
                    @php
                        $tagColor = match(strtolower($van->tag ?? 'simple')) {
                            'simple' => 'bg-tag-simple',
                            'rugged' => 'bg-tag-rugged',
                            'luxury' => 'bg-tag-luxury',
                            default => 'bg-tag-simple',
                        };
                    @endphp
                    <span class="{{ $tagColor }} text-white text-sm font-semibold px-4 py-1 rounded capitalize">
                        {{ $van->tag ?? 'Simple' }}
                    </span>
                </a>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $vans->links() }}
        </div>
    </div>
</x-layout>
