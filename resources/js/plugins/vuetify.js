// resources/js/plugins/vuetify.js
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { aliases, mdi } from 'vuetify/iconsets/mdi';

export default createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: { mdi },
    },
    theme: {
        defaultTheme: 'customTheme',
        themes: {
            customTheme: {
                dark: false,
                colors: {
                    primary: '#5A67BA',
                    secondary: '#A6ABC8',
                    background: '#f5f5f5',
                    surface: '#FFFFFF',
                    head: '#e3e5ee'
                },
            },
        },
    },
});
