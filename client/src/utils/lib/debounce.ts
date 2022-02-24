import { debounce as _debounce } from "lodash";
/**
 * Debounce function
 * Will make sure a function can only be called once within a wait period
 * this is a wrapper around lodash's debounce to match TNZ historical usage
 *
 * @param func
 * @param wait - time in ms
 * @param immediate - optional toggle to call at the start of the wait period
 * @returns
 */
export function debounce(func: Function, wait: number, immediate = false): Function {
  return _debounce(func, wait, { leading: immediate });
}
