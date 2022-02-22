<script>
/**
 * Not that we're creating a mixin here rather than a component. This can
 * be applied to entire Vue apps or to specific components.
 *
 * Using a mixin means we can write whatever HTML we want and attach our functionality to it,
 * rather than using a standardised component and enforcing a template. Even with template
 * slots scoping data and methods using in our HTML to our component is difficult and hacky.
 *
 * eg.
 * export default {
 *  name: 'my-component',
 *  mixins: [Meganav],
 * };
 *
 * The above component will inherit everything defined here meaning we can use this
 * in many different ways.
 *
 * eg. We can also apply this mixin to an entire app
 *
 * new Vue({
 *  el: '.my-meganav',
 *  mixins: [Meganav],
 * });
 */
export default {
  data() {
    return {
      activeItem: null,
      closingItem: null,
      transitionEnabled: true,
      offsetHeight: "100%",
    };
  },
  methods: {
    toggle(item, immediate) {
      if (item === null) {
        this.close(null, immediate);
        return;
      }

      if (item === this.activeItem) {
        this.close(item, immediate);
      } else {
        this.open(item, immediate);
      }
    },

    open(item, immediate) {
      if (this.activeItem === item) {
        return;
      }

      if (immediate === true) {
        this.transitionEnabled = false;
      } else {
        this.transitionEnabled = true;
      }

      if (this.activeItem !== null) {
        this.close(item, true);
      }

      this.activeItem = item;
    },

    close(item, immediate) {
      if (this.activeItem === null) {
        return;
      }

      if (immediate === true) {
        this.transitionEnabled = false;
        this.activeItem = null;
        return;
      }

      this.transitionEnabled = true;

      // We need this to fade out the menu
      let shouldClose = item;
      if (shouldClose === null) {
        shouldClose = this.activeItem;
      }

      this.closingItem = shouldClose;
      this.activeItem = null;

      setTimeout(() => {
        if (this.closingItem === shouldClose) {
          this.closingItem = null;
        }
      }, 300);
    },

    checkIfFocused() {
      // We need to use setTimeout here to ensure events have been processed
      // this.$nextTick doesn't work here since it can happen before events are finished.
      setTimeout(() => {
        let focused;
        if (typeof this.focusSelector === "string") {
          focused = document.querySelectorAll(this.focusSelector);
        } else {
          focused = this.$el.querySelectorAll("a:focus");
        }

        if (focused.length === 0) {
          this.close(null, true);
        }
      });
    },
  },
  mounted() {
    this.$nextTick(() => {
      if (this.offsetElement !== undefined) {
        const el = document.querySelector(this.offsetElement);
        if (el instanceof HTMLElement) {
          const offset = el.offsetHeight + el.offsetTop;
          this.offsetHeight = `calc(100% - ${offset}px)`;
        }
      }
    });

    window.addEventListener("keydown", (event) => {
      if (event.defaultPrevented) {
        return;
      }

      /* eslint-disable */
      // eslint is flagging the tabbing here as wrong for some reason.
      switch (event.key) {
        case "Esc":
        case "Escape":
          if (this.activeItem) {
            this.close(this.activeItem);
          }
          break;
        case "Tab":
          this.checkIfFocused();
          break;
        default:
          break;
      }
      /* eslint-enable */
    });
  },
};
</script>
