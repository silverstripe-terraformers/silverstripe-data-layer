Feature: Flyout panel

Scenario: I can open and close the flyout panel on desktop
  Given I am on desktop
  And I load the home page
  When I click on the menu button
  Then the flyout panel should be open
  When I click on the background overlay
  Then the flyout panel should be closed

Scenario: I can open and close the flyout panel on mobile
  Given I am on mobile
  And I load the home page
  When I click on the menu button
  Then the flyout panel should be open
  When I click on the background overlay
  Then the flyout panel should be closed
