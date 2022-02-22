import { Given, Then, And} from "cypress-cucumber-preprocessor/steps";
import SitemapXml from '../../page_objects/SitemapXml';

before(() => {
  /**
   * Make a request to retrieve the sitemap xml
   *
   * @param   {string}  url    the relative sitemap url; for example `sitemap.xml`
   * @param   {string}  alias  to be used in tests; for example `sitemap` is usable as `cy.get('@sitemap')`
   * @return  {void}
   */
  const getSitemap = (url, alias) => {
    cy.request({
      url,
      headers: {
        "Content-Type": "text/xml; charset=utf-8"
      },
    }).as(alias);
  };

  // get the sitemap root
  getSitemap(SitemapXml.root, 'root');
  // get the sitemap content
  getSitemap(SitemapXml.sitemap, 'sitemap');
});

Given('I load the sitemap xml', () => {
  // check the response is valid and contains a link to the sitemap content
  cy.get("@root").then((response) => {
    expect(response.status).to.eq(200);
    expect(response.body).to.contain(SitemapXml.sitemap);
  });
});

Then('I load the first sitemap xml', () => {
  // check the sitemap content loads successfully
  cy.get("@sitemap").then((response) => {
    expect(response.status).to.eq(200);
  });
});

Then('I can see a key page in the sitemap xml', () => {
  // check the sitemap content contains a link to the key page
  cy.get("@sitemap").then((response) => {
    expect(response.body).to.contain(SitemapXml.include);
  });
});

And('I can not see the content page demo in the sitemap xml', () => {
  // check the page with 'show in search' disabled is not in the sitemap
  cy.get("@sitemap").then((response) => {
    expect(response.body).to.not.contain(SitemapXml.exclude);
  });
});
