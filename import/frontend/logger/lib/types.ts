export const enum RequestMethod {
  POST = "POST",
  GET = "GET",
  DELETE = "DELETE",
  OPTIONS = "OPTIONS",
  PATCH = "PATCH",
}

/**
 * Basic Raygun message suitable for submitting to the crash reporting API
 *
 * @see https://raygun.com/documentation/product-guides/crash-reporting/api/
 */
export type RaygunMessage = {
  // Date and time that the error occurred in ISO-8601 format
  occurredOn: string;
  details: {
    // The name of machine this error occurred on
    machineName?: string;
    // Client defined error grouping key. Must be 1-100 chars, ideally the result of a hash function e.g MD5
    groupingKey?: string;
    // The version number of your application.
    version?: string;
    // Information about the client library you are using for talking to the Raygun API
    client?: {
      // Name of the library
      name: string;
      // Version of the library
      version?: string;
      // URL where the client can be downloaded or viewed
      clientUrl?: string;
    };
    // Information about the error itself
    error: {
      // The error that caused the outer error.
      // Has all the same properties as the "error" object that contains it.
      innerError?: {
        [index: string]: any;
      };
      // Data contained in the error object
      data?: object;
      // The name of the error
      className?: string;
      // The error message
      message?: string;
      // The collection of stack traces.
      // The first one in the list should be the highest on the stack
      stackTrace: [
        {
          // The line number of this stack frame
          lineNumber: number;
          // The name of the class this stack frame is in
          className?: string;
          // The column of the file that this stack frame is in
          columnNumber?: number;
          // The name of the file this stack frame is in
          fileName?: string;
          // The name of the method this stack frame is in
          methodName?: string;
        }
      ];
    };
    // Information about the environment at the time of the error.
    // Each of these properties are optional
    environment?: {
      // The number of processors in the machine
      processorCount?: number;
      // The version of the operating system this app is running on
      osVersion?: number;
      // The width of the window
      windowBoundsWidth?: number;
      // The height of the window
      windowBoundsHeight?: number;
      // The width of the browser window
      "browser-Width"?: number;
      // The height of the browser window
      "browser-Height"?: number;
      // The width of the screen
      "screen-Width"?: number;
      // The height of the screen
      "screen-Height"?: number;
      // The scale of the screen
      resolutionScale?: number;
      // Color depth of the screen
      "color-Depth"?: number;
      // The orientation of the screen
      currentOrientation?: string;
      // The type of CPU in the machine
      cpu?: string;
      packageVersion?: string;
      // CPU architecture (ARMv8, AMD64, etc)
      architecture?: string;
      // Device manufacturer
      deviceManufacturer?: string;
      // Device model
      model?: string;
      // Total RAM in MB
      totalPhysicalMemory?: number;
      // Available RAM in MB
      availablePhysicalMemory?: number;
      // Total Virtual Memory in MB - RAM plus swap space
      totalVirtualMemory?: number;
      // Available Virtual Memory in MB
      availableVirtualMemory?: number;
      // Free disk space in GB
      diskSpaceFree?: number[];
      // Name of the device (phone name for instance)
      deviceName?: string;
      // Locale setting of the system
      locale?: string;
      // Number of hours offset from UTC
      utcOffset?: number;
      // The browser manufacturer
      browser?: string;
      // The browser name
      browserName?: string;
      // The full user agent string
      "browser-Version"?: string;
      // OS Name
      platform?: string;
    };
    // Tags that should be applied to the error
    // These will be searchable and filterable on the dashboard.
    tags?: string[];
    // Any custom data you would like to attach to this error instance.
    // You can search on data entered here.
    userCustomData?: {
      [index: string]: any;
    };
    // Information about the HTTP request being processed when the error occurred.
    // Only useful in a web environment obviously.
    request?: {
      // The hostName portion of the URL being requested.
      hostName: string;
      // The path portion of the URL being requested
      url: string;
      // The HTTP method used to request the URL (GET, POST, PUT, etc)
      httpMethod: RequestMethod;
      // The IP address of the client that initiated the request
      iPAddress?: string;
      // The query string portion of the URL
      queryString?: object;
      // The form parameters sent through with the request. Not form encoded.
      form?: {
        [index: string]: string;
      };
      // The HTTP Headers sent as part of the request
      headers?: {
        [index: string]: string;
        // The full user agent string - for some reason this needs adding here as well as in environment and isn't documented in the API
        "User-Agent"?: string;
      };
      // The raw request body. Don't include form values here.
      // See the notes for more information
      rawData?: string;
    };
    // Information about the response that will be sent back to the client.
    response?: {
      // The HTTP status code that will be sent back to the client
      statusCode: 500;
    };
    // Information about the user that caused the error
    user?: {
      // Unique identifier for the user
      identifier?: string;
      // Flag indicating if the user is anonymous or not
      isAnonymous?: boolean;
      // User's email address
      email?: string;
      // User's full name
      fullName?: string;
      // User's first name (what you would use if you were emailing them - "Hi {{firstName}}, ...")
      firstName?: string;
      // Device unique identifier. Useful if sending errors from a mobile device.
      uuid?: string;
    };
    // A trail of breadcrumbs leading up to this error
    breadcrumbs?: [
      {
        // Milliseconds since the Unix Epoch (required)
        timeStamp?: number;
        // The display level of the message (valid values are Debug, Info, Warning, Error)
        level?: string;
        // The type of message (valid values are manual, navigation, click-event, request, console)
        type?: string;
        // Any value to categorize your messages
        category?: string;
        // The message you want to record for this breadcrumb
        message?: string;
        // If relevant, a class name from where the breadcrumb was recorded
        className?: string;
        // If relevant, a method name from where the breadcrumb was recorded
        methodName?: string;
        // If relevant, a line number from where the breadcrumb was recorded
        lineNumber?: 156;
        // Any custom data you want to record about application state when the breadcrumb was recorded
        customData?: object;
      }
    ];
  };
};
