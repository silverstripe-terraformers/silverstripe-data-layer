/**
 * Observer creator function
 * must return a promise to allow dependent observers
 */
export type ObserverCreator = (pipeline: Promise<any>) => Promise<any>;
