import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createImpressionsEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "impression",
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

const impressionEventListener = (event) => {
  const element = event.target;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);
  const impressionEvent = createImpressionsEvent(eventData, ParentID);
  log.debug("impression event", impressionEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", impressionEvent);
    notReadyEvents.push(impressionEvent);
    return;
  }

  add(impressionEvent);
};

export const createImpressionsObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past impression events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const intersectionObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        // We only want to log entries that are fully visible
        if (entry.intersectionRatio < 1) {
          return;
        }

        impressionEventListener(entry);

        // unobserve target after tracking as we only want one impression per page
        observer.unobserve(entry.target);
      });
    },
    { threshold: 1 }
  );

  const elementMatchedAttribute = (element) => {
    intersectionObserver.observe(element);
  };

  const elementUnmatchedAttribute = (element) => {
    intersectionObserver.unobserve(element);
  };

  const observer = new AttributeObserver(document.body, "data-layer-impressionable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });

  observer.start();

  return previousObserver;
};
