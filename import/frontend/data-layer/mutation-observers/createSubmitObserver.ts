import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createSubmitEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "submit",
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

const onSubmitListener = (event) => {
  const element = event.currentTarget;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);
  const submitEvent = createSubmitEvent(eventData, ParentID);
  log.debug("submit event", submitEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", submitEvent);
    notReadyEvents.push(submitEvent);
    return;
  }

  add(submitEvent);
};

export const createSubmitObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past submit events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    element.addEventListener("submit", onSubmitListener);
  };

  const elementUnmatchedAttribute = (element) => {
    element.removeEventListener("submit", onSubmitListener);
  };

  const observer = new AttributeObserver(document.body, "data-layer-submitable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });
  observer.start();

  return previousObserver;
};
