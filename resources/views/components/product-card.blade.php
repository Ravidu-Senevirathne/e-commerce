@props(['product'])

<div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
    <div class="relative">
        @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
        @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        @endif

        @if($product->featured)
        <div class="absolute top-0 right-0 bg-yellow-500 text-white px-2 py-1 m-2 rounded text-xs font-bold">
            Featured
        </div>
        @endif
    </div>

    <div class="p-4">
        <h3 class="font-bold text-lg mb-2 text-gray-900 truncate">{{ $product->name }}</h3>
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
        <div class="flex items-center justify-between">
            <span class="font-bold text-lg text-indigo-700">${{ number_format($product->price, 2) }}</span>
            <a href="{{ route('products.show', $product) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                View Details
            </a>
        </div>
    </div>
</div>
