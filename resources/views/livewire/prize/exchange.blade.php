<div>
    <div class="space-y-4 flex items-center gap-4 flex-col bg-white p-4 rounded-xl border shadow">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-semibold">Tukar Kode Hadiah</h2>
        </div>

        <div class="flex flex-col gap-4 w-full">
            <form wire:submit.prevent="exchange" class="flex flex-col items-center gap-4">
                <flux:input label="" autocomplete="off" placeholder="Kode Hadiah" type="text" wire:model="code" />
                <flux:button type="submit" variant="primary" class="w-full">Tukar</flux:button>
            </form>
        </div>
    </div>
</div>