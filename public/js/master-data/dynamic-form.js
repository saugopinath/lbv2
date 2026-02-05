window.initMasterData = function () {
    // 1. Check data
    if (!window.masterDataV2 || !window.masterDataV2.districts) return;

    const md = window.masterDataV2;

    // 2. Class chara sora-sori Select field khunje ber kora
    const districtSelect = document.querySelector('select[name="district_id"]');

    // Jodi district select-ti page-e thake (mane Contact Details tab active)
    if (districtSelect) {
        // Find other fields relative to this component or document
        const root = districtSelect.closest("[wire\\:id]") || document;
        const assemblie = root.querySelector('select[name="assemblie"]');
        const urban = root.querySelector('select[name="rural_urban"]');
        const localbody = root.querySelector('select[name="blockurban"]');
        const gpward = root.querySelector('select[name="gpWard"]');
        // District Fill (Jodi ekhono load na hoye thake)
        if (
            !districtSelect.dataset.loaded ||
            districtSelect.options.length <= 1
        ) {
            fillSelect(districtSelect, md.districts);
            districtSelect.dataset.loaded = "1";
        }

        // Rural/Urban Fill
        if (urban && (!urban.dataset.loaded || urban.options.length <= 1)) {
            fillSelect(urban, [
                { id: 1, text: "Urban" },
                { id: 2, text: "Rural" },
            ]);
            urban.dataset.loaded = "1";
        }

        // --- Change Events ---
        districtSelect.onchange = () => {
            if (urban) urban.value = "";
            clearSelect(assemblie);
            clearSelect(localbody);
            clearSelect(gpward);
            if (md.assemblies) {
                fillSelect(
                    assemblie,
                    md.assemblies.filter(
                        (a) => a.district_code == districtSelect.value,
                    ),
                );
            }
            syncLivewire(districtSelect);
        };
        if (assemblie) {
            assemblie.onchange = () => syncLivewire(assemblie);
        }
        if (urban) {
            urban.onchange = () => {
                clearSelect(localbody);
                clearSelect(gpward);
                if (urban.value == 2 && md.blocks) {
                    fillSelect(
                        localbody,
                        md.blocks.filter(
                            (b) => b.district_code == districtSelect.value,
                        ),
                    );
                } else if (urban.value == 1 && md.ulbs) {
                    fillSelect(
                        localbody,
                        md.ulbs.filter(
                            (u) => u.district_code == districtSelect.value,
                        ),
                    );
                }
                syncLivewire(urban);
            };
        }

        // Block & GP Change logic ager motoi thakbe...
        if (localbody) {
            localbody.onchange = () => {
                clearSelect(gpward);
                if (urban.value == 2 && md.gps) {
                    fillSelect(
                        gpward,
                        md.gps.filter(
                            (g) =>
                                g.district_code == districtSelect.value &&
                                g.block_code == localbody.value,
                        ),
                    );
                } else if (urban.value == 1 && md.ulb_wards) {
                    fillSelect(
                        gpward,
                        md.ulb_wards.filter(
                            (w) => w.urban_body_code == localbody.value,
                        ),
                    );
                }
                syncLivewire(localbody);
            };
        }
        if (gpward) {
            gpward.onchange = () => syncLivewire(gpward);
        }
    }
};

// ... clearSelect, fillSelect, syncLivewire functions (ager motoi thakbe) ...

function clearSelect(select) {
    if (select) {
        select.innerHTML = '<option value="">--Select--</option>';
        delete select.dataset.loaded;
    }
}

function fillSelect(select, list) {
    if (!select) return;
    clearSelect(select);
    list.forEach((row) => {
        const opt = document.createElement("option");
        opt.value = row.id;
        opt.textContent = row.text;
        select.appendChild(opt);
    });
    restoreSelected(select);
}
function restoreSelected(select) {
    const root = select.closest("[wire\\:id]");
    const key = select.dataset.wire;
    if (!root || !key) return;

    const component = Livewire.find(root.getAttribute("wire:id"));
    if (!component) return;

    const value = component.get("formData." + key);
    if (value) select.value = value;
}

function syncLivewire(select) {
    const root = select.closest("[wire\\:id]");
    const wireKey = select.dataset.wire;
    if (root && wireKey) {
        Livewire.find(root.getAttribute("wire:id"))?.set(
            "formData." + wireKey,
            select.value,
        );
    }
}

// Smart Observer jeta HTML poriborton holei check korbe
const observer = new MutationObserver(() => {
    window.initMasterData();
});
observer.observe(document.body, { childList: true, subtree: true });
