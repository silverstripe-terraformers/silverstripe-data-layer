import { decode } from "html-entities";

/**
 * Decode HTML entities
 *
 * Object is recursively traversed and all string values are decoded
 * Object returned is a new deep copy, origin object is not modified
 *
 * @param {Object|string|number|Array} object
 * @returns {Object|string|number|Array}
 */
export const decodeRecursive = (object) => {
  if (Number.isInteger(object)) {
    // We don't need to cover integers as there is nothing to decode
    return object;
  }

  if (Array.isArray(object)) {
    // Arrays need to keep their structure as we don't want to transform them into objects
    return object.map((value) => decodeRecursive(value));
  }

  if (object && typeof object === "object") {
    const decodedEntries = Object.entries(object).map((item) => {
      const [key, value] = item;

      return [key, decodeRecursive(value)];
    });

    return Object.fromEntries(decodedEntries);
  }

  // Otherwise it's a string
  return decode(String(object));
};
