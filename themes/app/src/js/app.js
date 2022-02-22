import { createApp } from "vue";
import { createStore } from "vuex";
import Promise from "promise-polyfill";
import Meganav from "./components/Meganav.vue";
import FlyoutPanel from "./components/FlyoutPanel/FlyoutPanel.vue";
import Tabs from "./components/Tabs/Tabs.vue";
import Tab from "./components/Tabs/Tab.vue";

/**
 * IE support, if you're not supporting IE remove this and update your babel yo
 */
if (!window.Promise) {
  window.Promise = Promise;
}

const RootApp = {
  components: {
    FlyoutPanel,
    Tabs,
    Tab,
  },
  mixins: [Meganav],
  data() {
    return {
      mobileMenuOpen: false,
    };
  },
  methods: {
    closeMobileMenu() {
      this.mobileMenuOpen = false;
    },
    openMobileMenu() {
      this.mobileMenuOpen = true;
    },
  },
};

const app = createApp(RootApp);
app.mount("#app");

/**
 * Enable and setup Vuex
 *
 * This should be used when we need to share state amongst components or actions
 * occur across components.
 */
const store = createStore({
  state: {},

  getters: {},

  /* eslint-disable no-param-reassign */
  mutations: {},
  /* eslint-enable no-param-reassign */

  actions: {},
});

app.use(store);
