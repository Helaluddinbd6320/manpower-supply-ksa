{{-- resources/views/filament/forms/components/worker-photo-checkbox-list.blade.php --}}
<div>
    @if($workers->isEmpty())
        <p class="text-sm text-gray-500">No matching available workers found for this category right now.</p>
    @else
        <div
            x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }"
            class="grid grid-cols-1 sm:grid-cols-2 gap-3"
        >
            @foreach($workers as $worker)
                <label class="flex items-center gap-3 p-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 dark:border-white/10">
                    <input
                        type="checkbox"
                        value="{{ $worker->id }}"
                        x-model="state"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    >

                    <img
                        src="{{ $worker->photo_url ?? asset('images/placeholder-avatar.png') }}"
                        onerror="this.src='{{ asset('images/placeholder-avatar.png') }}'"
                        class="w-10 h-10 rounded-full object-cover shrink-0"
                        alt="{{ $worker->name }}"
                    >

                    <span class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ $worker->worker_id }} — {{ $worker->name }}
                    </span>
                </label>
            @endforeach
        </div>
    @endif
</div>