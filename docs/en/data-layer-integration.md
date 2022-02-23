---
title: Data layer integration
---

How to integrate data layer into your project.

## How to identify components

The first step for data layer integration is to identify components which need to be tracked.
The obvious choice for these components are parts of your page which are interactive.
This usually doesn't cover all components though as there are situations where non-interactive components may need to be tracked as well.
This is for the purposes of exposing tracking data to interaction events.

Let's demonstrate this on a carousel.
Our example carousel has the following composition:

* carousel block (tracked data: title) - top level component, has many items
* carousel item (tracked data: slide number), has one image link
* carousel image link (tracked data: link href)

The interactive component for the click link event is carousel image link.
We do want to keep track of not just the data from the interactive component, but also the data from the components above it.
In this case we want the click link event to contain the following:

* block title
* slide number
* link href

This can be achieved by registering all the components into the data layer while only the carousel image link is interactive.
Click event will bubble up the tracked components hierarchy and will add the data from the registered components along the way.
This achieves our goal as all the required data will be collected.

This approach allows you to avoid complex data lookups on your components as the components can usually match your models.
The consequence of this, we can keep a clean separation of concerns and thus the code is easier to maintain.

## How to identify component data source

There are quite a few options when it comes to providing data for your components.
The simplest one is raw data via the template.
This is pretty straightforward, but it may not be suitable for cases where components require a lot of fields as we want to avoid cluttering the template with data lookups.

Components can use models or just objects (`ViewableData`) as data source.
Note that a single component can have different sources in different contexts, imagine this as a many-many relation between components and their data sources.

For example a link component can use page as data source but also a link object.
Looking at it from other direction, a single page can expose different data sets to different components.

To allow flexible mapping, this module provides several options for these cases.

### Field resolution priority

It's important to understand the field resolution priority in order to set up the right mapping rules.
Overall approach is that more specific rules override generic rules.

#### How field value is determined for components without source object

1. Identifier field type - special hard coded fields
2. Additional properties - raw data provided via template
3. Directly from decorator - decorator provides data directly
4. From default value - default value is part of the component definition

If none of the above yields any values an exception is thrown.

##### Decorator resolution

1. `DecoratorService` will try to find a decorator based on the `decorators` configuration
2. If no decorator is found `FallbackDecorator` will be used instead

#### How field value is determined for components from source object

1. Identifier field type - special hard coded fields
2. Additional properties - raw data provided via template
3. Directly from decorator - decorator provides data directly
4. Field mapping - decorator provides a map for source object fields and component fields
5. Object fallback - those source object fields which match component fields will be matched implicitly
6. From default value - default value is part of the component definition

If none of the above yields any values an exception is thrown.

##### Decorator resolution

1. Source object can have `data_layer_decorators` configuration which can have decorator specification
2. Source object can have `data_layer_decorators` configuration which can have explicit field mapping, `FallbackDecorator` will be used in such case
3. `DecoratorService` will try to find a decorator based on the `decorators` configuration
4. If no decorator is found `FallbackDecorator` will be used instead

##### Field mapping resolution

1. If `FallbackDecorator` is in use, it will have an explicit field map which will be used over any generic rules
2. Decorator in use provides `field_map` which is used to map properties which didn't match the rule above

#### Which data source to use in what situation?

**Additional properties**

Great for simple components which don't have any object data sources and have only a few fields.
For example: Link components with `href` and `title`.

**Decorator value**

Suitable for a group of objects of different type that need some custom data transformation.
For example: We have a page and a block and both need a transformation of their data based on some global state like a feature flag.

**Mapping from data source**

This is probably the most common one used as it covers field mapping between objects and components.
For example: Page has a `Heading` field and this needs to be mapped to Component field `Title`.

**Implicit mapping**

In case the object and component fields names match, you don't need an explicit mapping.
For example: Page has a `Title` field and this needs to be mapped to Component field `Title`.

**Default value**

Default values are handy in case use need hard coded values to be passed, or you just have a value which covers most cases.
The minority case can be overridden via additional properties feature (typically done via template).

# TODO the section below needs updating

## Component specifications

To register a component we need to configure our component manifest.
This is done via a standard Silverstripe configuration API property `Manifest` / `specifications`.

Example configuration:

```yaml
App\Tracking\DataLayer\Config\Manifest:
  specifications:
    listing/blocks/carousel:
      fields:
        - key: Component
          value: 'listing/blocks/carousel'
          type: String
        - key: Type
          value: 'components/block'
          type: String
        - key: ID
          type: String
        - key: Title
          type: String

    listing/blocks/carousel/item:
      interactions:
        - 'swipeable'
      fields:
        - key: Component
          value: 'listing/blocks/carousel/item'
          type: String
        - key: Type
          value: 'components/item'
          type: String
        - key: ID
          type: String
        - key: Location
          type: Integer
```

The example above will register two components: Carousel (non-interactive) and Carousel item (interactive).
Interactive components have the `interactions` property which registers event listeners as per specification.

### Specification syntax

Each component needs to have a component manifest key.
This key is used to reference other components when specifying component inheritance.
It is recommended to use the same value for this as is used in the Component field.

```yaml
App\Tracking\DataLayer\Config\Manifest:
  specifications:
      # component manifest key
      button/generic:
          interactions:
              - 'clickable'
          fields:
              - key: Type
                value: 'components/button'
                type: String
              - key: ID
                type: String
              - key: Title
                type: String
      # component manifest key
      content/blocks/mosaic/video/play:
          extends: 'button/generic' # <-- reference to other component using manifest key
          fields:
              - key: Component
                value: 'content/blocks/mosaic/video/play'
                type: String
```

All components should have the following as a minimal set of fields:

* Component
* Type
* ID or have a parent component with an ID

```yaml
App\Tracking\DataLayer\Config\Manifest:
  specifications:
    listing/blocks/carousel/item:
      interactions:
        - 'swipeable'
      fields:
        - key: Component
          value: 'listing/blocks/carousel/item'
          type: String
        - key: Type
          value: 'components/item'
          type: String
        - key: ID
          type: String
        - key: Location
          type: Integer
```

#### Default values for fields

You can choose to specify a default value for a field.
This value can be overridden in places such as templates if needed.

```yaml
App\Tracking\DataLayer\Config\Manifest:
  specifications:
    listing/blocks/carousel/item:
      fields:
        - key: Component
          value: 'listing/blocks/carousel/item'
          type: String
        - key: Type
          value: 'components/item'
          type: String
        - key: ID
          type: String
        # Field with default value
        - key: MyField
          value: 'default value for this field'
          type: String
```

### Supported interactions

Data layer supports most common interactions which should cover the majority of user activity scenarios.
Below is the list of supported interactions:

* change
* click
* hover
* impression
* scroll
* submit
* swipe

### Component inheritance

Components can inherit configuration from each other.
Example below shows a generic button component and a specific button which inherits from the generic one.

```yaml
App\Tracking\DataLayer\Config\Manifest:
  specifications:
      button/generic:
          interactions:
              - 'clickable'
          fields:
              - key: Type
                value: 'components/button'
                type: String
              - key: ID
                type: String
              - key: Title
                type: String
      content/blocks/mosaic/video/play:
          extends: 'button/generic'
          fields:
              - key: Component
                value: 'content/blocks/mosaic/video/play'
                type: String
```

Both fields and interactions are inherited.
Inheritance chain depth is limited but configurable via `max_hierarchy_depth`.

### Configuration file structure

You may be tempted to put all your component specifications into a single `yaml` file, but it is recommended to keep the configuration files well-structured as it makes the files easier to maintain.
All files need to be placed under your `_config` folder but the underlying file / folder structure can be arbitrary.

Example below shows a simple structure that separates generic components and specific components.

* `_config/`
* --> `DataLayer/`
* ----> `generic.yml`
* ----> `Content/`
* ------> `my-block.yml`

```yaml
App\Tracking\DataLayer\Config\Manifest:
  # generic.yml
  specifications:
      button/generic:
          interactions:
              - 'clickable'
          fields:
              - key: Type
                value: 'components/button'
                type: String
              - key: ID
                type: String
              - key: Title
                type: String
```

```yaml
App\Tracking\DataLayer\Config\Manifest:
  # my-block.yml
  specifications:
      content/blocks/mosaic/video/play:
          extends: 'button/generic'
          fields:
              - key: Component
                value: 'content/blocks/mosaic/video/play'
                type: String
```

Silverstripe configuration API will collect specifications from all your files and merges them together.
The order of your component definitions doesn't matter as any dependencies will be resolved during runtime.
It's also worth noting that component rendering will load components on demand, so only the components necessary for render will be loaded into memory during runtime.

## Template integration

Tracking data is expected to be added to your templates via helper methods.

### Page view event

This is a special event as there is no user interaction with any of the UI elements, hence it requires a different way of integrating compared to other events.

This script needs to be placed into the `<head>` of your page template.

```html
<script type="text/javascript">
  var trackingData = $PageTrackingData.RAW; // @see SiteTreeTrackingExtension::getPageTrackingData()

  window.silverstripeDataLayer.push(trackingData);

  <%-- This is based on the users cookie so can't be stored statically --%>
  window.silverstripeDataLayer.push({
      page: { user: { country: TNZ.Util.getUsersCountryCode() } },
  });

  window.silverstripeDataLayer.push({
    event: "page_view",
  });
</script>
```

### Standard events

All standard events will follow your interaction specifications from the `Manifest`.

#### Generic data layer attributes

This is the most basic way how to expose tracking data to your templates.

```html
<div $GenericDataLayerAttributes('listing/blocks/anchor-links')>Some content</div>
```

`$GenericDataLayerAttributes` template helper call is placed inside your element.
The only mandatory value which needs to be passed is the manifest key of your component.
This will output data layer tracking attributes into your element.

##### Adding / overriding properties

Some components will require additional properties to be passed inside the template.
This can be done by `AddProp()` template method.

Example below shows how to add a template variable `$Pos` (current index within a loop) as a tracking property.

```html
<div $GenericDataLayerAttributes('listing/blocks/anchor-links').AddProp('Location', $Pos)>Some content</div>
```

Adding property in this way will override any default or previous value as explicit population of tracking property has the highest priority.
Unfortunately, the whole method call with all the `AddProp()` chain has to be in a single line due to the parsing limitations of SS viewer.

Passing multiple values to the `AddProp()` will result in concatenation of these values.

```
AddProp('PropertyName', 'first-part-', 'second-part')
```

Example above will add tracking property `PropertyName` with the value of `first-part-second-part`.
This is handy in cases where the template needs to glue together values from different variables.

#### Decorators

Using the generic data layer attributes helper is straightforward, but sometimes it may be tedious to add many properties to it, especially when these properties are available on your models which are in your current scope within the template.
To make this case simpler, you can use decorators feature which allows you to specify the mapping between model and component properties.

Subclass `AbstractDecorator` to create your own decorator.
Example below shows a decorator for a `Link` model which maps `LinkTitle` and `LinkHref` model properties to `Title` and `Link` component fields.

```php
class PageLinkDecorator extends AbstractDecorator
{
    /**
     * @var array
     */
    private static $generic_values = [
        'LinkTitle' => 'Title',
        'LinkHref' => 'Link',
    ];
}
```

Once the decorator is available, we need t register it.
`DecoratorService` has a configurable property `decorators` which is mapping of component manifest keys and decorators.

Example below shows how to register your decorator.

```php
    private static $decorators = [
        'content/blocks/breadcrumbsheader/link' => PageLinkDecorator::class,
    ];
```

This is standard configuration API so configuration via yaml file will work as well.

Finally, we can put this decorator to good use in our template.
The code below is expected to be placed within the template scope of the `Link` model.

```html
<a $DataLayerAttributes('content/blocks/breadcrumbsheader/link') >
  My link
</a>
```

We still need to pass the component manifest key, but we no longer need to add any properties as they are automatically populated from the mapping provided by the decorator.
With that said, it's still possible to add custom properties via the template in this case if it's needed.
Example below uses a decorator to populate most properties but the ID property is populated via the template.

```html
<a $DataLayerAttributes('content/blocks/breadcrumbsheader/link').AddProp('ID', 'breadcrumbs') >
  My link
</a>
```
##### Advanced decorators

Further customisation can be achieved by overriding the `produceData()` method in your decorator.
You can place arbitrary code inside your method in case you need some very specific data transformation.
This approach should be used for exceptional cases though.

#### Recommended component hierarchy

Component hierarchy shouldn't be too shallow or too deep.
Balanced hierarchy example below:

```
Page - root component
--> Block - component with ID
----> Nested components (can be with or without IDs)
```

This recommendation contains Page as a root component.
you can achieve this by creating a Page decorator and placing the related template helper method call into the `<body>` element.

```html
<body $DataLayerAttributes('global/page')>
```

This will ensure that all events will contain data of the current page which is quite useful to identify which page is the event coming from.

### Data layer field

This feature is what makes the template helper method calls possible.
You don't need to worry about this field in most cases but there may be situations where you want to extract tracking attributes on the backend.

Example below shows how to extract HTML field that contains the tracking data:

```php
$trackingData = $block->DataLayerAttributes('listing/blocks/about')->getFieldForTemplate();
```

Example below shows how to extract raw string form of the tracking data:

```php
$trackingData = $block->DataLayerAttributes('listing/blocks/about')->forTemplate();
```

`DataLayerField` must not be serialised as it contains mutable data.
If you need to serialise it, get the immutable HTML field instead via `getFieldForTemplate()`.

## Rendering outside of templates

There are cases where HTML is created via JS and injected into the DOM.
Such cases are not supported out of the box, and it's up to you to integrate the tracking properties into the markup.

Example below shows how to integrate tracking data into markup generated in JS.

```javascript
  const dataLayerData = {
    Component: "content/blocks/search-results/autocomplete",
    Title: title,
    Href: url,
  };

  const link = `
  <a
    href="${url}"
    title="${title}"
    data-layer-data='${encode(JSON.stringify(dataLayerData))}'
    data-layer-clickable="1"
  >
    ${title}
  </a>`;
```

`data-layer-data` - contains the tracking data of this component.
`data-layer-clickable` indicates the type of interaction that the component needs to support.

Interactions / attributes mapping:

* change - data-layer-changeable
* click - data-layer-clickable
* hover - data-layer-hoverable
* impression - data-layer-impressionable
* scroll - data-layer-scrollable
* submit - data-layer-submitable
* swipe - data-layer-swipeable

It's your responsibility to ensure that this data is properly encoded.

### Direct fire call

In very rare instances you might need to fire the event directly.
Example below shows how to achieve this:

```javascript
// Import basic data layer functionality from the core library
import { add, getDataFromElement } from "@tnz/data-layer";
import { getParentId } from "@tnz/data-layer/helpers/getParentId";

// This allows us to add custom events (base method)
const addCustomEvent = (element, data) => {
    const elementData = getDataFromElement(element);
    const ParentID = elementData?.ParentID || getParentId(element);
    const eventData = {
        ...data,
        component: elementData,
        ParentID,
    };

    log.debug("custom event", eventData);
    add(eventData);
};

// Specific method for our event
export const addSearchEvent = (element, query, facets, total, resultsPerPage, page) => {
    const data = {
        event: "search",
        eventInfo: {
            query,
            facets,
            total,
            resultsPerPage,
            page,
        },
    };

    addCustomEvent(element, data);
};

// You need to add this method to the place in your JS code where you want the event to be triggered
addSearchEvent(originElement, query, facet ? [facet] : [], resultCount, resultsPerPage, page);
```

## Known issues

JS Chosen library breaks the event observers.
You need to manually hook these up in order for the tracking to work.

Example below shows how to achieve this:

```javascript
$(this.element)
  .find(".js-chosen")
  .chosen()
  .on("change", (e) => {
    // Data layer tracking: fire the event manually as Chosen JS interrupts standard fire mechanism
    addChangeEvent(e);
});
```
