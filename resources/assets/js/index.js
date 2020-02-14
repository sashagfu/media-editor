import Vue from 'vue';
import axios from 'axios';
import { get } from 'lodash';
import VueSweetalert2 from 'vue-sweetalert2';
import VueChatScroll from 'vue-chat-scroll';
import VueVideoPlayer from 'vue-video-player';
import * as VueGoogleMaps from 'vue2-google-maps';
// ElementUI
import ElementUI from 'element-ui';
import ElementUILocale from 'element-ui/lib/locale/lang/en';
// fontawesome
import fontawesome from '@fortawesome/fontawesome';
import brands from '@fortawesome/fontawesome-free-brands';
import regular from '@fortawesome/fontawesome-free-regular';
import solid from '@fortawesome/fontawesome-free-solid';
import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
// graphQL
import VueApollo from 'vue-apollo';
import apolloClient from '../graphQL';

import EventBus from './helpers/EventBus';
import { store } from './store';

import * as HomePage from './vue/components/HomePage/HomePage';
// import * as ChatWidget from './vue/components/ChatWidget/ChatWidget';
import * as MediaEditorProjectsPage from './vue/components/MediaEditorProjects/MediaEditorProjectsPage';
import GenericLayout from './vue/components/Layout/GenericLayout';

// ElementUI styles
import '../sass/front/components/el-theme/index.css';
// import '../sass/front/components/element-variables.scss';

import router from './router';

Vue.use(VueApollo);
const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
});

Vue.use(ElementUI, { locale: ElementUILocale });
fontawesome.library.add(brands, regular, solid);

Vue.use(VueVideoPlayer, ElementUI, { locale: ElementUILocale });
Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyBMC71R_eezNZCvBCtJWhkZ8SVcFjqgS_o',
    libraries: 'places',
  },
});
Vue.use(VueSweetalert2);

Vue.use(VueChatScroll);


// window.$ = require('jquery');
const $ = require('jquery');

// Vue translation helper
window.trans = string => get(window.i18n, string);
Vue.prototype.trans = string => get(window.i18n, string);
Vue.prototype.$http = axios;

// create EventBus in Vue instance
Vue.prototype.$bus = EventBus;

Vue.create = options => new Vue(options);
Vue.component('font-awesome-icon', FontAwesomeIcon);

if ($('#homepage').length) {
  Vue.create({
    el: '#homepage',
    store,
    provide: apolloProvider.provide(),
    components: {
      HomePage,
    },
  });
}
if ($('#media-editor-projects-page').length) {
  Vue.create({
    el: '#media-editor-projects-page',
    store,
    provide: apolloProvider.provide(),
    components: {
      MediaEditorProjectsPage,
    },
  });
}
if ($('#generic-layout').length) {
  Vue.create({
    el: '#generic-layout',
    store,
    router,
    provide: apolloProvider.provide(),
    components: {
      GenericLayout,
    },
  });
}

