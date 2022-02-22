import * as log from "loglevel";
import { Logger } from "loglevel";
import { sendErrorToRaygun } from "./lib/raygun";

/**
 * Takes in a name of the logger, this is what gets prepended to the logger.
 * Logs will not be output if we are not in debug mode
 *
 * Usage:
 * const log = createLogger('Example');
 * log.debug('test')
 * Would output:
 * Example: test
 *
 * For most packages you'll want to create a log file that returns an instance
 * of the logger like the following:
 * // log.ts
 * const log = createLogger('Example');
 * export log;
 *
 * // test.ts
 * import log from './log'
 * log.info('test')
 *
 * This is using loglevel: https://github.com/pimterry/loglevel
 */
export const createLogger = (name: string): Logger => {
  /**
   *  Create loglevel plugin
   *
   * the plugin has two functions:
   * 1. it adds a name prefix to the logs based on the name given to createLogger
   * 2. it sends any error messages to raygun
   *
   * Refer to the loglevel docs for the plugin syntax
   */
  const originalFactory = log.methodFactory;
  // @ts-ignore Library said to write to the read only prop :shrug:
  log.methodFactory = function (methodName, logLevel, loggerName) {
    const delegate = originalFactory(methodName, logLevel, loggerName);

    if (methodName !== "error") {
      // non-error logs are only logged to preserve log line references
      return delegate.bind(this, `${loggerName}: `);
    }

    return function () {
      // error logs also get sent to raygun
      // they have a stacktrace that includes original line number
      const messages = [`${loggerName}:`];
      let error;
      for (let i = 0; i < arguments.length; i++) {
        const arg = arguments[i];
        messages.push(arguments[i]);

        if (arg instanceof Error) {
          error = arg;
        }
      }

      // send error logs to raygun

      if (error) {
        sendErrorToRaygun(error);
      } else {
        sendErrorToRaygun(new Error(messages.join(" ")));
      }

      // call original function to avoid losing line number references in stack trace
      delegate.apply(undefined, messages);
    };
  };

  // call setLevel method in order to apply plugin
  log.setLevel(log.getLevel());

  // read url to set log level
  if (TNZ.Util.isDebugMode()) {
    // show all messages in debug mode
    log.enableAll();
  } else {
    // Lets only log warnings and above by default
    log.setLevel(log.levels.WARN);
  }

  return log.getLogger(name);
};
