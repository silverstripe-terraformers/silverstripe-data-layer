import { get } from "../core";
import log from "../log";
import { decodeRecursive } from "./decodeRecursive";

const getDataFromElementAttributes = (element: Element) => {
  const id = element.getAttribute("data-layer-id");

  // We assume that it will have the parent ID if it's been added to the data
  // layer already, so we don't need to parse for one
  if (id) {
    const eventData = get(`components.${id}`);

    // Some components are added to DOM before we can add them to data layer so we need a fallback
    if (eventData !== undefined && eventData !== null) {
      return eventData;
    }
  }

  // Generic data with no id
  const dataAttribute = element.getAttribute("data-layer-data");
  if (!dataAttribute) return null;

  try {
    const data = JSON.parse(dataAttribute);
    if (!data) return null;
    // HTML special chars decode
    return decodeRecursive(data);
  } catch (e) {
    log.warn(e);
    return null;
  }
};

export const getDataFromElement = (element: Element) => {
  return getDataFromElementAttributes(element) ?? {};
};
