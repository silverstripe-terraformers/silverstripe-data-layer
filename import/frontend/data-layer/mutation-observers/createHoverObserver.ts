import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createHoverEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "hover",
    component: data,
    ParentID,
  };
};

/**
 * Store events fired before previous observers are ready
 */
const notReadyEvents = [];

/**
 * Indicate if ready to add to Data Layer directly
 */
let dataReady = false;

const onHoverListener = (event) => {
  const element = event.currentTarget;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);
  const hoverEvent = createHoverEvent(eventData, ParentID);
  log.debug("hover event", hoverEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", hoverEvent);
    notReadyEvents.push(hoverEvent);
    return;
  }

  add(hoverEvent);
};

export const createHoverObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past hover events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    element.addEventListener("mouseenter", onHoverListener);
  };

  const elementUnmatchedAttribute = (element) => {
    element.removeEventListener("mouseenter", onHoverListener);
  };

  const observer = new AttributeObserver(document.body, "data-layer-hoverable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });
  observer.start();

  return previousObserver;
};
