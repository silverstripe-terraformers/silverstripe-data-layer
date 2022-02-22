@sitemap
Feature: SitemapXml

  Scenario: Check the sitemap.xml
    Given I load the sitemap xml
    Then I load the first sitemap xml
    Then I can see a key page in the sitemap xml
    And I can not see the content page demo in the sitemap xml

