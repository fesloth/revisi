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
    <!-- Existing content -->
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
    <div>
        <button
        style="border: none;"
        onclick="deleteTask({{ $record->id }})">
        <x-heroicon-o-x-mark style="width: 20px; height: 20px; color: #bb0f0f; margin-bottom: 8px;" />
    </button>
    </div>
        {{-- <div class="text-xs text-right text-gray-400">{{ $record->user->name }}</div> --}}
    </div>
    {{ $record->title }}
    <div class="text-xs text-gray-400" style="padding-left: 8px; margin-bottom: 12px; border-left-width: 2px; margin-top: 8px;">
        {{ $record->description }}
    </div>
    {{-- Note: nanti bikin diatas checklist model team dan terlihan circle profile img pengguna yg di assign hrd ke task --}}
    <div style="display: flex; justify-content: space-between; align-items: center;">
    <div style="background-color: #4d4b4b;  width: 45px; height: 25px; border-radius: 3px; display: flex; justify-content: space-evenly; align-items: center;">
        <x-heroicon-o-check style="width: 15px; height: 15px; color: #fff;"/>
        <p style="font-size: 12px;  color: #fff; padding-right: 5px;">
            {{ $record->checklists->where('is_done', true)->count() }}/{{ $record->checklists->where('user_id', $record->user->id)->count() }}
        </p>
    </div>
    <div class="flex -space-x-3 hover:-space-x-1">
        @foreach($record->users as $member)
            <div
                class="flex items-center justify-center w-8 h-8 transition-opacity duration-1000 border border-gray-300 rounded-full"
                aria-label="User: {{ $member->name }}">
                <p class="text-sm font-medium text-center">
                    {{ substr($member->name, 0, 2) }}
                </p>
            </div>
        @endforeach
    </div>
</div>
</div>

<script>
    function deleteTask(taskId) {
        if (confirm('Apakah Anda yakin ingin menghapus task ini?')) {
            // Kirim request menggunakan fetch atau AJAX
            fetch(`/tasks/${taskId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Untuk keamanan Laravel
                    'Accept': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    alert('Task berhasil dihapus!');
                    location.reload(); // Reload halaman setelah delete
                } else {
                    alert('Ini bukan task anda');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan.');
            });
        }
    }
</script>
