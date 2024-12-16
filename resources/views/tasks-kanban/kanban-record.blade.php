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

<div style="display: flex; justify-content: space-between;">
    <div>
        @if ($record->urgent)
            <x-heroicon-s-star style="width: 20px; height: 20px; color: #eab304; margin-bottom:8px"/>
        @endif
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            {{-- Note: nanti bikin agar bisa ditekan sekali, maka name akan hilang hanya memunculkan color namun apabila ditekan sekali lagi akan kembali seperti semula --}}
            @foreach ($record->labels as $label)
                <div style="background-color: {{ $label->color ?? ' ' }}; font-weight: bold; font-size: 11px; padding: 2px 5px; border-radius: 5px; margin-bottom: 8px; color: white;">
                    {{ $label->name ?? ' ' }}
                </div>
            @endforeach
        </div>
    </div>
        <div class="text-xs text-right text-gray-400">{{ $record->user->name }}</div>
    </div>
    {{ $record->title }}
    <div class="text-xs text-gray-400" style="padding-left: 8px; margin-bottom: 12px; border-left-width: 2px; margin-top: 8px;">
        {{ $record->description }}
    </div>
    {{-- Note: nanti bikin diatas checklist model team dan terlihan circle profile img pengguna yg di assign hrd ke task --}}
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 6px; justify-content: center;  align-items: center; ">
            <x-heroicon-o-clock style="width: 15px; height: 15px;"/>
            <p>Dec 18</p>
    @if($record->checklists->isNotEmpty())
    <div style="background-color: #4d4b4b;  width: 45px; height: 25px; border-radius: 3px; display: flex; justify-content: space-evenly; align-items: center;">
        <x-heroicon-o-check style="width: 15px; height: 15px; color: #fff;"/>
        <p style="font-size: 12px;  color: #fff; padding-right: 5px;">
            {{ $record->checklists->where('user_id', $record->user->id)->where('is_done', true)->count() }}/{{ $record->checklists->where('user_id', $record->user->id)->count() }}
        </p>
    </div>
    @endif
        </div>
    <div style="display: flex; justify-content: center; align-items: center;">
        @foreach($record->users as $member)
            <div style="display: flex; justify-content: center; align-items: center; border: 1px solid; border-radius: 100%; width: 32px; height: 32px; transition: opacity 1s linear;"><p style="font-size: 15px; text-align: center;">{{ substr($record->user->name, 0, 2) }}</p></div>
        @endforeach
    </div>
</div>
</div>
