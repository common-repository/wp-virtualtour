const {__} = wp.i18n
import { createApp } from 'vue'
import App from './components/App.vue'

import './scss/bootstrap.scss'
import './scss/wp_virtualtour-admin.scss'

if (typeof wp_virtualtour === 'undefined') {
	document.getElementById('wpvtApp').innerText = __('Could not find necessary scripts.', 'wp_virtualtour')
}

const app = createApp(App)
app.provide('__', (a, b) => __(a, b))
app.provide('$scenePlaceholder', wp_virtualtour.pluginsUrl + '/wp-virtualtour/admin/vue-app/static/img/scene-placeholder.png')

app.mount('#wpvtApp')

