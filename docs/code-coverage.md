# Code coverage

Code coverage reports are generated through CircleCI. Reports are generated for jest, cypress and phpunit tests. There's also a "combined" report which merges jest + cypress tests to view full front-end app coverage. The full reports can be found in CircleCI Artifacts after each build. Additionally to this, codecov can be enabled on the project and will comment on Pull Requests:

 - Go to https://codecov.io/ and login with GitHub
 - Under the silverstripeltd account, find and enable your repository (May require additional access)
 - In the codecov settings for your repository, copy the Repository Upload Token and set it as `CODECOV_TOKEN` env variable in CircleCI
