import './styles/app.css';
import './stimulus_bootstrap.js';
import { registerVueControllerComponents } from '@symfony/ux-vue';
import { createApp } from 'vue';

import * as Turbo from '@hotwired/turbo';

Turbo.start();

import AppHeader from './vue/controllers/AppHeader.vue'; 

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

let headerInstance = null;

function mountHeader() {
    const root = document.getElementById('vue-header-app');
    
    if (root && !headerInstance) {
        headerInstance = createApp(AppHeader);
        headerInstance.mount(root);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountHeader);
} else {
    mountHeader();
}
