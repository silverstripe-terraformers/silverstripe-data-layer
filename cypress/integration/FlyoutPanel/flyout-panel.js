import { When } from 'cypress-cucumber-preprocessor/steps';
import Page from '../../page_objects/Page';

When('I click on the menu button', () => {
  cy.get(Page.menuButton).click();
});

When('I click on the background overlay', () => {
  cy.get(Page.flyoutPanelBackground).click({ force: true });
});

Then('the flyout panel should be open', () => {
  cy.get(Page.flyoutPanelContent).should('be.visible');
});

Then('the flyout panel should be closed', () => {
  cy.get(Page.flyoutPanelContent).should('not.be.visible');
});
