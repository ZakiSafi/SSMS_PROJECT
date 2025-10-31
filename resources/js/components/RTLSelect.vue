<template>
    <div class="rtl-select-wrapper" :class="{ 'is-rtl': isRTL }">
        <select
            ref="selectRef"
            :value="modelValue"
            :disabled="disabled"
            :required="required"
            :class="selectClass"
            :style="selectStyle"
            @change="handleChange"
            @blur="$emit('blur', $event)"
            @focus="$emit('focus', $event)"
        >
            <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
            <option
                v-for="item in items"
                :key="getItemValue(item)"
                :value="getItemValue(item)"
            >
                {{ getItemTitle(item) }}
            </option>
        </select>
        <div v-if="error" class="rtl-select-error">{{ error }}</div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    modelValue: [String, Number],
    items: {
        type: Array,
        default: () => []
    },
    itemTitle: {
        type: [String, Function],
        default: 'title'
    },
    itemValue: {
        type: [String, Function],
        default: 'value'
    },
    placeholder: String,
    label: String,
    disabled: Boolean,
    required: Boolean,
    error: String,
    selectClass: String,
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);

const { locale } = useI18n();
const selectRef = ref(null);

const isRTL = computed(() => locale.value === 'fa' || locale.value === 'pa');

const selectStyle = computed(() => {
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

const getItemValue = (item) => {
    if (typeof props.itemValue === 'function') {
        return props.itemValue(item);
    }
    return item[props.itemValue];
};

const getItemTitle = (item) => {
    if (typeof props.itemTitle === 'function') {
        return props.itemTitle(item);
    }
    return item[props.itemTitle] || item;
};

const handleChange = (event) => {
    const value = event.target.value === '' ? null : (props.itemValue === 'id' ? parseInt(event.target.value) : event.target.value);
    emit('update:modelValue', value);
};

onMounted(() => {
    if (isRTL.value && selectRef.value) {
        selectRef.value.style.textAlign = 'right';
        selectRef.value.style.direction = 'rtl';
    }
});

watch(locale, () => {
    if (selectRef.value) {
        if (isRTL.value) {
            selectRef.value.style.textAlign = 'right';
            selectRef.value.style.direction = 'rtl';
        } else {
            selectRef.value.style.textAlign = 'left';
            selectRef.value.style.direction = 'ltr';
        }
    }
});
</script>

<style>
.rtl-select-wrapper {
    width: 100%;
    margin-bottom: 16px;
}

.rtl-select-wrapper select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(0, 0, 0, 0.38);
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23000' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 40px;
    transition: border-color 0.3s;
    outline: none;
}

.rtl-select-wrapper.is-rtl select {
    text-align: right !important;
    direction: rtl !important;
    background-position: left 12px center;
    padding-right: 16px;
    padding-left: 40px;
}

.rtl-select-wrapper select:focus {
    border-color: #1976D2;
    border-width: 2px;
}

.rtl-select-wrapper select:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.rtl-select-label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: rgba(0, 0, 0, 0.6);
}

.rtl-select-wrapper.is-rtl .rtl-select-label {
    text-align: right;
    direction: rtl;
}

.rtl-select-error {
    margin-top: 4px;
    font-size: 12px;
    color: #d32f2f;
}

.rtl-select-wrapper.is-rtl .rtl-select-error {
    text-align: right;
    direction: rtl;
}

/* Compact density */
.rtl-select-wrapper select.compact {
    padding: 8px 12px;
    padding-right: 40px;
}

.rtl-select-wrapper.is-rtl select.compact {
    padding-left: 40px;
    padding-right: 12px;
}
</style>

