import { addComponent, getComponent } from "..";

export const getDataLayerData = (element) => {
  const elementId = element.getAttribute("data-layer-id");

  if (!elementId) {
    return null;
  }

  return getComponent(elementId);
};

export const updateDataLayerData = (element, updateDataCallback) => {
  const dataLayerData = getDataLayerData(element);

  if (dataLayerData === null) {
    return;
  }

  const elementId = element.getAttribute("data-layer-id");

  element.setAttribute("data-layer-data", JSON.stringify(updateDataCallback(dataLayerData)));
  addComponent(elementId, dataLayerData);
};
