
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
var Vue = require('vue');
var VueAutosize = require('vue-autosize');
var EventBus = require('./event-bus.js');
var BootstrapVue = require('bootstrap-vue');
var Moment = require('moment');
var VueMoment = require('vue-moment');

Vue.use(BootstrapVue);
Vue.use(VueAutosize);
Vue.use(EventBus);
Vue.use(Moment);
Vue.use(VueMoment);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('new-post', require('./components/posts/new.vue'));
Vue.component('posts', require('./components/posts.vue'));
Vue.component('post-like', require('./components/posts/like.vue'));
Vue.component('post-share', require('./components/posts/share.vue'));

Vue.component('user-about', require('./components/users/About.vue'));
Vue.component('user-follow', require('./components/users/follow.vue'));
Vue.component('user-posts', require('./components/users/posts.vue'));
Vue.component('user-edit', require('./components/users/edit.vue'));
Vue.component('user-cover-photo', require('./components/users/cover-photo.vue'));

Vue.component('follow-request-actions', require('./components/users/follow-request-actions.vue'));

// Utilities
Vue.component('image-upload', require('./components/utilities/ImageUpload.vue'));

const app = new Vue({
    el: '#app'
});
