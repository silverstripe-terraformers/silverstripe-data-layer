# Tailwind CSS

https://tailwindcss.com

## Installation

### Add dependencies

```
$ yarn add tailwindcss@latest postcss@latest postcss-import@latest postcss-nested@latest autoprefixer@latest laravel-mix-tailwind@latest
```

- `tailwindcss`: The Tailwind library.
- `postcss`: PostCSS is pre-processor. It does not support SASS/etc, but our goal here it to not need/use any of that anyway.
- `postcss-import`: Add support for @import in our css files
- `postcss-nested`: Allows you to nest CSS rules similar to how you would with SASS/etc.
- `autoprefixer`: PostCSS plugin to parse CSS and add vendor prefixes to CSS rules.
- `laravel-mix-tailwind`: Add support for Tailwind to Laravel Mix.

#### You might also consider adding

- `postcss-custom-properties`: Add support for [CSS Custom Properties](https://www.w3.org/TR/css-variables-1/).
- `focus-visible` + `postcss-focus-visible`: Add support for `focus-visible` pseudo-class in CSS.

### Add PostCSS and Tailwind configs

Create new files in your project root.

`postcss.config.js`:
```
module.exports = {
  plugins: [
    require('tailwindcss'),
    require('autoprefixer'),
    require('postcss-import'),
    require('postcss-nested'),
    // If you added them:
    // require('postcss-custom-properties'),
    // require('postcss-focus-visible'),
  ],
};

```

`tailwind.config.js`:
```
module.exports = {
  purge: [
    './themes/app/templates/**/*.ss',
    './themes/app/src/**/*.vue',
    './themes/app/src/**/*.js',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [],
};

```

We've set ourselves up to purge any Silverstripe templates that are found within `themes/app/templates`, as well as any
.vue/.js files found within `themes/app/src`. You can add more directories here if need be.

### Update Webpack config

Open `webpack.mix.js`:

```
# Existing stuff
const mix = require('laravel-mix');
// eslint-disable-next-line import/no-extraneous-dependencies
const StyleLintPlugin = require('stylelint-webpack-plugin');

# New stuff
// Tailwind CSS
require('laravel-mix-tailwind');

# Existing stuff
...

mix.sass(`${srcFolder}/scss/app.scss`, distFolder)
  .sass('themes/app/src/scss/editor.scss', distFolder)
  .options({ processCssUrls: true });

# New stuff
mix.tailwind();

# Existing stuff
...
```

### Remove bootstrap

```
$ yarn remove bootstrap
```

### Remove references to bootstrap and our existing scaffolding

```
$ rm -rf themes/app/src/scss/components
$ rm -rf themes/app/src/scss/core
```

Open the following files.

- `themes/app/src/scss/app.scss`
- `themes/app/src/scss/editor.scss`

Remove any references to `@import '~bootstrap/*` or core/component includes.

### Add Tailwind base styles

Add the following to `app.scss`.

```
/**
* Base Tailwind styles
*/
@import 'tailwindcss/base';
@import 'tailwindcss/components';
@import 'tailwindcss/utilities';

```

## Storybook Installation

```
$ cd .storybook/
```

### Add dependencies

```
$ yarn add tailwindcss@latest postcss@latest postcss-nested@latest postcss-import@latest postcss-loader@latest
```

### Add Tailwind config

`tailwind.config.js`:
```
module.exports = require('../tailwind.config.js');

```

### Update Storybook Website config

`.storybook/webpack.config.js`:
```
const path = require('path');

// Export a function. Accept the base config as the only param.
module.exports = async ({ config, mode }) => {
  // `mode` has a value of 'DEVELOPMENT' or 'PRODUCTION'
  // You can change the configuration based on that.
  // 'PRODUCTION' is used when building the static version of storybook.

  // Make whatever fine-grained changes you need
  // config.module.rules.push({
  //   test: /\.css$/,
  //   loaders: ['style-loader', 'css-loader', 'postcss-loader'],
  //   include: path.resolve(__dirname, '../'),
  // });
  config.module.rules.push({
    test: /\.css$/,
    use: [
      {
        loader: 'postcss-loader',
        options: {
          postcssOptions: require('../postcss.config'),
        },
      },
    ],
    include: path.resolve(__dirname, "../")
  });

  // Return the altered config
  return config;
};
```

### Everything else

There is a lot in the default Storybook that will not be compatible with your new postcss loaders. EG: Anything with
BEM. You will need to go through the existing stories and update any SCSS in your vue files to be vanilla CSS.

There are also some stories like the TinySlider which are including styles from the library.

Or, you'll need to add the appropriate loaders.
