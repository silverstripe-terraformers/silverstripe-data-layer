import { Adapter, registerAdapters } from "./adapters";
import { add, getComponent, addComponent } from "./core";
import { createDataLayerDataObserver } from "./mutation-observers/createDataLayerDataObserver";
import { createClickObserver } from "./mutation-observers/createClickObserver";
import { createChangeObserver } from "./mutation-observers/createChangeObserver";
import { createImpressionsObserver } from "./mutation-observers/createImpressionsObserver";
import { createWYSIWYGClickObserver } from "./mutation-observers/createWYSIWYGClickObserver";
import { getDataFromElement } from "./helpers/getDataFromElement";
import { getParentId } from "./helpers/getParentId";
import { createSubmitObserver } from "./mutation-observers/createSubmitObserver";
import { createSwipeObserver } from "./mutation-observers/createSwipeObserver";
import { createHoverObserver } from "./mutation-observers/createHoverObserver";
import { flow } from "lodash";

type DataLayerHelper = {
  (datalayer: Array<object>, settings: object): void;
  get: (path: string) => object | null;
};

declare global {
  interface Window {
    silverstripeDataLayer: Array<object>;
    DataLayerHelper: DataLayerHelper;
    Silverstripe: {
      Tracking: {
        dataLayerHelper: DataLayerHelper;
        adapterLoaded: Function | null;
        adaptersToLoad: Array<string>; // TODO: Move to internal
      };
    };
  }
}

window.Silverstripe.Tracking = window.Silverstripe.Tracking || ({} as any);

const createDataLayer = (adapters: Array<Adapter>) => {
  registerAdapters(adapters);

  // call observers in order - each may delay execution or adding to datalayer based on previous adapter state
  flow([
    createDataLayerDataObserver,
    createClickObserver,
    createChangeObserver,
    createWYSIWYGClickObserver,
    createImpressionsObserver,
    createSubmitObserver,
    createSwipeObserver,
    createHoverObserver,
  ])(new Promise<void>((resolve) => resolve()));
};

export { createDataLayer, add, getDataFromElement, getComponent, addComponent, getParentId };
