(function () {
    window.masterDataV2 = window.masterDataV2 || {};

    const files = [
        'castes.js',
        'districts.js',
        'assemblies.js',
        'blocks.js',
        'gps.js',
        'ulbs.js',
        'ulb_wards.js',
        'rural_urban.js',
        'dynamic-form.js'
    ];

    const basePath = '/js/master-data/';
    let loaded = 0;

    files.forEach(file => {
        const script = document.createElement('script');
        script.src = basePath + file + '?v=' + Date.now();
        script.onload = () => {
            loaded++;
            if (loaded === files.length) {
                window.dispatchEvent(new Event('masterdata:ready'));
            }
        };
        document.head.appendChild(script);
    });
})();
