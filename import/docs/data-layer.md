---
title: Data layer
---
The data layer is the mechanism by which we share information about the site and interactions with its elements. This information is sent to the analytics suite (currently Adobe Analytics through Tealium) via a collector script (Tealium's utag). We are responsible for populating the data layer with relevant information and connecting it with the collector script.

## Contents
- [Initial Design](data-layer-design.md)
- [Initial Data](initial-data.md)
- [Integration](data-layer-integration.md)

## The problem
Traditionally tracking is achieved by using an analytics tool such as Adobe Launch or Google Tag Manager to select what HTML elements (e.g. classes or attributes) are meaningful and create 'rules' based on what HTML is present on a page. This works well for small sites as the analytics teams have a lot of flexibility to change things as they need.

With a larger site we start to hit the boundaries of how this approach scales. The analytics tool will start accumulating business logic, redundant code, unused code, third party scripts (which are unused), and inversely affect the site performance. This poses an even larger issue when the documentation on interactions is light/non-existent as it means regressions in tracking occur more often. This also means the process for creating new tracking functionality involves upskilling from all parties on what the tracking will look like (e.g. what the spec is)

## The solution
A documented, scalable data layer with consistent and generic APIs to interact with. Application developers have a clear and consistant method to implement tracking requirements and regressions are picked up early either through unit testing, e2e testing, or manual testing (where the tester is able to refer to a document with expected tracking details)

To build support for future integrations we're taking an approach where we've used an adapter pattern to allow us to adapt the data layer to third party services. 

## Data vs Event
We have both data and events coming into the data layer.

### Data
Data is objects which describe either the page or components on the page. When their added to the data layer we just update the data layer. We do not send forward those updates

## Events
These are the events that are being added to the data layer, they are identified by having the top level key `event`. When these are added we forward them onto the third party services

## Keys
These are keys that have been "taken". This means we're using them across the site and we shouldn't use them inside event data. They are as follows:
- `event`
- `site`
- `page`
