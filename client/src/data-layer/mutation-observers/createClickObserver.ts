import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createClickEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "click",
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

const onClickListener = (event) => {
  const element = event.currentTarget;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);
  const clickEvent = createClickEvent(eventData, ParentID);
  log.debug("click event", clickEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", clickEvent);
    notReadyEvents.push(clickEvent);
    return;
  }

  add(clickEvent);
};

/**
 * Create click observer
 *
 * will wait for any previous observers to be ready before adding events to the Data Layer
 * this is helpful to avoid race conditions with the DataObserver
 */
export const createClickObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past click events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    element.addEventListener("click", onClickListener);
  };

  const elementUnmatchedAttribute = (element) => {
    element.removeEventListener("click", onClickListener);
  };

  const observer = new AttributeObserver(document.body, "data-layer-clickable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });
  observer.start();

  // return previous observer promise
  return previousObserver;
};
