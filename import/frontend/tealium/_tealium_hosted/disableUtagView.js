/**
 * Scope       : Pre Loader
 * Execution   : n/a
 * Condition   : n/a
 * Description : Disable automatic utag.view() event that fires on page load. This allows the page load events to be sent from the datalayer listener
 */

// disable the automatic view call on utag.js, we want to fire it from the datalayer listener instead
// this should be registered as a javascript code extension in Tealium in the 'Preload' scope.

// Doc: https://docs.tealium.com/platforms/javascript/settings/
// https://community.tealiumiq.com/t5/Customer-Data-Hub/Tealium-Collect-and-Google-Tag-Manager-GTM-Setup-Guide/ta-p/17939
window.utag_cfg_ovrd = window.utag_cfg_ovrd || {};
window.utag_cfg_ovrd.noview = true;
