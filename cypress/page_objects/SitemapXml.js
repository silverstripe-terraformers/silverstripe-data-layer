module.exports = {
  // the filename for the sitemap root xml
  // for example: mysite.test/sitemap.xml
  root: 'sitemap.xml',
  // the filename for the first sitemap content xml
  // for example: mysite.test/sitemap.xml/sitemap/SilverStripe-CMS-Model-SiteTree/1
  sitemap: 'sitemap.xml/sitemap/SilverStripe-CMS-Model-SiteTree/1',
  // choose a single page uri to check for in the sitemap xml
  // all published pages are include by default
  include: '/about-us/',
  // choose a single page uri to make sure is *not* in the sitemap xml
  // this is defined by `ShowInSearch` being set to false
  exclude: '/example-page-to-exclude-from-sitemap/',
};
