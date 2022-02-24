import { add, InputEvent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getDataFromElement } from "../helpers/getDataFromElement";
import { getParentId } from "../helpers/getParentId";
import { ObserverCreator } from "./types";

export const createChangeEvent = (data: object, ParentID?: string | null): InputEvent => {
  return {
    event: "change",
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

export const addAdditionalChangeEventData = (element, data) => {
  // Allow a custom name for the key which holds the changed value
  const keyAttribute = element.getAttribute("data-layer-changeable-key");
  const keyName = keyAttribute === null ? "changedValue" : keyAttribute;
  data[keyName] = element.value;

  // In case input is a <select> we also need to include the label
  if (element.tagName === "SELECT") {
    const labelName = keyAttribute === null ? "changedLabel" : `${keyAttribute}Label`;
    data[labelName] = element.options[element.selectedIndex].text;
  }

  // in case of radio or checkbox we can add checked status
  if (element.type === "checkbox" || element.type === "radio") {
    const labelName = keyAttribute === null ? "Checked" : `${keyAttribute}Checked`;
    data[labelName] = element?.checked;
  }
};

const onChangeListener = (event) => {
  const element = event.currentTarget;
  const eventData = getDataFromElement(element);
  const ParentID = eventData?.ParentID || getParentId(element);

  addAdditionalChangeEventData(element, eventData);

  const changeEvent = createChangeEvent(eventData, ParentID);
  log.debug("change event", changeEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", changeEvent);
    notReadyEvents.push(changeEvent);
    return;
  }

  add(changeEvent);
};

export const createChangeObserver: ObserverCreator = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past change events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    element.addEventListener("change", onChangeListener);
  };

  const elementUnmatchedAttribute = (element) => {
    element.removeEventListener("change", onChangeListener);
  };

  const observer = new AttributeObserver(document.body, "data-layer-changeable", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });

  observer.start();

  return previousObserver;
};
