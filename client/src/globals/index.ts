// These are the global definitions shared across the project that don't declare their own types
export type SilverstripeGlobal =
  | {
      Util: {
        isDebugMode: () => boolean;
      };
    }
  | any;

// Set up window objects
window.Silverstripe = window.Silverstripe || ({} as any);

// Exported window objects
export const Silverstripe: SilverstripeGlobal = window.Silverstripe;
