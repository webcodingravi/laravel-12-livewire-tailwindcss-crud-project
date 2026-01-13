@if (session()->has('success'))
    <div class="px-8 py-3 bg-green-200 text-green-700 rounded">
        {{ session('success') }}
    </div>
@elseif (session()->has('error'))
    <div class="px-8 py-3 bg-red-200 text-red-700 rounded">
        {{ session('error') }}
    </div>
@endif
