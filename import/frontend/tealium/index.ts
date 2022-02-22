import { Adapter, AdapterFunc } from "@tnz/data-layer/adapters";
import log from "./log";

type Tealium = {
  flattenObject: (argOne: object, argTwo: object) => object;
};

type UTag = {
  view: (event: object) => void; // Page view event
  link: (event: object) => void; // Generic event
};

type Event = {
  event: string;
  tealium_event: string;
};

declare global {
  var teal: Tealium;
  var utag: UTag;
}

/**
 * Parse relative link and add a new absolute link based on it
 * Assumes a fixed field name "LinkHref"
 *
 * @param value
 */
const parseRelativeLink = (value) => {
  try {
    return parseRelativeLinkRecursive(value);
  } catch (e) {
    // In case something goes wrong return original value (likely failing due to unsupported JS features)
    log.warn(e.message, value);
    return value;
  }
};

/**
 * Performs recursive traversal of the object and parses all instanced of "LinkHref" property
 *
 * @param value
 */
const parseRelativeLinkRecursive = (value) => {
  if (Number.isInteger(value)) {
    // We don't need to cover integers as there is nothing to parse
    return value;
  }

  if (Array.isArray(value)) {
    // Arrays need to keep their structure as we don't want to transform them into objects
    return value.map((item) => parseRelativeLinkRecursive(item));
  }

  if (value && typeof value === "object") {
    const extraProperties = [];
    const processed = Object.entries(value).map((data) => {
      const [key, item] = data;

      // Detect relative link, parse it and add a new absolute link property based on it
      if (key === "LinkHref") {
        const url = new URL(String(item), window.location.origin);
        extraProperties.push(["LinkHrefFull", url.toString()]);
      }

      return [key, parseRelativeLinkRecursive(item)];
    });

    return Object.fromEntries(processed.concat(extraProperties));
  }

  // Otherwise it's a string
  return value;
};

const flattenObject = (obj: object): object => {
  const tealium = window?.teal;

  if (!tealium || typeof tealium.flattenObject !== "function") {
    // If we can't flatten it then we return it as it was
    return obj;
  }

  // Tealium will flatten the actual object rather than cloning it, so we destruct it here
  // to make it immutable
  const toFlatten = { ...obj };

  return tealium.flattenObject(toFlatten, toFlatten);
};

export const createTealiumAdapter = (): Adapter => {
  return {
    id: "Tealium",
    adapterFunc: createTealiumListener(),
    hasLoaded: () =>
      window.teal !== undefined && window.utag !== undefined && typeof window.teal?.flattenObject === "function",
  };
};

/**
 * This will return a effectively a listener function what takes in
 * the model (data layer) and message (event) which it will then send to Tealium
 * This expects `window.teal` to exist
 */
export const createTealiumListener =
  (): AdapterFunc =>
  (model, message): void => {
    // This will happen when we add data to the data layer and we don't want to forward it to
    // tealium as it's not an event, this will happen fairly often as we add data for every component
    // therefore we just return early and don't log
    if (!message.event) {
      return;
    }

    const tealiumUtag = window?.utag;

    // Should we process the event as a page view?
    if (message.event === "page_view") {
      let event = flattenObject(model) as Event;
      event.tealium_event = message.event;
      log.debug(`view / ${event.tealium_event}`, event);
      tealiumUtag.view(event);

      return;
    }

    // We're processing a generic event
    const parsedMessage = parseRelativeLink(message);
    let event = flattenObject(parsedMessage) as Event;
    event.tealium_event = event?.event;
    log.debug(`link / ${event.tealium_event}`, event);
    tealiumUtag.link(event);
  };
