<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
</div>


<input type="text"
    class="w-full border rounded-lg block disabled:shadow-none dark:shadow-none appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] pl-3 pr-3 bg-white dark:bg-white/10 dark:disabled:bg-white/[7%] text-zinc-700 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400/70 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500 shadow-xs border-zinc-200 border-b-zinc-300/80 disabled:border-b-zinc-200 dark:border-white/10 dark:disabled:border-white/5"
    x-ref="datepicker" x-init="
                        picker = new Pikaday({
                        field: $refs.datepicker,
                        format: 'DD/MM/YYYY',
                        toString(date, format) {
                            const day = String(date.getDate()).padStart(2, 0);
                            const month = String(date.getMonth() + 1).padStart(2, 0);
                            const year = date.getFullYear();
                            return `${day}/${month}/${year}`;
                        },
                        onSelect: function() {
                            console.log(moment(this.getDate()).format('YYYY-MM-DD'));
                            }
                        });
                        " wire:model.defer="expiry_date" id="datepicker" readonly />