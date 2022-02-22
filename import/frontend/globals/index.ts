// These are the global definitions shared across the project that don't declare their own types
export type TNZGlobal =
  | {
      Util: {
        isDebugMode: () => boolean;
      };
    }
  | any;

// Set up window objects
window.TNZ = window.TNZ || ({} as any);

// Exported window objects
export const TNZ: TNZGlobal = window.TNZ;
