import SwipeListener from "swipe-listener";
import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createSwipeEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "swipe",
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

const onSwipeListener = (event) => {
  const element = event.target;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);
  const swipeEvent = createSwipeEvent(eventData, ParentID);
  log.debug("swipe event", swipeEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", swipeEvent);
    notReadyEvents.push(swipeEvent);
    return;
  }

  add(swipeEvent);
};

export const createSwipeObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past swipe events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    const listener = SwipeListener(element);
    element.addEventListener("swipe", onSwipeListener);
  };

  const elementUnmatchedAttribute = (element) => {
    element.removeEventListener("swipe", onSwipeListener);
  };

  const observer = new AttributeObserver(document.body, "data-layer-swipeable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });

  observer.start();

  return previousObserver;
};
