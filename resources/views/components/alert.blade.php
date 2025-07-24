@push('scripts')
<script>
    new Promise(function(resolve) {
        if (typeof Swal !== 'undefined') {
            resolve();
        } else {
            const checkSwal = function() {
                if (typeof Swal !== 'undefined') {
                    resolve();
                } else {
                    setTimeout(checkSwal, 100);
                }
            };
            setTimeout(checkSwal, 100);
        }
    }).then(function() {
        Swal.fire({
            icon: '{{ $icon }}',
            title: '{{ $title }}',
            text: '{{ $text }}',
        });
    });
</script>
@endpush