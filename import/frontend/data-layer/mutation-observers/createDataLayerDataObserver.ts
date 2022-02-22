import { addComponent } from "../core";
import log from "../log";
import { AttributeObserver } from "./AttributeObserver";
import { getParentId } from "../helpers/getParentId";
import { decodeRecursive } from "../helpers/decodeRecursive";
import { debounce } from "@tnz/utils";
import { ObserverCreator } from "./types";

/**
 *
 * @returns Promsise
 */
export const createDataLayerDataObserver: ObserverCreator = (previousObserver) => {
  return new Promise<void>((resolve) => {
    let resolved = false;
    // fallback to a timeout in case no activity or a lot of activity on page load
    const timeout = setTimeout(() => {
      log.debug("data observer ready - timeout");
      resolve();
    }, 800);

    // Resolve after a period with no changes
    const isReady = debounce(() => {
      clearTimeout(timeout);

      if (!resolved) {
        // only log the first resolution
        log.debug("data observer ready - activity");
      } else {
        // we've already resolved so just stop
        return;
      }

      // @todo - it seems very difficult to return the observer object here but it would be ideal
      resolve();
    }, 200);

    function elementMatchedAttribute(element, attributeName) {
      log.debug(`matched an element with [${attributeName}]`);

      const id = element.getAttribute("data-layer-id");

      // We can't add items without IDs as they can't be linked to
      // the data layer in any meaningful way.
      if (!id) {
        return;
      }

      // Prepare the data
      const dataAttribute = element.getAttribute("data-layer-data");
      const data = JSON.parse(dataAttribute);
      // HTML special chars decode
      const decoded = decodeRecursive(data);
      decoded.ParentID = getParentId(element) ?? undefined;

      // Add it to the data layer
      addComponent(id, decoded);

      isReady();
    }

    const observer = new AttributeObserver(document.body, "data-layer-id", { elementMatchedAttribute });

    observer.start();
  });
};
