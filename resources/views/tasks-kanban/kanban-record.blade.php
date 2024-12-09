{{-- @extends('tasks-kanban.main') --}}

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

    <div style="display: flex; justify-content:space-between;">
        <div>
            @if ($record->urgent)
            <x-heroicon-s-star style="width: 20px; height: 20px; color: #eab304; margin-bottom:8px"/>
        @endif
        {{-- <div style="margin-bottom: 8px; background-color: #d33832; font-weight: bold; font-size: 11px; width: auto; padding: 2px 5px; border-radius: 5px;">
            Important
        </div> --}}

            <div style="margin-bottom: 8px; background-color:  {{ $record->labels->first()?->color ?? ' '}}; font-weight: bold; font-size: 11px; width: auto; padding: 2px 5px; border-radius: 5px;">
                {{ $record->labels->first()?->name ?? ' '}}
            </div>
        </div>

        <div class="text-xs text-right text-gray-400">{{ $record->user->name }}</div>
    </div>
    {{ $record->title }}

    <div class="text-xs text-gray-400" style="padding-left: 8px; margin-bottom: 8px; border-left-width: 2px; margin-top: 8px;">
        {{ $record->description }}
    </div>
</div>

