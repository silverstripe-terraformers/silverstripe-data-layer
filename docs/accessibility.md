# Accessibility
<p align="center">
<strong><a href="#generic-check-lists">Generic check list</a></strong>
|
<strong><a href="#in-depth-overview-check-list">In-depth overview check list check list</a></strong>
</p>

----

## Introduction
If you're looking for a basic intro into a11y (accessibility), there is an 
[accessibility](https://silverstripe.atlassian.net/l/c/3N11xwCm) workspace on confluence which includes
guides, links and findings.

## Check lists
Check list are broken down into two main lists. First list is a basic checklist of accessibility 
requirements that should be checked every pull request/ peer review. 
Secondly there is a more in-depth checklist that can be used per function 
e.g. if a pull request is a menu/navigation feature, check the navigation technical check list. 

### Generic check lists
- [ ] Check incorrect `role` usage (no explicit tags are using roles that are duplicate/redundant)
- [ ] Check incorrect semantic html tag usage (e.g. no `<button>` usage for anchors, or `<button>` 
usage made to look like anchors)
- [ ] Check images for decorative or contextual bias and respectively meet requirements of each type
- [ ] Check images have correct alt text property applied (decorative images should be empty `alt=””`)
- [ ] Check `aria-label` are required (and provide actual context!) 
- [ ] run accessibility audit tool extensions 
e.g. [Wave Accessibility tool](https://wave.webaim.org/extension/) or 
[Chrome extension axe](https://chrome.google.com/webstore/detail/axe/lhdoppojpmngadmnindnejefpokejbdd)
- [ ] inspect any elements with Firefox dev accessibility tool (provides more in-line a11y checks)
- [ ] Check text colours meet 
[WCAG contrast requirements](https://www.w3.org/WAI/WCAG21/Understanding/contrast-minimum.html)
 (use plugins e.g. Firefox dev tools / Axe)

### In-depth overview check list

<p align="center">
<strong><a href="#images---decorative-and-non-decorative">Images - decorative and non-decorative</a></strong>
|
<strong><a href="#links-and-navigation">Links and navigation</a></strong>
|
<strong><a href="#actions-and-dynamic-content">Actions and dynamic content</a></strong>
|
<strong><a href="#inputs-and-labelling">Inputs and labelling</a></strong>
</p>

----

#### Images - decorative and non-decorative
**Images:**
- [ ] Check if image is contextual (add context to the image with `aria-label` or hidden 
element/SR only classes).
- [ ] Check if image is decorative, should have no label or content properties applied .
(*NB: hex codes are “usually” ignored by screen reader tools*).
- [ ] Check pseudo elements are not being used for contextual imagery (no `::before` or `::after` in use).
- [ ] Check SVG images have appropriate meta descriptions and titles applied or are using `aria-labelledby`.
- [ ] Check images that are contextual have `alt` text applied.
- [ ] Check images that are decorative do not have `alt` text property applied.

> *Decorative images should have empty/null `alt=””` property and no `title=""` applied.*

**Backgrounds:**
- [ ] Check background image is not decorative (should be physical element e.g. `<img>`).
- [ ] Check if decorative background image has no labels or alt text applied.
- [ ] Check if decorative background image using physical element has no `title=""` property.
- [ ] Check if contextual background image requires `role=”img”`.

> *Do not specify explicit img role on `<img>` tags*

**Icons:**
- [ ] Check if decorative icon image has no labels or alt text applied.
- [ ] Check if icon image is contextual (add context to the icon with `aria-label` or hidden with
element/SR only classes).
- [ ] Check pseudo elements are not used for contextual icons (no `::before` or `::after` in use).

#### Links and navigation

**Link components:**
- [ ] Check external link has correct aria-labels e.g. `aria-labelledby`.
- [ ] Check VO/SR tools can read the link correctly and makes contextual sense (e.g. not “click here”).
- [ ] Check if any icon(s) are decorative or contextual e.g. does the icon need `aria-label`.
- [ ] Check component uses correct role/element e.g. links should always be `<a>` and actions should be `<button>`.
- [ ] Check if a ‘skip link’, is it targeted via ‘#fragment’ and does the target have `tabindex=”-1”` 
applied.
- [ ] Check if external links require a contextual icon or contextual text applied to the link.
- [ ] Check focus styles work on all elements.
- [ ] Check `aria-labelledby` is correctly used and is targeted to the correct parent element.

**Navigation components:**
- [ ] Check `aria-expanded` is applied ‘false’ and ‘true’ respectively on the container/selector element.
- [ ] Check aria-hidden is applied ‘false’ and ‘true’ respectively on the main navigation if a mobile 
navigation is available.
- [ ] Check multiple navigation landmarks have `role=”navigation”` applied and contextual `aria-label` 
e.g. `aria-label=”main”`.
  
  *NB: where possible always use `<nav>` for navigation containers*. 
- [ ] Check keyboard shortcuts work for controlling the menu e.g. <kbd>esc</kbd> key closes, <kbd>space</kbd> key opens etc

> *`role=”navigation”` is not required on explicit tags like `<nav>`. However older screen readers that 
> do not support HTML5 tags will require it.*

#### Actions and dynamic content

**Buttons:**
- [ ] Check if correct semantic element is in use for button (`<button>` should not be used for links).
- [ ] Check incorrect `aria-role` has been applied to any buttons.
- [ ] Check context has been applied to the action/button e.g. `aria-label="my contextual text"`.
- [ ] Check if button controls a tab and has `aria-controls` applied.

**Alerts/Notices:**
- [ ] Check if the alert has an `aria-landmark` set or uses `role=”region”`.
- [ ] Check if alert/notice has correct `aria-label` applied to the container.

**Dynamic content:**
- [ ] Check if `role="alert"` and `aria-live="polite"` are applied to the element.
- [ ] Check `aria-label` is applied if content text is not contextual.

#### Inputs and labelling

**Customised Select/Dropdown inputs**
- [ ] Check keyboard access works, e.g. <kbd>up</kbd>, <kbd>dwn</kbd> can select options.
- [ ] Check `aria-haspopup=”listbox”` to button/container.
- [ ] If a customised select box has any listen events set (e.g. onClick/KeyDown) then a set a contextual warning 
to the element, i.e. add descriptive text “Immediate effect upon selection” (use `aria-hidden` or visually hide with css) 
  on the element that has the event.

> NB: `<input>`/`<select>` elements do not require keyboard access to be setup because browsers have this functionality set by default.

**All input fields**
- [ ] Check `<fieldset>` and `<legend>` surrounds all form elements.
- [ ] Check `disabled` attribute is set, rather than `aria-disabled` (disabled attribute has better support).

**Iframes:**
- [ ] Check `<iframe>` uses `title="description of my iframe content"`.
