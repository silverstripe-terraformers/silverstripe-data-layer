# Vue Setup

The skeleton project comes ready with Vue installed. To start developing Vue components you'll simply need to update the
app.js file and wrap the page content so that Vue can do its stuff. These steps are detailed below.

## Update themes/mysite/src/js/app.js

```js
import Vue from 'vue';

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('app');

  if (el === undefined || el === null) {
    return;
  }

  new Vue({ // eslint-disable-line no-new
    el: '#app',
    components: { },
    data: { },
  });
});
```

## Wrap page content

```html
<div id="app">
    Page html goes here
</div>
```

Note: vue replaces the HTML element targeted (ie. #app) so don't apply this to the body tag.

## Uninstalling Vue dependencies

If you don't need Vue as part of your project you can remove it as follows.

````
$ yarn remove vue eslint-plugin-vue babel-preset-vue vuex
````

You can also remove `plugin:vue/essential` as well as the vue `overrides` section from `.eslintrc.json`:

```
  "extends": [
    "airbnb-base",
    "plugin:vue/essential"
  ],
  ...
  "overrides": [
    {
      "files": ["*.vue"],
      "rules": {
        "indent": "off",
        "vue/script-indent": ["error", 2, { "baseIndent": 1 }]
      }
    }
  ]
```

As well as removing vue linting from `package.json`

```
  "lint-js": "eslint --ext .js,.vue themes/app/src/js",
  "lint-scss": "stylelint themes/app/src/scss/**/*.scss themes/app/src/js/**/*.vue",
```

and `vue` from the eslint and stylelint config `webpack.mix.js`

```
  {
    enforce: 'pre',
    test: /\.(js|vue)$/,
    exclude: /node_modules/,
    loader: 'eslint-loader',
  },
  ...
  new StyleLintPlugin({
    context: 'themes/app/src/',
    files: ['**/*.{scss,vue}'],
  }),
```

## State Management with Vuex

Vuex is included and added to the main vue app instance by default. This is accessible to all components within the app using `this.$store`.

Most simple components will not need to use the store and will still have props passed down to them. However, when you find multiple components depending on each other, whether that's triggering the same event or rely on something being in a specific state (ie. is an overlay showing?) then the store should be used and the events/state managed centrally.

[Learn more about Vuex](https://vuex.vuejs.org/)

## Recommendations
You can huck `vue-tiny-slider` in for a slider

An example story with it:
```javascript
import { storiesOf } from "@storybook/vue3";
import VueTinySlider from "vue-tiny-slider";

// Default styles - remove this if necesary to display the styles of your project accurately
import "tiny-slider/src/tiny-slider.scss";

storiesOf("Components/Tiny Slider", module)
  .add("Standard", () => ({
    components: {
      "tiny-slider": VueTinySlider,
    },
    template: `<div style="max-width: 1200px; margin: 0 auto;">
      <tiny-slider :mouse-drag="true" :loop="false" items="1" gutter="20">
        <img src="http://placekitten.com/1200/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1201/500" alt="A kitten" />
        <img src="http://placekitten.com/1202/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1203/500" alt="A kitten" />
        <img src="http://placekitten.com/1204/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1205/500" alt="A kitten" />
      </tiny-slider>
    </div>`,
  }))

  .add("Without controls", () => ({
    components: {
      "tiny-slider": VueTinySlider,
    },
    template: `<div style="max-width: 1200px; margin: 0 auto;">
      <p style="margin-top: 20px;">Drag to change the slide.</p>
      <tiny-slider :mouse-drag="true" :loop="false" items="1" gutter="20" :controls="false">
        <img src="http://placekitten.com/1200/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1201/500" alt="A kitten" />
        <img src="http://placekitten.com/1202/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1203/500" alt="A kitten" />
        <img src="http://placekitten.com/1204/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1205/500" alt="A kitten" />
      </tiny-slider>
    </div>`,
  }))

  .add("With nav", () => ({
    components: {
      "tiny-slider": VueTinySlider,
    },
    template: `<div style="max-width: 1200px; margin: 0 auto;">
      <p style="margin-top: 20px;">Use the buttons to go direct to a slide.</p>
      <tiny-slider :mouse-drag="true" :loop="false" items="1" gutter="20" :nav="true" :controls="false">
        <img src="http://placekitten.com/1200/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1201/500" alt="A kitten" />
        <img src="http://placekitten.com/1202/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1203/500" alt="A kitten" />
        <img src="http://placekitten.com/1204/500" alt="A kitten" />
        <img src="http://placekitten.com/g/1205/500" alt="A kitten" />
      </tiny-slider>
    </div>`,
  }));

```
