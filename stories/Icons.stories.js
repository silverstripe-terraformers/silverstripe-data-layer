import { storiesOf } from "@storybook/vue3";

const icons = [
  // 'my-icon',
  // 'my-other-icon',
  "chev-down",
];

storiesOf("Styleguide", module).add("Icons", () => ({
  data() {
    return {
      icons,
    };
  },
  template: `
      <div class="sb__story-container">
        <section class="sb__group">
          <h2 class="sb__header">Available icons</h2>

          <div class="row" v-if="icons.length > 0">
            <div class="col-3" v-for="icon in icons">
              <span class="sb__icon-label">{{ icon }}</span>
              <div class="sb__icon-holder">
                <div :class="['sb__icon sb__icon--' + icon]"></div>
              </div>
            </div>
          </div>
        </section>

        <section class="sb__group">
          <div class="row">
            <div class="col">
              <h2 class="sb__header">Adding icons</h2>
              <p>To use new icons within your project, all you need to do is add them to the <code>themes/app/src/icons</code> folder and use them with the mixin as shown below. You can also reference them using <code>#{$icon-path}/my-icon.svg</code>.</p>

              <p>To add them to this page, you will need to add a new row to <code>.storybook/_icons.scss</code> and <code>./storybook/stories/Icons.stories.js</code>.</p>
            </div>
          </div>

        </section>

        <section class="sb__group">
          <div class="row">
            <div class="col">
              <h2 class="sb__header">Icon Usage</h2>
              <p>You can use icons listed in the icons directory using the following SCSS:</p>
              <code>
                <pre>
  .btn {
    &::after {
      @include icon(my-icon, 16px, 16px);
    }
  }
                </pre>
              </code>
            </div>
          </div>
        </section>
      </div>
    `,
}));
