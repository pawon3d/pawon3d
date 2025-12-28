@props(['wireModel' => '', 'placeholder' => '0', 'class' => ''])

<div x-data="{
    rawValue: @entangle($wireModel).live,
    displayValue: '',
    init() {
        this.displayValue = this.formatCurrency(this.rawValue || 0);
        this.$watch('rawValue', value => {
            if (value === '' || value === null) {
                this.displayValue = '';
            } else {
                this.displayValue = this.formatCurrency(value);
            }
        });
    },
    formatCurrency(value) {
        if (value === '' || value === null) return '';
        const num = parseFloat(value) || 0;
        return num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },
    handleInput(event) {
        let input = event.target.value;
        // Remove all non-numeric characters except dots and commas
        input = input.replace(/[^\d.,-]/g, '');
        // Remove dots (thousand separators)
        input = input.replace(/\./g, '');
        // Replace comma with dot for decimal
        input = input.replace(/,/g, '.');
        
        // Convert to number
        const numValue = parseFloat(input) || 0;
        
        // Update the raw value (this syncs with Livewire)
        this.rawValue = numValue;
    }
}" class="relative">
    <input 
        type="text" 
        x-model="displayValue"
        @input="handleInput"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => $class]) }}
    />
</div>
