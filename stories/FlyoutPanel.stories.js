import { storiesOf } from "@storybook/vue3";

import FlyoutPanel from "../themes/app/src/js/components/FlyoutPanel/FlyoutPanel.vue";

storiesOf("Components/Flyout Panel", module).add("with toggle", () => ({
  components: { FlyoutPanel },
  template: `<div>
      <button class="btn btn-primary" @click="mobileMenuOpen = true">Open {{direction}}</button>
      <flyout-panel
        :slideFrom="direction"
        :open="mobileMenuOpen"
        @close="mobileMenuOpen = !mobileMenuOpen"
      >
        <h1>Flyout Panel</h1>
        <p>Slides in from the {{direction}}</p>
      </flyout-panel>
    </div>`,
  data() {
    return {
      mobileMenuOpen: false,
    };
  },
}));
