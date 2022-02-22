# Namespacing

Generally speaking, there are two commonly used conventions when it comes to namespacing:

- [Namespace by type](#namespace-by-type)
- [Namespace by feature](#namespace-by-feature)

There are pros and cons to both.

## Namespace by type

Namespacing by type is the typical convention that has been used in the past by Silverstripe developers. I suspect
everyone is already familiar with it, but just quickly, it is the convention whereby your namespaces matches the types
of classes in your project.

EG, in a given project we probably have:

- Controllers
- Models (which can likely also be split into different types)
- Extensions
- Services

A basic "type" namespace might look something like this:

```
- App /
    - Model /
        - Block /
            - TextBlock
            - CarouselBlock
            - etc
        - Page /
            - BlockPage
            - BlogPage
            - etc
    - Extension /
        - SiteConfigMenuExtension
        - etc
```

## Namespace by feature

### What is namespacing by feature vs type?

Traditionally, a lot of devs (ourselves included) namespace by class/object type (as described above).

The problem with this type of namespacing is that the files related to a feature are spread across many directories,
making it difficult to find all of the pieces of something. For example, this project has a migration piece, which means
that each `Block` also has a corresponding `HydrationService` and ` Result`. Namespacing by type would look something
like this:

```
- App /
    - Model /
        - Block /
            - TextBlock
            - CarouselBlock
            - ...
    - Service /
        - Hydration /
            - TextBlockHydrationService
            - CarouselBlockHydrationService
            - ...
        - Result /
            - TextBlockResult
            - CarouselBlockResult
            - ...
    ...
...
```

Compare this to namespacing by feature, where the primary concern is to keep all of the "pieces of the puzzle" as close
to each other as possible. EG:

```
App /
    - Elemental / (the namespace used for all features relating to Elemental)
        - Carousel / (the namepsace used for all features relating to the Carousel block)
            - Block
            - HydrationService
            - Result
        - Text / (the namepsace used for all features relating to the Text block)
            - Block
            - HydrationService
            - Result
        - ...
    ...
```

In this example, everything related to Elemental is under the `Elemental` directory, and then likewise each Block has
its own namespace with everything relating to it.

This makes it easier to find all of the pieces that you're looking for, for any particular feature.

### Deciding on your "feature" namespace

If you were to write a verbose title for your class (think SS3), where no namespacing was allowed, what would it be?

- ModelPickerUiExtension (an extension which can be applied to many different types of objects)
- ModelPickerApiExtension (an extension which can be applied to many different types of objects)
- RelationshipObject (a dataobject that is often reused)
- RelationshipHelper (a helper for that dataobject)
- CarouselBlock
- HeroBannerBlock
- TextBlock
- BaseElementPopulateDefaultsExtension (an extension only applied to a single dataobject)

It's likely that the desired namespace will follow a similar pattern to the verbose title that you came up with. Using
the examples above, this is how they might look in a feature namespace. **Note: This is a namespace tree, not a
directory tree, the `Page` and `PageController` do not have a namespace, but would live within your `app/` directory**.

```
- Page (the standard one that can't be namespaced)
- PageController (the standard one that can't be namespaced)
- App /
    - Relationship / (through object)
        - Object
        - Helper
    - ModelPicker /
        - Ui /
            - Extension
        - Api /
            - Extension
    - Elemental /
        - Page (this is BlockPage)
        - BaseElement /
            - PopulateDefaults /
                - Extension
        - Carousel /
            - Block (ideally... but as mentioned above, due to a bug, we have to name these uniquely)
        - HeroBanner
            - Block
        - Text /
            - Block
```

### Deciding on your table name

Similar to above. Decide what your verbose class name would be, and likely you should use that.

- CarouselBlock
- TextBlock

### This is just a guideline

The goal of namespacing by feature is first and foremost to make finding thing easier.
