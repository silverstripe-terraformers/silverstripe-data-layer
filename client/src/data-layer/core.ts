export const getDataLayer = (): typeof window.silverstripeDataLayer => {
  return window.silverstripeDataLayer;
};

export type InputEvent = {
  event: string;
  component?: object;
  ParentID?: string;
  [key: string]: any;
};

export interface OutputEvent extends InputEvent {
  block?: object | null;
}

export type Data = {
  components: object;
  [key: string]: any;
};

// Add data to the data layer (this can be an event or data)
export const add = (data: InputEvent | Data): number => getDataLayer().push(data);

// Get data by path (only works if the helper has been loaded)
export const get = (path: string): InputEvent | Data | null => {
  const helper = window.Silverstripe?.Tracking?.dataLayerHelper;

  // The helper has not loaded, we can not query it
  if (!helper) {
    return null;
  }

  return helper.get(path) as any;
};

export const getComponent = (path: string): InputEvent | Data | null => {
  return get(`components.${path}`);
};

export const addComponent = (id: string, data: object) => {
  add({ components: { [id]: data } });
};
