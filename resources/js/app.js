import "./bootstrap";
// import '../../vendor/masmerise/livewire-toaster/resources/js';
import toastr from "toastr";
import Sortable from 'sortablejs';
import "toastr/build/toastr.min.css";

// Optional default settings
toastr.options = {
    positionClass: "toast-top-right",
    closeButton: true,
    progressBar: true,
};
window.Sortable = Sortable;
window.addEventListener('toastr', event => {
    console.log('Toastr Event Data:', event.detail); // Debug purpose

    const data = event.detail[0] || {}; // Livewire passes arguments in an array
    const type = data.type || 'info';
    const message = data.message || 'No message found';

    switch (type) {
        case 'success':
            toastr.success(message);
            break;
        case 'error':
            toastr.error(message);
            break;
        case 'info':
            toastr.info(message);
            break;
        case 'warning':
            toastr.warning(message);
            break;
    }
});