(function () {

    function toggleSpouseName(forceHide = false) {
        const maritalSelect = document.querySelector('select[name="mar_statu"]');
        const spouseInput   = document.querySelector('input[name="sfname"]');

        if (!maritalSelect || !spouseInput) return;

        const wrapper = spouseInput.closest('div'); // x-form.input wrapper
        const val = maritalSelect.value;

        // Page load / Un Married / empty হলে hide
        if (
            forceHide ||
            val === '' ||
            val === 'Un Married'
        ) {
            wrapper.classList.add('hidden');
            spouseInput.value = '';
            spouseInput.removeAttribute('required');
            return;
        }

        // Married / Widow / Divorcee / Widower হলে show
        wrapper.classList.remove('hidden');
        spouseInput.setAttribute('required', 'required');
    }

    // ✅ Page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleSpouseName(true);
    });

    // ✅ Change listener
    document.addEventListener('change', function (e) {
        if (e.target.name === 'mar_statu') {
            toggleSpouseName();
        }
    });

    // ✅ Livewire re-render support
    document.addEventListener('livewire:load', () => {
        if (window.Livewire) {
            Livewire.hook('message.processed', () => {
                toggleSpouseName();
            });
        }
    });

})();