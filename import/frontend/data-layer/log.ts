import { createLogger } from "@tnz/logger";

// This provides us a scoped logger (you can filter messages with `Data Layer:`)
const log = createLogger("Data Layer");
export default log;
