import { getDataFromElement } from "../index";

export const getParentId = (childElement?: Element): string | null => {
  // if a ParentID is set, bypass lookup
  const data = getDataFromElement(childElement);
  if (data.ParentID) {
    return data.ParentID;
  }

  // lookup ParentID in DOM
  const element = childElement?.parentElement;

  if (!element) {
    return null;
  }

  const id = element.closest("[data-layer-id]")?.getAttribute("data-layer-id");

  if (!id) {
    return null;
  }

  return id;
};
