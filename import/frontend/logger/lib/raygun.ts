/**
 * Raygun integration for consumer site logging
 *
 * note we are not using the npm supplied raygun4js as it hijacks a lot of global functionality (eg console.log)
 * this leads to undesirable effects such as losing line numbers for logging. Instead we have a simple fetch
 * client to post raygun errors.
 *
 * @see https://raygun.com/documentation/product-guides/crash-reporting/api/
 */

import { RaygunMessage, RequestMethod } from "./types";

const raygunAPIUrl = "https://api.raygun.com/entries";
const raygunAPIKeyHeader = "X-ApiKey";
// @ts-ignore: Global var refer to the readme for details
const apiKey = window?.TNZ?.RaygunAPIkey;

export const sendErrorToRaygun = (e: Error) => {
  const queryEntries = window.URLSearchParams ? new URLSearchParams(window?.location?.search).entries() : [];
  const queryString = Object.fromEntries(queryEntries);

  // send a basic error message to raygun
  const body: RaygunMessage = {
    occurredOn: new Date().toISOString(),
    details: {
      client: {
        name: "tnz js logger",
        //@ts-ignore: global BUILD_VERSION provided by webpack define
        version: BUILD_VERSION,
      },
      error: {
        message: e.message,
        // only sending a basic stacktrace to raygun
        stackTrace: [
          {
            fileName: e.stack,
            lineNumber: 1, // fake line number as we're only doing basic alerting
          },
        ],
      },
      request: {
        hostName: window?.location?.hostname,
        url: window?.location?.href,
        httpMethod: RequestMethod.GET, // hard coding to GET until we need to add more sophisticated handling
        queryString,
        headers: {
          "User-Agent": window?.navigator?.userAgent,
        },
      },
      environment: {
        "browser-Version": window?.navigator?.userAgent,
      },
    },
  };

  // send to raygun
  fetch(raygunAPIUrl, {
    method: RequestMethod.POST,
    headers: {
      "Content-Type": "application/json",
      [raygunAPIKeyHeader]: apiKey,
    },
    mode: "cors",
    body: JSON.stringify(body),
  }).catch((e) => console.error(e));
};
