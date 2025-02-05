import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import Layout from './Layouts/Layout.vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

createInertiaApp({
  title: (title) => `Crafty Corners ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout  ;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .component('Head', Head)
      .component('Link', Link)
      .mount(el)
  },

  progress:{

    color: '#d93479',

    includeCSS: true,

    showSpinner: false,



  },
})

