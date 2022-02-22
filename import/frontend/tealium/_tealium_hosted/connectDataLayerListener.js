/**
 * Scope       : DOM Ready (run once)
 * Execution   : n/a
 * Condition   : n/a
 * Description : Datalayer Listener - Attach listener function using the DataLayer Helper
 */
if (TNZ && typeof TNZ.Tracking.adapterLoaded === "function") {
  TNZ.Tracking.adapterLoaded("Tealium");
}
