# Silverstripe Data Layer

Data Layer is a flexible data capture solution for your website.
The main benefits are as follows:

**Integration with third party vendors via adapters**

Implement your tracking in a standardised way (vendor independent) and choose your own adapter to accommodate any Data Layer compatible vendor such as Dynamic Tag Manager, Adobe Launch, Tealium and more.

**Keep tracking implementation light and separate from the logic of your website**

Use Data Layer configuration API to describe your tracking needs.
Use template decorators to inject tracking attributes into your templates without too much disruption.

**Clean separation of concerns for tracking data sources**

Data Layer encourages a clean hierarchy and responsibility of tracked components.
Each component can be very simple as Data Layer provides hierarchy capability for tracked events.
Events bubble up the component tree adding data from all components along the ancestor path.
This ensures that each event has rich tracking data without the need to burden the template render.

**Interaction catalogue**

Each tracking component has a unique key which can be used to easily identify the interaction you are looking for when searching in your code or requirements.
This makes communication with clients regarding tracking requirements much easier.

**Un-opinionated e2e tests**

Write your e2e tests without the concerns which Data Layer adapter is in use on your website.
Use e2e test adapter to assert your interactions to gain confidence in the longevity of your tests.
If the third party vendor changes, your e2e tests remain unchanged but still relevant.

## This module is still WIP

There are still some tasks that need to be completed before this module can be used.

* review and update backend setup (docs, extensibility, extension points, configuration, unit tests)
* review and update docs in general
* review and document how to implement your own Data Layer adapter
* provide an e2e test solution for Data Layer (we need a test Adapater capable of assertions)
* review and document page view event integration
* review and document adapter integration
* provide a way to generate component definitions in a format usable by JS (dev task which generates JSON or this can run on dev build or API which provides it?)
* provide a HOC for React integration
* provide a dev task of registered components (CMS report?)
