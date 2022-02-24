import { add } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { createClickEvent } from "./createClickObserver";
import { getParentId } from "../helpers/getParentId";

/**
 * WYSIWYG links are special in that we don't have a data component for them.
 * This is due to all the links existing and being stored in the database before
 * the data layer was introduced. To handle this separation we need to specifically
 * handle when they are interacted with. Therefore you'll see we define the type here
 * and pick specific pre-existing attribute off the links (e.g. `data-operatorname`)
 * Ensure documentation is updated when these are updated:
 * https://tourismnz.atlassian.net/wiki/spaces/SWPRJ/pages/2789736490/WYSIWYG+Link+Interaction
 */

type LinkEvent = {
  Type: string;
  Component: string;
  LinkHref: string;
  LinkTitle?: string | null;
  OperatorName?: string | null;
  EditorReferral?: boolean;
  ParentID?: string | null;
  LinkType?: string | null;
};

const getDataFromElement = (element): LinkEvent => {
  const data = {
    Type: "components/link",
    Component: "generic/link/wysiwyg",
    LinkHref: element.getAttribute("href"),
    OperatorName: element.getAttribute("data-operatorname"),
    LinkType: element.getAttribute("data-linktype"),
    EditorReferral: element.getAttribute("data-editorreferral"),
  } as LinkEvent;

  if (element.textContent) {
    data.LinkTitle = element.textContent;
  }

  if (data.LinkTitle === undefined && element.hasAttribute("title")) {
    data.LinkTitle = element.getAttribute("title");
  }

  return data;
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
  const clickEvent = createClickEvent(eventData, getParentId(element));
  log.debug("click event", clickEvent);

  if (!dataReady) {
    // store events that happen before data is ready
    log.debug("storing event for processing later", clickEvent);
    notReadyEvents.push(clickEvent);
    return;
  }

  add(clickEvent);
};

export const createWYSIWYGClickObserver = (previousObserver) => {
  // only add events to Data Layer if previous observers are ready
  previousObserver.then(() => {
    dataReady = true;
    // add previous events to the data-layer
    log.debug("adding past wysiwyg click events to Data Layer", notReadyEvents);
    notReadyEvents.forEach(add);
  });

  const elementMatchedAttribute = (element) => {
    const links = element.querySelectorAll("a");
    let linkCount = links.length;

    while (linkCount--) {
      const link = links[linkCount];
      link.addEventListener("click", onClickListener);
    }
  };

  const elementUnmatchedAttribute = (element) => {
    const links = element.querySelectorAll("a");
    let linkCount = links.length;

    while (linkCount--) {
      const link = links[linkCount];
      link.removeEventListener("click", onClickListener);
    }
  };

  const observer = new AttributeObserver(document.body, "data-layer-wysiwyg", {
    elementMatchedAttribute,
    elementUnmatchedAttribute,
  });
  observer.start();

  return previousObserver;
};
