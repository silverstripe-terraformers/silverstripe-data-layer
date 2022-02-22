# CWP setup

In order to use the project skeleton on a CWP project, you can simply require the cwp recipe with composer.

```bash
$ composer require cwp/cwp-recipe-core
```

## Wātea theme

Sometimes projects require the watea theme as a base, firstly you'll need to install it (and additional dependencies) with composer.

```bash
$ composer require cwp/watea-theme cwp/cwp cwp/agency-extensions
```

Ensure the starter and watea theme are added to the `.gitignore` file, then add them both to your theme config as follows:

```yaml
# config.yml
SilverStripe\View\SSViewer:
  themes:
    - 'app'
    - 'watea'
    - 'starter'
    - '$default'
```

You can then setup `themes/app/src/app.scss` to use watea as a base for its css:

```scss
// themes/app/src/app.scss
@import 'variables';
@import '../../../watea/src/scss/main';
@import 'other-custom-files';
```

Note: you will need to do a `yarn install` in the starter theme directory to get the required dependencies for building the css.

You'll also want to adjust your template files to match the Wātea theme (Page.ss, Header.ss and Footer.ss) or you could also:
- Remove these templates:
    - `themes/app/templates/Layout/Page.ss`
    - `themes/app/templates/Includes/Footer.ss`
    - `themes/app/templates/Includes/Header.ss`
- Copy the html from `themes/watea/templates/Page.ss` to `themes/app/templates/Page.ss`. Make sure to keep the app css and js requirements from the original template. It should look something like this:
```html
<head>
    <!-- watea <head> html tags here -->

    <% if $IsDev %>
        <% require themedCSS("dist/app") %>
    <% else %>
        <% require themedCSS("dist/app.min") %>
    <% end_if %>
</head>
<body class="$ClassName
    <% if $SiteConfig.MainFontFamily %>theme-font-{$SiteConfig.MainFontFamily}<% end_if %>
    <% if $SiteConfig.HeaderBackground %>theme-header-{$SiteConfig.HeaderBackground}<% end_if %>
    <% if $SiteConfig.NavigationBarBackground %>theme-nav-{$SiteConfig.NavigationBarBackground}<% end_if %>
    <% if $SiteConfig.CarouselBackground %>theme-carousel-{$SiteConfig.CarouselBackground}<% end_if %>
    <% if $SiteConfig.FooterBackground %>theme-footer-{$SiteConfig.FooterBackground}<% end_if %>
    <% if $SiteConfig.AccentColor %>theme-accent-{$SiteConfig.AccentColor}<% end_if %>
    <% if $SiteConfig.TextLinkColor %>theme-link-{$SiteConfig.TextLinkColor}<% end_if %>">

    <!-- watea <body> html tags here -->

    <% if $IsDev %>
        <% require themedJavascript("dist/app") %>
    <% else %>
        <% require themedJavascript("dist/app.min") %>
    <% end_if %>
</body>
```

To fix the CSS issues with Bootstrap:
 - Remove all bootstrap and typography imports from `themes/app/src/scss/app.scss`
 - Remove `img` styles from `typography.scss` or remove the whole file if there's no custom typography styling
 
Adjust `Page` to extend from `BasePage` instead of SiteTree, and add a `HomePage` which extends from `BaseHomePage`. See the [CWP Installer](https://github.com/silverstripe/cwp-installer/tree/master/app/src) for how these should look.


### Wātea Plus (Optional)

This feature allows CMS users to adjust site fonts and colours. It requires CWP 2.x and is disabled by default.
Follow this [documentation](https://github.com/silverstripe/cwp-agencyextensions/blob/master/docs/en/01_Features/ThemeColors.md) to enable it.

For custom fonts and colors, add new ones to `config.yml`
```yaml
// app/_config/config.yml
SilverStripe\SiteConfig\SiteConfig:
  // Theme color picker enabled
  enable_theme_color_picker: true
  // Add new font
  theme_fonts:
    aleo: Aleo
  // Add new colors
  theme_colors:
    teal:
      Color: '#A4D3D3' // Adjusting the default teal color
    light-blue:
      Title: 'Light blue'
      CSSClass: 'light-blue'
      Color: '#C3E8FE'
    light-yellow:
      Title: 'Light yellow'
      CSSClass: 'light-yellow'
      Color: '#F8FDBF'
```

and `_variables.scss` to match the color in css. If the new colors are light, add them to `$custom-light-colors`
so when they are used as background, a dark grey font color will be used against it.

```scss
// themes/app/src/scss/_variables.scss

$custom-theme-fonts: (
  aleo: (
    'Aleo',
    'sans-serif'
  )
);
$custom-theme-colors: (
  'teal': #A4D3D3, // Adjusting existing teal color
  'light-blue': #C3E8FE,
  'light-yellow': #F8FDBF
);
$custom-light-colors: (
  'teal',
  'light-blue',
  'light-yellow'
);
```

There's a few options in the CMS that will only work with colours which contrast against white text and you need to add
your new light colors to this list of colors (white and light grey). You can extend `CWPSiteConfigExtension` and
update the colors in `updateCMSFields()`. See `CWPSiteConfigExtension::addThemeColorPicker()`.
