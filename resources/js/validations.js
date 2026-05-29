/**
 * =========================================================
 * VALIDATION ALGORITHMS & HELPERS
 * =========================================================
 */

const verhoeffTables = {
    d: [
        [0,1,2,3,4,5,6,7,8,9],
        [1,2,3,4,0,6,7,8,9,5],
        [2,3,4,0,1,7,8,9,5,6],
        [3,4,0,1,2,8,9,5,6,7],
        [4,0,1,2,3,9,5,6,7,8],
        [5,9,8,7,6,0,4,3,2,1],
        [6,5,9,8,7,1,0,4,3,2],
        [7,6,5,9,8,2,1,0,4,3],
        [8,7,6,5,9,3,2,1,0,4],
        [9,8,7,6,5,4,3,2,1,0]
    ],
    p: [
        [0,1,2,3,4,5,6,7,8,9],
        [1,5,7,6,2,8,3,0,9,4],
        [5,8,0,3,7,9,6,1,4,2],
        [8,9,1,6,0,4,3,5,2,7],
        [9,4,5,3,1,2,6,8,7,0],
        [4,2,8,6,5,7,3,9,0,1],
        [2,7,9,3,8,0,6,4,1,5],
        [7,0,4,6,9,1,3,2,5,8]
    ],
    inv: [0,4,3,2,1,5,6,7,8,9]
};

window.validateVerhoeff = function(num) {
    if (!/^[2-9]\d{11}$/.test(num)) return false;
    if (/^(\d)\1{11}$/.test(num)) return false;
    let c = 0;
    const arr = num.split('').reverse().map(Number);
    for (let i = 0; i < arr.length; i++) {
        c = verhoeffTables.d[c][verhoeffTables.p[i % 8][arr[i]]];
    }
    return c === 0;
};

window.validateEpic = function(val) {
    return /^[A-Z]{3}[0-9]{7}$/.test(val);
};

window.validatePan = function(val) {
    return /^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/.test(val);
};

window.validateVehicle = function(val) {
    if (!val) return false;
    let cleaned = val.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
    return /^[A-Z]{2}[0-9]{2}[A-Z]{1,3}[0-9]{4}$/.test(cleaned);
};

// Real-time Input Sanitizers
window.cleanInput = {
    numeric(val, maxLen) {
        if (val === null || val === undefined) return '';
        let cleaned = val.toString().replace(/\D/g, '');
        return maxLen ? cleaned.slice(0, maxLen) : cleaned;
    },
    alphaNumericUpper(val, maxLen) {
        if (val === null || val === undefined) return '';
        let cleaned = val.toString().replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        return maxLen ? cleaned.slice(0, maxLen) : cleaned;
    },
    lettersOnly(val) {
        if (val === null || val === undefined) return '';
        return val.toString().replace(/[^A-Za-z\s.]/g, '');
    }
};

// Real-time Validation Checkers
window.checkValid = {
    aadhaar(val) {
        return window.validateVerhoeff(val);
    },
    epic(val) {
        return window.validateEpic(val);
    },
    pan(val) {
        return window.validatePan(val);
    },
    ifsc(val) {
        return /^[A-Z]{4}0[A-Z0-9]{6}$/.test(val);
    },
    contact_no(val) {
        return /^[0-9]{10}$/.test(val);
    },
    pincode(val) {
        return /^[0-9]{6}$/.test(val);
    },
    vehicle(val) {
        return window.validateVehicle(val);
    },
    acc_no(val) {
        return /^[0-9]{9,18}$/.test(val);
    },
    name(val) {
        return val.trim().length > 0 && /^[A-Za-z\s.]+$/.test(val);
    }
};
