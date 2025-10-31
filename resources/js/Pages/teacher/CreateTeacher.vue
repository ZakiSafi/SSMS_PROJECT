<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="FacultyRepository.createDialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                FacultyRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("create")
                            }}
                        </h2>
                        <v-btn variant="text" @click="isActive.value = false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider class="border-opacity-100 mx-6"></v-divider>

                    <v-card-text>
                        <v-form
                            ref="formRef"
                            class="pt-4"
                            @submit.prevent="save"
                        >
                            <v-row>
                                <v-col cols="12">
                                    <DatePicker
                                        v-model="formData.academic_year"
                                        format="jYYYY"
                                        type="year"
                                        :placeholder="$t('Select year')"
                                        rounded
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <RTLInput
                                        v-model="formData.total_teachers"
                                        :placeholder="$t('Total Teachers')"
                                        type="number"
                                        input-class="compact"
                                        :required="true"
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <RTLSelect
                                        v-model="formData.university_id"
                                        :items="FacultyRepository.universities"
                                        item-value="id"
                                        item-title="name"
                                        :placeholder="$t('University')"
                                        select-class="compact"
                                        :required="true"
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn
                            type="submit"
                            color="primary"
                            class="px-4"
                            @click="save"
                        >
                            {{
                                FacultyRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("form.submit")
                            }}
                        </v-btn>
                    </div>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick, watch } from "vue";
import { useFacultyRepository } from "@/store/FacultyRepository";
import DatePicker from "vue3-persian-datetime-picker";
import { useI18n } from "vue-i18n";
import RTLInput from "@/components/RTLInput.vue";
import RTLSelect from "@/components/RTLSelect.vue";

const { locale, t } = useI18n();
const dir = computed(() => (locale.value === "fa" || locale.value === "pa" ? "rtl" : "ltr"));

const FacultyRepository = useFacultyRepository();

// Force RTL alignment on inputs after render - Ultra aggressive
const applyRTLStyles = () => {
    if (locale.value !== "fa" && locale.value !== "pa") return;
    
    const applyStyles = () => {
        // Find all inputs in RTL containers - try multiple selectors
        const container = document.querySelector('[dir="rtl"]') || 
                          document.querySelector('.v-dialog [dir="rtl"]') ||
                          document.querySelector('.v-overlay__content');
        
        if (!container) return;
        
        // Get all inputs including nested ones - search within container or globally if needed
        const allInputs = container ? 
            container.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="hidden"])') :
            document.querySelectorAll('[dir="rtl"] input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="submit"]):not([type="button"]):not([type="hidden"])');
        
        allInputs.forEach((input) => {
            // Force inline styles
            input.style.setProperty('text-align', 'right', 'important');
            input.style.setProperty('direction', 'rtl', 'important');
            
            // Force placeholder alignment using CSS variable or direct style
            const style = document.createElement('style');
            style.textContent = `
                input[id="${input.id || Math.random()}"]::placeholder,
                input[placeholder*="${input.placeholder || ''}"]::placeholder {
                    text-align: right !important;
                    direction: rtl !important;
                }
            `;
            
            // Try to add a unique class for targeting
            if (!input.classList.contains('rtl-forced')) {
                input.classList.add('rtl-forced');
                document.head.appendChild(style);
            }
        });
        
        // Special handling for Vuetify v-field inputs
        const fieldInputs = container.querySelectorAll('.v-field__input input, .v-field input');
        fieldInputs.forEach((input) => {
            input.style.setProperty('text-align', 'right', 'important');
            input.style.setProperty('direction', 'rtl', 'important');
        });
        
        // DatePicker specific - Ultra aggressive
        const datePickerContainers = container.querySelectorAll('.vue3-datepicker, .vdp-datepicker, [class*="datepicker"], [class*="DatePicker"]');
        datePickerContainers.forEach((picker) => {
            const inputs = picker.querySelectorAll('input');
            inputs.forEach((input) => {
                input.style.setProperty('text-align', 'right', 'important');
                input.style.setProperty('direction', 'rtl', 'important');
            });
        });
        
        // Also target DatePicker inputs directly
        const datePickerInputs = container.querySelectorAll('.vue3-datepicker input, .vdp-datepicker input, [class*="datepicker"] input, [class*="DatePicker"] input');
        datePickerInputs.forEach((input) => {
            input.style.setProperty('text-align', 'right', 'important');
            input.style.setProperty('direction', 'rtl', 'important');
        });
        
        // Target DatePicker wrapper classes
        const vdpInputs = container.querySelectorAll('.vdp-datepicker__input input, .vdp-datepicker-wrapper input, .vdp-datepicker__input-group input');
        vdpInputs.forEach((input) => {
            input.style.setProperty('text-align', 'right', 'important');
            input.style.setProperty('direction', 'rtl', 'important');
        });
    };
    
    // Run immediately and with delays
    nextTick(() => {
        applyStyles();
        setTimeout(applyStyles, 50);
        setTimeout(applyStyles, 150);
        setTimeout(applyStyles, 300);
        setTimeout(applyStyles, 500);
        setTimeout(applyStyles, 1000);
    });
};

// MutationObserver to watch for new inputs being added
let observer = null;

const setupObserver = () => {
    if (observer) observer.disconnect();
    
    // Try to find the dialog container or any RTL container
    const container = document.querySelector('.v-dialog [dir="rtl"], [dir="rtl"], .v-overlay__content [dir="rtl"]');
    if (!container) {
        // If no container yet, observe the document body
        const body = document.body;
        observer = new MutationObserver(() => {
            applyRTLStyles();
        });
        observer.observe(body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class', 'dir']
        });
        return;
    }
    
    observer = new MutationObserver(() => {
        applyRTLStyles();
    });
    
    observer.observe(container, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'dir']
    });
    
    // Also observe the document in case dialog is rendered outside
    const bodyObserver = new MutationObserver(() => {
        applyRTLStyles();
    });
    bodyObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
};

onMounted(() => {
    FacultyRepository.FetchUniversities();
    applyRTLStyles();
    setupObserver();
});

// Watch for locale changes
watch(locale, () => {
    applyRTLStyles();
    setupObserver();
});

// Watch for dialog opening/closing to apply styles when dialog becomes visible
watch(() => FacultyRepository.createDialog, (isOpen) => {
    if (isOpen) {
        applyRTLStyles();
        setupObserver();
        // Multiple attempts to catch all rendering scenarios
        const intervals = [100, 200, 300, 500, 800, 1200];
        intervals.forEach(delay => {
            setTimeout(() => {
                applyRTLStyles();
                setupObserver();
            }, delay);
        });
    } else {
        if (observer) {
            observer.disconnect();
            observer = null;
        }
    }
});

const formRef = ref(null);

const formData = reactive({
    id: FacultyRepository.teacher.id,
    academic_year: FacultyRepository.teacher.academic_year?.toString() || null,
    total_teachers: FacultyRepository.teacher.total_teachers,
    university_id: FacultyRepository.teacher.university?.id || null,
});

const rules = {
    required: (value) => !!value || "This field is required.",
    number: (value) => /^[0-9]+$/.test(value) || "Please enter a valid number.",
};

const save = async () => {
    // Validate manually since we're using custom inputs
    if (!formData.total_teachers || formData.total_teachers === '') {
        return;
    }
    if (!/^[0-9]+$/.test(formData.total_teachers)) {
        return;
    }
    if (!formData.university_id) {
        return;
    }
    
    if (FacultyRepository.isEditMode) {
        await FacultyRepository.updateTeacher(formData, formData.id);
    } else {
        await FacultyRepository.createTeacher(formData);
    }
};
</script>

<style scoped>
/* RTL styling for inputs and placeholders in this component */
[dir="rtl"] :deep(.v-text-field input),
[dir="rtl"] :deep(.v-select input),
[dir="rtl"] :deep(.v-field__input input),
[dir="rtl"] :deep(input::placeholder),
[dir="rtl"] :deep(.v-field__input input::placeholder),
[dir="rtl"] :deep(.v-field input) {
    text-align: right !important;
    direction: rtl !important;
}

/* DatePicker RTL styling - Ultra specific */
[dir="rtl"] :deep(.vue3-datepicker input),
[dir="rtl"] :deep(.vdp-datepicker input),
[dir="rtl"] :deep(.vue3-datepicker input::placeholder),
[dir="rtl"] :deep(.vdp-datepicker input::placeholder),
[dir="rtl"] :deep([class*="datepicker"] input),
[dir="rtl"] :deep([class*="DatePicker"] input),
[dir="rtl"] :deep([class*="datepicker"] input::placeholder),
[dir="rtl"] :deep([class*="DatePicker"] input::placeholder) {
    text-align: right !important;
    direction: rtl !important;
}
</style>
