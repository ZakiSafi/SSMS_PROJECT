<template>
    <div class="rtl-input-wrapper" :class="{ 'is-rtl': isRTL }">
        <input
            ref="inputRef"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder || label"
            :disabled="disabled"
            :required="required"
            :class="inputClass"
            :style="inputStyle"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="$emit('blur', $event)"
            @focus="$emit('focus', $event)"
        />
        <div v-if="error" class="rtl-input-error">{{ error }}</div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    modelValue: [String, Number],
    placeholder: String,
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    disabled: Boolean,
    required: Boolean,
    error: String,
    inputClass: String,
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);

const { locale } = useI18n();
const inputRef = ref(null);

const isRTL = computed(() => locale.value === 'fa' || locale.value === 'pa');

const inputStyle = computed(() => {
    if (isRTL.value) {
        return {
            textAlign: 'right',
            direction: 'rtl'
        };
    }
    return {
        textAlign: 'left',
        direction: 'ltr'
    };
});

const updateInputStyles = () => {
    if (inputRef.value) {
        if (isRTL.value) {
            inputRef.value.style.setProperty('text-align', 'right', 'important');
            inputRef.value.style.setProperty('direction', 'rtl', 'important');
            
            // Force placeholder alignment
            const style = document.createElement('style');
            style.id = `rtl-input-placeholder-${inputRef.value.id || Math.random()}`;
            style.textContent = `
                input[placeholder="${inputRef.value.placeholder || ''}"]::placeholder,
                input[placeholder="${inputRef.value.placeholder || ''}"]::-webkit-input-placeholder,
                input[placeholder="${inputRef.value.placeholder || ''}"]::-moz-placeholder {
                    text-align: right !important;
                    direction: rtl !important;
                }
            `;
            if (!document.getElementById(style.id)) {
                document.head.appendChild(style);
            }
        } else {
            inputRef.value.style.setProperty('text-align', 'left', 'important');
            inputRef.value.style.setProperty('direction', 'ltr', 'important');
        }
    }
};

onMounted(() => {
    updateInputStyles();
    // Multiple attempts to ensure styles are applied
    setTimeout(updateInputStyles, 50);
    setTimeout(updateInputStyles, 150);
});

watch(locale, () => {
    updateInputStyles();
});

watch(() => props.placeholder || props.label, () => {
    updateInputStyles();
});
</script>

<style>
.rtl-input-wrapper {
    width: 100%;
    margin-bottom: 16px;
}

.rtl-input-wrapper input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(0, 0, 0, 0.38);
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s;
    outline: none;
}

.rtl-input-wrapper.is-rtl input {
    text-align: right !important;
    direction: rtl !important;
}

.rtl-input-wrapper.is-rtl input::placeholder {
    text-align: right !important;
    direction: rtl !important;
}

.rtl-input-wrapper input:focus {
    border-color: #1976D2;
    border-width: 2px;
}

.rtl-input-wrapper input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.rtl-input-label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: rgba(0, 0, 0, 0.6);
}

.rtl-input-wrapper.is-rtl .rtl-input-label {
    text-align: right;
    direction: rtl;
}

.rtl-input-error {
    margin-top: 4px;
    font-size: 12px;
    color: #d32f2f;
}

.rtl-input-wrapper.is-rtl .rtl-input-error {
    text-align: right;
    direction: rtl;
}

/* Outlined variant similar to Vuetify */
.rtl-input-wrapper input.outlined {
    padding: 16px 16px 8px 16px;
}

/* Compact density */
.rtl-input-wrapper input.compact {
    padding: 8px 12px;
}
</style>

