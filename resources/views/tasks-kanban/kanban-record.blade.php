<div
    id="{{ $record->id }}"
    wire:click="recordClicked('{{ $record->id }}', {{ @json_encode($record) }})"
    class="px-4 py-2 font-medium text-gray-600 transition bg-white rounded-lg record dark:bg-gray-700 cursor-grab dark:text-gray-200"
    @if($record->just_updated)
        x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        "
    @endif
>
    <div class="flex justify-between">
        <div>
            {{ $record->title }}

            @if ($record->urgent)
                <x-heroicon-s-star class="inline-block w-4 h-4 mb-[10px] text-yellow-500"/>
            @endif
        </div>

        <div class="text-xs text-right text-gray-400">{{ $record->owner }}</div>
    </div>

    <div class="pl-2 mt-2 mb-2 text-xs text-gray-400 border-l-4">
        {{ $record->description }}
    </div>

    <div class="flex -space-x-3 hover:-space-x-1">
        @foreach($record->team as $member)
            <div class="w-8 h-8 transition-all bg-gray-200 border-2 border-white rounded-full"></div>
        @endforeach
    </div>

    <div class="relative mt-2">
        <div class="absolute h-1 rounded-full bg-sky-500" style="width: {{ $record->progress }}%"></div>
        <div class="h-1 bg-gray-200 rounded-full"></div>        
    </div>
</div>
