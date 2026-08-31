document.addEventListener('alpine:init', () => {
    /**
     * Multi-strategy Input Element Resolver.
     * Locates target form controls dynamically regardless of custom IDs, Livewire bindings, or x-refs.
     *
     * @param {string} fieldKey - The field attribute identifier to look up.
     * @returns {HTMLElement|null} The matching DOM element or null if not found.
     */
    function findInputElement (fieldKey) {
        if (!fieldKey) return null

        // Sanitize field key variations (clean, snake_case, kebab-case)
        const cleanKey = fieldKey.replace(/^formData\./, '')
        const snakeKey = cleanKey.replace(/[\.\s]/g, '_')
        const kebabKey = cleanKey.replace(/[\.\s]/g, '-')

        // Strategy 1: Look up direct HTML IDs, wire:model, and standard name attributes
        const directSelectors = [
            `#${cleanKey}`,
            `#${snakeKey}`,
            `#${kebabKey}`,
            `[wire\\:model="${fieldKey}"]`,
            `[wire\\:model\\.live="${fieldKey}"]`,
            `[wire\\:model="formData.${cleanKey}"]`,
            `[wire\\:model\\.live="formData.${cleanKey}"]`,
            `[wire\\:model="${cleanKey}"]`,
            `[wire\\:model\\.live="${cleanKey}"]`,
            `[name="${fieldKey}"]`,
            `[name="${cleanKey}"]`
        ]

        for (let selector of directSelectors) {
            try {
                const el = document.querySelector(selector)
                if (el) return el
            } catch (e) {
                // Ignore DOM selector syntax exceptions caused by custom characters
            }
        }

        // Strategy 2: File Upload Input Fallbacks (handles custom dropzones & x-refs)
        const fileInputs = Array.from(
            document.querySelectorAll('input[type="file"]')
        )
        for (let input of fileInputs) {
            const wireModel =
                input.getAttribute('wire:model') ||
                input.getAttribute('wire:model.live') ||
                ''
            const xRef = input.getAttribute('x-ref') || ''

            if (
                wireModel === cleanKey ||
                wireModel === fieldKey ||
                wireModel.includes(cleanKey) ||
                input.id === cleanKey ||
                input.name === cleanKey ||
                xRef.toLowerCase().includes('file')
            ) {
                return input
            }
        }

        // Fallback for singular document file upload fields
        if (
            fieldKey.includes('enc') ||
            fieldKey.includes('doc') ||
            fieldKey.includes('enclosure')
        ) {
            if (fileInputs.length === 1) return fileInputs[0]
        }

        // Strategy 3: Checkbox and Radio input fallback searches
        const checkboxes = document.querySelectorAll(
            'input[type="checkbox"], input[type="radio"]'
        )
        for (let cb of checkboxes) {
            if (
                cb.id.replace(/[\.\s]/g, '_').toLowerCase() ===
                    snakeKey.toLowerCase() ||
                cb.name.replace(/[\.\s]/g, '_').toLowerCase() ===
                    snakeKey.toLowerCase()
            ) {
                return cb
            }
        }

        return null
    }

    Alpine.store('formState', {
        clientErrors: {},
        serverErrors: {},

        setServerErrors (errors) {
            /* console.log('📥 [formState] Updating global serverErrors:', errors) */
            this.serverErrors = errors || {}
        },

        clearAll () {
            this.clientErrors = {}
            this.serverErrors = {}
        }
    })

    Alpine.data('formErrorBanner', (initialServerErrors = {}) => ({
        /** @type {Object<string, string>} Stores backend validation errors passed from Livewire */
        serverErrors: initialServerErrors,

        init () {
            /* console.log(
                '🔍 [formErrorBanner:init] Component mounted. Initial Server Errors:',
                JSON.parse(JSON.stringify(this.serverErrors))
            ) */

            // Listen for client-side error changes
            window.addEventListener('update-client-errors', () => {
                const rawClientErrors =
                    Alpine.store('formState')?.clientErrors || {}
                /* console.log(
                    '📡 [Event: update-client-errors] Received store state:',
                    JSON.parse(JSON.stringify(rawClientErrors))
                ) */

                const hasActiveClientErrors = Object.values(
                    rawClientErrors
                ).some(msg => msg && String(msg).trim().length > 0)

                if (hasActiveClientErrors) {
                    /* console.log(
                        '🧹 [Event: update-client-errors] Active client errors detected. Clearing server errors.'
                    ) */
                    this.serverErrors = {}
                } else {
                    /* console.log(
                        'ℹ️ [Event: update-client-errors] No active client errors found.'
                    ) */
                }
            })
        },

        resolveFieldId (key) {
            return key.replace(/^formData\./, '').replace(/[\.\s]/g, '_')
        },

        jumpTo (key) {
            /* console.log('🎯 [formErrorBanner:jumpTo] Triggered for key:', key) */
            const target = findInputElement(key)

            if (target) {
                /* console.log(
                    '✅ [formErrorBanner:jumpTo] Target element located:',
                    target
                ) */
                target.scrollIntoView({ behavior: 'smooth', block: 'center' })
                target.focus()

                target.classList.add('ring-2', 'ring-red-500', 'border-red-500')

                setTimeout(() => {
                    target.classList.remove(
                        'ring-2',
                        'ring-red-500',
                        'border-red-500'
                    )
                }, 2000)
            } else {
                /* console.warn(
                    '❌ [formErrorBanner:jumpTo] Could not locate DOM input element for key:',
                    key
                ) */
            }
        },

        get activeErrors () {
            const rawClientErrors =
                Alpine.store('formState')?.clientErrors || {}

            // Extract only non-empty string client error messages
            const activeClientErrors = Object.fromEntries(
                Object.entries(rawClientErrors).filter(([_, msg]) =>
                    Boolean(msg && String(msg).trim().length > 0)
                )
            )

            /* console.group('📊 [Getter: activeErrors]') */
            /* console.log(
                'Client Errors Store:',
                JSON.parse(JSON.stringify(activeClientErrors))
            ) */
            /* console.log(
                'Server Errors State:',
                JSON.parse(JSON.stringify(this.serverErrors))
            ) */

            const selected =
                Object.keys(activeClientErrors).length > 0
                    ? activeClientErrors
                    : this.serverErrors || {}

            /* console.log(
                'Final Selected activeErrors Output:',
                JSON.parse(JSON.stringify(selected))
            ) */
            /* console.groupEnd() */

            return selected
        },

        get totalCount () {
            const count = Object.keys(this.activeErrors).length
            /* console.log('🔢 [Getter: totalCount] Total Error Count:', count) */
            return count
        }
    }))

    /**
     * Main Hybrid Form Engine Component
     *
     * Handles client-side validation, cross-browser DOM input lookup,
     * field evaluators, and step-by-step submission dispatching to Livewire.
     */
    Alpine.data('hybridFormGuard', () => ({
        /**
         * Initializes shared Alpine store memory for form error state tracking.
         */
        init () {
            /* console.log('🚀 [hybridFormGuard:init] Initializing form store...') */
            if (!Alpine.store('formState')) {
                Alpine.store('formState', { clientErrors: {} })
            }
        },

        findInputElement,

        evaluators: {
            required: (val, args, input) => {
                if (!input) return false
                if (input.type === 'file')
                    return input.files && input.files.length > 0
                if (input.type === 'checkbox') return input.checked === true
                return (
                    val !== null &&
                    val !== undefined &&
                    val.toString().trim() !== ''
                )
            },
            accepted: (val, args, input) => {
                if (input && input.type === 'checkbox')
                    return input.checked === true
                return ['1', 'true', 'yes', 'on'].includes(
                    String(val).toLowerCase()
                )
            },
            file: (val, args, input) => {
                if (!input || input.type !== 'file') return true
                return (
                    input.files.length === 0 || input.files[0] instanceof File
                )
            },
            mimes: (val, args, input) => {
                if (!input || input.type !== 'file' || !input.files.length)
                    return true
                const extension = input.files[0].name
                    .split('.')
                    .pop()
                    .toLowerCase()
                const allowedExtensions = args.map(ext =>
                    ext.toLowerCase().trim()
                )
                return allowedExtensions.includes(extension)
            },
            string: val => typeof val === 'string' || val instanceof String,
            numeric: val => !isNaN(val) && val !== '',
            min: (val, [min]) => {
                if (!val) return true
                return !isNaN(val) && val !== ''
                    ? Number(val) >= Number(min)
                    : String(val).length >= Number(min)
            },
            max: (val, [max], input) => {
                if (input && input.type === 'file') {
                    if (!input.files.length) return true
                    return input.files[0].size / 1024 <= Number(max)
                }
                if (!val) return true
                return !isNaN(val) && val !== ''
                    ? Number(val) <= Number(max)
                    : String(val).length <= Number(max)
            },
            digits: (val, [d]) =>
                !val ||
                (/^\d+$/.test(val) && val.toString().length === Number(d))
        },

        validateClientSide () {
            /* console.log('🧪 [validateClientSide] Running client validation...') */
            let errors = {}
            let isValid = true

            const rulesScript = document.getElementById('active-tab-rules')
            const activeRules = rulesScript
                ? JSON.parse(rulesScript.textContent || '{}')
                : {}

            /* console.log(
                '📋 [validateClientSide] Active rules loaded:',
                activeRules
            ) */

            for (let [fieldKey, ruleString] of Object.entries(activeRules)) {
                const input = this.findInputElement(fieldKey)

                if (!input) {
                    /* console.log(
                        `⏩ [validateClientSide] Field ${fieldKey} not found in DOM, skipping.`
                    ) */
                    continue
                }

                if (input.type === 'file') {
                    continue
                }

                const ruleData = activeRules[fieldKey]

                const labelName =
                    (typeof ruleData === 'object' && ruleData?.level_name) ||
                    input.getAttribute('data-label-name') ||
                    input.getAttribute('data-label') ||
                    fieldKey
                        .replace(/^formData\./, '')
                        .replace(/[\._]/g, ' ')
                        .replace(/\b\w/g, letter => letter.toUpperCase())

                const actualRules =
                    typeof ruleString === 'object' && ruleString !== null
                        ? ruleString.rules || ''
                        : ruleString || ''

                const ruleList = Array.isArray(actualRules)
                    ? actualRules
                    : actualRules.split('|')

                const isRequired =
                    ruleList.includes('required') ||
                    ruleList.includes('accepted')

                let val = ''
                if (input.type === 'checkbox') {
                    val = input.checked ? input.value : ''
                } else if (input.type === 'radio') {
                    const checkedRadio = document.querySelector(
                        `input[name="${input.name}"]:checked`
                    )
                    val = checkedRadio ? checkedRadio.value : ''
                } else if (input.type === 'file') {
                    val =
                        input.files && input.files.length > 0
                            ? input.files[0].name
                            : ''
                } else {
                    val = input.value ? input.value.trim() : ''
                }

                if (
                    !isRequired &&
                    val === '' &&
                    (!input.files || input.files.length === 0)
                ) {
                    continue
                }

                if (
                    ruleList.includes('nullable') &&
                    val === '' &&
                    (!input.files || input.files.length === 0)
                ) {
                    continue
                }

                for (let rule of ruleList) {
                    const [ruleName, argString] = rule.split(':')
                    const args = argString ? argString.split(',') : []

                    if (this.evaluators[ruleName]) {
                        if (!this.evaluators[ruleName](val, args, input)) {
                            const isNumeric = ruleList.includes('numeric') || ruleList.includes('integer');
                            const isFile = input && input.type === 'file';

                            if (ruleName === 'mimes') {
                                errors[fieldKey] = `The ${labelName} must be a file of type: ${args.join(', ')}.`;
                            } else if (ruleName === 'max') {
                                if (isFile) {
                                    errors[fieldKey] = `The ${labelName} file size must not exceed ${args[0]} KB.`;
                                } else if (isNumeric) {
                                    errors[fieldKey] = `The ${labelName} must not be greater than ${args[0]}.`;
                                } else {
                                    errors[fieldKey] = `The ${labelName} must not be greater than ${args[0]} characters.`;
                                }
                            } else if (ruleName === 'min') {
                                if (isNumeric) {
                                    errors[fieldKey] = `The ${labelName} must be at least ${args[0]}.`;
                                } else {
                                    errors[fieldKey] = `The ${labelName} must be at least ${args[0]} characters.`;
                                }
                            } else if (ruleName === 'numeric') {
                                errors[fieldKey] = `The ${labelName} must be a number.`;
                            } else if (ruleName === 'digits') {
                                errors[fieldKey] = `The ${labelName} must be exactly ${args[0]} digits.`;
                            } else if (ruleName === 'string') {
                                errors[fieldKey] = `The ${labelName} must be a valid text string.`;
                            } else if (ruleName === 'accepted' || (ruleName === 'required' && input && input.type === 'checkbox')) {
                                errors[fieldKey] = `You must accept the ${labelName} declaration.`;
                            } else if (ruleName === 'required') {
                                if (isFile) {
                                    errors[fieldKey] = `Please upload the required document for ${labelName}.`;
                                } else {
                                    errors[fieldKey] = `The ${labelName} field is required.`;
                                }
                            } else {
                                errors[fieldKey] = `The ${labelName} format is invalid.`;
                            }

                            isValid = false
                            break
                        }
                    }
                }
            }

            /* console.log(
                `🧪 [validateClientSide] Validation complete. Result: ${
                    isValid ? 'PASSED' : 'FAILED'
                }`,
                errors
            ) */

            if (Alpine.store('formState')) {
                Alpine.store('formState').clientErrors = errors
            }
            window.dispatchEvent(new CustomEvent('update-client-errors'))

            return isValid
        },

        async processSaveAndNext (nextTab, enableClientValidation = true) {
            /* console.log(
                `📤 [processSaveAndNext] Initiated for tab: "${nextTab}". Enable Client Validation: ${enableClientValidation}`
            ) */

            if (Alpine.store('formState')) {
                /* console.log(
                    '🧹 [processSaveAndNext] Purging store clientErrors prior to dispatch.'
                ) */
                Alpine.store('formState').clientErrors = {}
            }

            const clientPassed = enableClientValidation
                ? this.validateClientSide()
                : true

            // const clientPassed = true // DO NOT REMOVE THIS COMMENT: ENABLE THIS IF YOU WISH TO BYPASS CLIENT VALIDATION

            if (!clientPassed) {
                /* console.warn(
                    '🛑 [processSaveAndNext] Client validation failed. Stopping execution before Livewire dispatch.'
                ) */
                this.scrollToErrorBanner()
                return false
            }

            if (window.Livewire) {
                /* console.log(
                    '📡 [processSaveAndNext] Client validation passed. Dispatching Livewire call "saveAndNext"...'
                ) */
                Livewire.dispatch('showLoader')
            }

            try {
                await this.$wire.call('saveAndNext', nextTab)
                /* console.log(
                    '✅ [processSaveAndNext] $wire.call("saveAndNext") promise resolved.'
                ) */
            } catch (e) {
                /* console.error(
                    '❌ [processSaveAndNext] Error during $wire.call:',
                    e
                ) */
            } finally {
                this.scrollToErrorBanner()
            }
        },

        async processFinalSubmit (enableClientValidation = true) {
            /* console.log(
                `📤 [processFinalSubmit] Initiated. Enable Client Validation: ${enableClientValidation}`
            ) */

            if (Alpine.store('formState')) {
                Alpine.store('formState').clientErrors = {}
            }

            const clientPassed = enableClientValidation
                ? this.validateClientSide()
                : true

            if (!clientPassed) {
                /* console.warn(
                    '🛑 [processFinalSubmit] Client validation failed. Stopping execution before Livewire dispatch.'
                ) */
                this.scrollToErrorBanner()
                return false
            }

            if (window.Livewire) {
                /* console.log(
                    '📡 [processFinalSubmit] Client validation passed. Dispatching Livewire call "finalSubmit"...'
                ) */
                Livewire.dispatch('showLoader')
            }

            try {
                await this.$wire.call('finalSubmit')
                /* console.log(
                    '✅ [processFinalSubmit] $wire.call("finalSubmit") promise resolved.'
                ) */
            } catch (e) {
                /* console.error(
                    '❌ [processFinalSubmit] Error during $wire.call:',
                    e
                ) */
            } finally {
                this.scrollToErrorBanner()
            }
        },

        scrollToErrorBanner () {
            this.$nextTick(() => {
                const errorBanner =
                    document.getElementById('form-error-summary')
                if (errorBanner) {
                    /* console.log(
                        '📜 [scrollToErrorBanner] Error banner found, scrolling into view.'
                    ) */
                    errorBanner.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    })
                } else {
                    /* console.warn(
                        '⚠️ [scrollToErrorBanner] DOM element "#form-error-summary" not found!'
                    ) */
                }
            })
        }
    }))
})
