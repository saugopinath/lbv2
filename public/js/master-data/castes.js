(function () {
    function initCasteCertificate() {
        const casteSelect = document.querySelector('select[name="castes"]');
        const certInput  = document.querySelector('div[name="cas_cer_no"]');

        if (!casteSelect || !certInput) return;

        const label = certInput.previousElementSibling;

        const handleCaste = () => {
            const val = casteSelect.value;

            if (["SC", "ST", "OBC"].includes(val)) {
                // SHOW
                certInput.classList.remove("hidden");
                label?.classList.remove("hidden");

                // required ADD
                certInput.setAttribute("required", "required");
            } else {
                // HIDE
                certInput.classList.add("hidden");
                label?.classList.add("hidden");

                // required REMOVE
                certInput.removeAttribute("required");

                // clear value + error
                certInput.value = "";
            }
        };

        if (!casteSelect.dataset.casteBound) {
            casteSelect.dataset.casteBound = "1";
            casteSelect.addEventListener("change", handleCaste);
        }

        // initial run
        handleCaste();
    }

    document.addEventListener("DOMContentLoaded", initCasteCertificate);

    // Livewire support
    document.addEventListener("livewire:load", () => {
        if (window.Livewire) {
            Livewire.hook("message.processed", () => {
                initCasteCertificate();
            });
        }
    });

    // fallback observer
    const observer = new MutationObserver(() => {
        initCasteCertificate();
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();
