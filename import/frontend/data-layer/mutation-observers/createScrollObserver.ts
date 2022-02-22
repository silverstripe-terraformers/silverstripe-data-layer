import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createScrollEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "scroll",
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

/**
 * @TODO add debounce/throttling
 * this listener is very basic and may need throttling to avoid
 * creating too many messages but we do not have many implentations to
 * test this on yet
 */
const onScrollListener = (event) => {
  const element = event.currentTarget;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);
  const scrollEvent = createScrollEvent(eventData, ParentID);
  log.debug("scroll event", scrollEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", scrollEvent);
    notReadyEvents.push(scrollEvent);
    return;
  }

  add(scrollEvent);
};

/**
 * Create scroll observer
 *
 * will wait for any previous observers to be ready before adding events to the Data Layer
 * this is helpful to avoid race conditions with the DataObserver
 */
export const createScrollObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past scroll events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    element.addEventListener("click", onScrollListener);
  };

  const elementUnmatchedAttribute = (element) => {
    element.removeEventListener("click", onScrollListener);
  };

  const observer = new AttributeObserver(document.body, "data-layer-scrollable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });
  observer.start();

  // return previous observer promise
  return previousObserver;
};
