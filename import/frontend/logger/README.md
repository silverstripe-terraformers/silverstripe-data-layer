# TNZ Logger

This is a small utility library for managing logs across the consumer site.

It allows you to create prefixed logs, set log levels and it integrates with raygun

## Prefixed logging

the only export from the library is `createLogger`. This function takes a 'name' as its first and only argument. It returns an instance of [loglevel](https://github.com/pimterry/loglevel). Logs from this instance will be prefixed with the `name` supplied. For example:

```Javascript
import { createLogger } from '@tnz/logger';

const log = createLogger('MyLog');

log.warn('wags finger...');

// outputs: "MyLog: wags finger..."
```

## Log Levels and Debug mode

The logger's default level is 'warn' this means everything below warnings (info, debug etc) will not be logged to the console by default. You can set debug mode by including the `debug` url parameter (eg `myurl/?debug=1`).

💡 **hot tip** 💡 if you are in debug mode there will be a lot of messages but you can filter them using the browser's console filter. This combined with the log prefix means you should be able to narrow down the log messages to just the part of the application you want to see messages from.

## Raygun Integration

Error logs (eg `log.error`) will also be sent to Raygun. It will send via the [crash reporting api](https://raygun.com/documentation/product-guides/crash-reporting/api/). Currently we deliberately avoid using the [raygun4js package](https://github.com/MindscapeHQ/raygun4js) as its function is to try and log as much as possible - it will also intercept logging functions and mess with line numbers for logging calls.

We are only sending very basic error log information to raygun - it is intended as an alert rather than a full log.

## Global Dependencies

Some variables are needed for this module to sucessfully funtion. Ideally we re-factor these out but at time of writing it is very difficult to provide environment variables to the JS build (see the Cloud Build limitations) so we provide them as global variables instead.

- `BUILD_VERSION` is used to set the raygun client version (injected by webpack's define plugin)
- `TNZ.RaygunAPIkey` must be present on the page and contain a valid Raygun crash reporting API key. If not provided, requests to raygun will return 403
- `TNZ.Util.isDebugMode()` is used to determine the logging level - if `debug=1` is present in the url this will return `true`
