import { storiesOf } from "@storybook/vue3";
import Color from "./components/Color.vue";

storiesOf("Styleguide", module).add("Colour Palette", () => ({
  components: {
    Color,
  },
  template: `
      <div class="sb__story-container">
        <section class="sb__group">
          <h2 class="sb__header">Default Bootstrap Colours</h2>
          <div class="row">
            <div class="col">
                <color name="$blue" hex="#007bff" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$indigo" hex="#6610f2" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$purple" hex="#6f42c1" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$pink" hex="#e83e8c" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$red" hex="#dc3545" :showOpacity="true" />
            </div>
          </div>
          <div class="row">
            <div class="col">
                <color name="$orange" hex="#fd7e14" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$yellow" hex="#ffc107" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$green" hex="#28a745" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$teal" hex="#20c997" :showOpacity="true" />
            </div>
            <div class="col">
                <color name="$cyan" hex="#17a2b8" :showOpacity="true" />
            </div>
          </div>
        </section>
        <section class="sb__group">
          <h2 class="sb__header">Grayscale Colours</h2>
          <div class="row">
            <div class="col">
                <color name="$white" hex="#fff" textColor="#000000" />
            </div>
            <div class="col">
                <color name="gray-100" hex="#f8f9fa" textColor="#000000" />
            </div>
            <div class="col">
                <color name="$gray-200" hex="#e9ecef" textColor="#000000" />
            </div>
            <div class="col">
                <color name="$gray-300" hex="#dee2e6" textColor="#000000" />
            </div>
            <div class="col">
                <color name="$gray-400" hex="#ced4da" textColor="#000000" />
            </div>
            <div class="col">
                <color name="$gray-500" hex="#adb5bd" textColor="#000000" />
            </div>
            <div class="col">
                <color name="$gray-600" hex="#6c757d" />
            </div>
            <div class="col">
                <color name="$gray-700" hex="#495057" />
            </div>
            <div class="col">
                <color name="$gray-800" hex="#343a40" />
            </div>
            <div class="col">
                <color name="$gray-900" hex="#212529" />
            </div>
            <div class="col">
                <color name="$black" hex="#000" />
            </div>
          </div>
        </section>
      </div>
    `,
}));
