import { getComponent, InputEvent, OutputEvent } from "../core";

const getBlock = (ParentID: string) => {
  const block = getComponent(ParentID);

  if (!block) {
    // We've reached the end of the tree and we have no block
    return null;
  }

  if (block.Type === undefined) {
    return null;
  }

  if (block.Type !== "components/block") {
    if (block.ParentID === undefined) {
      return null;
    }

    return getBlock(block.ParentID);
  }

  return block;
};

/**
 * Add event Ancestry
 *
 * this is added to events so their nested context is available. For example
 * a component may be a button within a video which sits within a block. Clicking
 * this button will send an event that includes the video and the block data in
 * the ancestry object
 *
 * @param {string} ParentID - parent ID of the item to get ancestry for
 * @param {object} components - the output component ancestry used recursively
 * @param {number} depth - recursion depth
 * @returns {object} - an object containing ancestory components labeled by Parent(n) where n = depth
 * @example
 * <caption>example return value</caption>
 * ```
 {
     "Parent1": {
          "Component": "content/blocks/carousel/video",
          "Type": "generic/video",
          "ID": "ss-Video-19",
          "Title": "TEST VIDEO",
          "Platform": "youtube",
          "VideoCode": "C0DPdy98e4c",
          "ParentID": "ss-CarouselBlock-49"
      },
      "Parent2": {
          "Component": "content/blocks/carousel",
          "Title": "Carousel",
          "Type": "components/block",
          "ID": "ss-CarouselBlock-49"
      }
  }
 * ```
 */
const getAncestry = (ParentID: string, components = {}, depth = 1): object => {
  const component = getComponent(ParentID);

  if (!component) {
    // This shouldn't happen as we were able to find parent ID, likely indicates an error
    return components;
  }

  const isTopLevel = component.Type === undefined || component.ParentID === undefined;
  // Top level is named differently to provide consistent access to top level component
  const ancestorKey = isTopLevel ? "TopParent" : `Parent${depth}`;

  components[ancestorKey] = component;

  if (isTopLevel) {
    // We've reached the end of the tree and we have no block
    return components;
  }

  components = getAncestry(component.ParentID, components, depth + 1);

  return components;
};

export const addEventContext = (event: InputEvent): OutputEvent => {
  if (event.event === undefined) {
    return event;
  }

  if (event.ParentID === undefined) {
    return event;
  }

  const block = getBlock(event.ParentID);
  const ancestry = getAncestry(event.ParentID);

  return {
    ...event,
    block,
    ancestry,
  };
};
