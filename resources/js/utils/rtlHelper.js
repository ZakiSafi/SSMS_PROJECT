/**
 * Global RTL Helper Utility
 * Forces RTL alignment on all inputs when locale is RTL
 */

export const setupGlobalRTL = (locale) => {
    const isRTL = locale === 'fa' || locale === 'pa';
    
    if (!isRTL) return;
    
    // Create a style element that will override everything
    let styleElement = document.getElementById('rtl-forced-styles');
    if (!styleElement) {
        styleElement = document.createElement('style');
        styleElement.id = 'rtl-forced-styles';
        document.head.appendChild(styleElement);
    }
    
    // Inject ultra-aggressive CSS
    styleElement.textContent = `
        [dir="rtl"] input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="hidden"]) {
            text-align: right !important;
            direction: rtl !important;
        }
        [dir="rtl"] input::placeholder,
        [dir="rtl"] input::-webkit-input-placeholder,
        [dir="rtl"] input::-moz-placeholder,
        [dir="rtl"] input:-ms-input-placeholder {
            text-align: right !important;
            direction: rtl !important;
        }
        [dir="rtl"] .v-field__input input,
        [dir="rtl"] .v-field__input input::placeholder,
        [dir="rtl"] .v-text-field input,
        [dir="rtl"] .v-text-field input::placeholder,
        [dir="rtl"] .v-select input,
        [dir="rtl"] .v-select input::placeholder {
            text-align: right !important;
            direction: rtl !important;
        }
        [dir="rtl"] .vue3-datepicker input,
        [dir="rtl"] .vue3-datepicker input::placeholder,
        [dir="rtl"] .vdp-datepicker input,
        [dir="rtl"] .vdp-datepicker input::placeholder {
            text-align: right !important;
            direction: rtl !important;
        }
    `;
    
    // Function to apply inline styles directly
    const applyInlineStyles = () => {
        const inputs = document.querySelectorAll('[dir="rtl"] input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="hidden"])');
        inputs.forEach(input => {
            input.style.setProperty('text-align', 'right', 'important');
            input.style.setProperty('direction', 'rtl', 'important');
        });
    };
    
    // Run immediately
    applyInlineStyles();
    
    // Set up interval to keep applying (as a fallback)
    const intervalId = setInterval(applyInlineStyles, 500);
    
    // Clean up function
    return () => {
        clearInterval(intervalId);
        if (styleElement) {
            styleElement.remove();
        }
    };
};

