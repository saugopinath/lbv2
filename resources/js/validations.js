/**
 * =========================================================
 * VALIDATION ALGORITHMS
 * =========================================================
 *
 * 1. Aadhaar — Verhoeff Checksum Algorithm
 *    (Official UIDAI checksum method for 12-digit Aadhaar)
 *    Reference: https://en.wikipedia.org/wiki/Verhoeff_algorithm
 *
 * 2. EPIC (Voter ID) — Format only: [A-Z]{3}[0-9]{7} (10 chars)
 *    No public checksum algorithm exists for Voter ID.
 *
 * 3. PAN — Format only: [A-Z]{5}[0-9]{4}[A-Z]{1} (10 chars)
 *    No public checksum algorithm exists for PAN.
 * =========================================================
 */

/**
 * Verhoeff algorithm tables stored as JSON.
 * d  = multiplication table
 * p  = permutation table
 * inv = inverse table
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

/**
 * Validate Aadhaar number using Verhoeff algorithm.
 * Returns true if the 12-digit number passes checksum.
 * @param {string} num - 12-digit Aadhaar string
 * @returns {boolean}
 */
window.validateVerhoeff = function(num) {
    if (!/^\d{12}$/.test(num)) return false;
    let c = 0;
    const arr = num.split('').reverse().map(Number);
    for (let i = 0; i < arr.length; i++) {
        c = verhoeffTables.d[c][verhoeffTables.p[i % 8][arr[i]]];
    }
    return c === 0;
};

/**
 * Validate EPIC (Voter ID) format.
 * Format: 3 uppercase letters + 7 digits = 10 characters total.
 * No public checksum — format validation only.
 * @param {string} val
 * @returns {boolean}
 */
window.validateEpic = function(val) {
    return /^[A-Z]{3}[0-9]{7}$/.test(val);
};

/**
 * Validate PAN Card format.
 * Format: 5 uppercase letters + 4 digits + 1 uppercase letter = 10 chars.
 * No public checksum — format validation only.
 * @param {string} val
 * @returns {boolean}
 */
window.validatePan = function(val) {
    return /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(val);
};

/**
 * Validate Vehicle Registration format.
 */
window.validateVehicle = function(val) {
    if (!val) return false;
    let cleaned = val.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
    return /^[A-Z]{2}[0-9]{2}[A-Z]{1,3}[0-9]{4}$/.test(cleaned);
};
