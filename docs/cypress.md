## Acceptance Testing with Cypress

Cypress with cucumber is setup and ready to go. To run the tests you will need to:

- Ensure you have copied the cypress config (`cp cypress.env.json.dist cypress.env.json`) and set the base URL to your local URL.
- Run `yarn e2e`.

Alternatively, you can run selected tests via the Cypress Test Runner with the command `yarn cypress open`

### Subsites

You can specify a different URL to the one defined in config files when running the tests, e.g. `$ yarn e2e -e baseUrl=http://my-subsite.test/`
