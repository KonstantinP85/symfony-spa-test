import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css' // Ensure you are using css-loader
import { VDataTableServer } from 'vuetify/labs/VDataTable'


const vuetify = createVuetify({
    components: {
        ...components,
        VDataTableServer
    },
    directives,
    icons: {
        defaultSet: 'mdi', // This is already the default value - only for display purposes
    },
})

export default vuetify