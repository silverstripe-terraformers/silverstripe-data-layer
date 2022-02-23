---
title: Initial data
---

This is the data that is on the page when it loads, it's sent across to third party services as a `page_view` event.

## Adding data to all pages
You'll want to update `SiteTreeTrackingExtension::getPageTrackingData` to add in the additional data. Be aware that you'll need to apply some caution here. If you add a top level value of `event`, it is likely to be overwritten with another event

## Adding data to specific pages
Subclasses of Page can define `getAdditionalPageTrackingData(): array` to add items to the page data. The array will be recursively merged and override any default values

An example of this is:
```php
class PageExample extends Page
{
    public function getAdditionalPageTrackingData(): array
    {
        return [
            'url_segment' => $this->URLSegment,
        ];
    }
}
```
