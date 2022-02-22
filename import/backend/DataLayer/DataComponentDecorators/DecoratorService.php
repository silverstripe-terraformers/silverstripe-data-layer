<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

use App\Tracking\DataLayer\DataComponentDecorators\Listing\PageInteractionDecorator;
use App\Tracking\DataLayer\DataComponentDecorators\Listing\SectionDecorator;
use App\Tracking\DataLayer\DataComponentDecorators\Listing\SystemDealWallDecorator;
use InvalidArgumentException;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;

/**
 * This maps component ID's to decorators and their outputs
 *
 * This currently only supports decorating with attributes, in the future we
 * might need to update to support JSON
 */
class DecoratorService
{

    use Injectable;
    use Configurable;

    /**
     * @var array
     * @config
     */
    private static $decorators = [
        'content/blocks/breadcrumbsheader' => BlockDecorator::class,
        'content/blocks/breadcrumbsheader/link' => PageLinkDecorator::class,
        'content/blocks/carousel' => BlockDecorator::class,
        'content/blocks/carousel/item' => BlockDecorator::class,
        'content/blocks/carousel/video/close' => BlockDecorator::class,
        'content/blocks/carousel/video/play' => BlockDecorator::class,
        'content/blocks/crowdriff' => BlockDecorator::class,
        'content/blocks/cta-text' => BlockDecorator::class,
        'content/blocks/custom-deal' => BlockDecorator::class,
        'content/blocks/custom-deal/deal-link' => LinkDecorator::class,
        'content/blocks/destination-map' => BlockDecorator::class,
        'content/blocks/destination-map/legend' => BlockDecorator::class,
        'content/blocks/destination-map/link' => PageLinkDecorator::class,
        'content/blocks/destination-map/useful-link' => LinkDecorator::class,
        'content/blocks/dream-highlights' => BlockDecorator::class,
        'content/blocks/driving-text' => BlockDecorator::class,
        'content/blocks/featured-comparison' => BlockDecorator::class,
        'content/blocks/featured-comparison/item' => PageLinkDecorator::class,
        'content/blocks/filterable-deal' => BlockDecorator::class,
        'content/blocks/getting-here-map' => BlockDecorator::class,
        'content/blocks/getting-here-map/intro-link' => LinkDecorator::class,
        'content/blocks/highlights' => BlockDecorator::class,
        'content/blocks/highlights/item' => BlockDecorator::class,
        'content/blocks/image-navigation' => BlockDecorator::class,
        'content/blocks/image-navigation/link' => LinkDecorator::class,
        'content/blocks/itineraries-filter' => BlockDecorator::class,
        'content/blocks/itineraries-filter/filter' => BlockDecorator::class,
        'content/blocks/itineraries-filter/load-more' => BlockDecorator::class,
        'content/blocks/journey-collection-map' => BlockDecorator::class,
        'content/blocks/links-belt' => BlockDecorator::class,
        'content/blocks/links-belt/item' => LinkDecorator::class,
        'content/blocks/links-column' => BlockDecorator::class,
        'content/blocks/links-column/item' => LinkDecorator::class,
        'content/blocks/links-wall' => BlockDecorator::class,
        'content/blocks/links-wall/item' => LinkDecorator::class,
        'content/blocks/mediawall' => BlockDecorator::class,
        'content/blocks/mediawall/item' => BlockDecorator::class,
        'content/blocks/mediawall/nav-item' => BlockDecorator::class,
        'content/blocks/mediawall/video/close' => BlockDecorator::class,
        'content/blocks/mediawall/video/play' => BlockDecorator::class,
        'content/blocks/metservice' => BlockDecorator::class,
        'content/blocks/mosaic' => BlockDecorator::class,
        'content/blocks/mosaic/feature' => BlockDecorator::class,
        'content/blocks/mosaic/feature/link' => PageLinkDecorator::class,
        'content/blocks/mosaic/item' => BlockDecorator::class,
        'content/blocks/mosaic/nav/arrow' => BlockDecorator::class,
        'content/blocks/middle-earth-quiz' => BlockDecorator::class,
        'content/blocks/middle-earth-quiz/answer' => BlockDecorator::class,
        'content/blocks/middle-earth-quiz/question' => BlockDecorator::class,
        'content/blocks/region-highlights' => BlockDecorator::class,
        'content/blocks/rto-banner' => BlockDecorator::class,
        'content/blocks/rto-banner/region-link' => GenericLinkDecorator::class,
        'content/blocks/search-results' => BlockDecorator::class,
        'content/blocks/search-results/button' => BlockDecorator::class,
        'content/blocks/search-results/show-more' => BlockDecorator::class,
        'content/blocks/socialpins' => BlockDecorator::class,
        'content/blocks/socialpins/share' => GenericLinkDecorator::class,
        'content/blocks/spotlightmap' => BlockDecorator::class,
        'content/blocks/systemdealwall-image' => BlockDecorator::class,
        'content/blocks/systemdealwall' => BlockDecorator::class,
        'content/blocks/tabset' => BlockDecorator::class,
        'content/blocks/tabset/image-link' => GenericLinkDecorator::class,
        'content/blocks/tabset/title-link' => GenericLinkDecorator::class,
        'content/blocks/terms-conditions-text' => BlockDecorator::class,
        'content/blocks/text' => BlockDecorator::class,
        'content/blocks/text/quote' => BlockDecorator::class,
        'content/blocks/text/quote/button-link' => LinkDecorator::class,
        'content/blocks/text/quote/quote-link' => LinkDecorator::class,
        'content/blocks/thumbnail-icon' => BlockDecorator::class,
        'content/blocks/thumbnail-icon/item' => BlockDecorator::class,
        'content/blocks/thumbnail-list' => BlockDecorator::class,
        'content/blocks/thumbnail-list/item' => BlockDecorator::class,
        'content/blocks/travel-distance-calc' => BlockDecorator::class,
        'content/blocks/video' => BlockDecorator::class,
        'content/blocks/video/close' => BlockDecorator::class,
        'content/blocks/video/play' => BlockDecorator::class,
        'content/blocks/weather-chart' => BlockDecorator::class,
        'content/blocks/where-to-next' => BlockDecorator::class,
        'content/blocks/where-to-next/item' => BlockDecorator::class,
        'content/blocks/where-to-next/link' => LinkDecorator::class,
        'content/map/link/image' => PageLinkDecorator::class,
        'content/map/link/readmore' => PageLinkDecorator::class,
        'content/map/link/title' => PageLinkDecorator::class,
        'content/map/pin' => BlockDecorator::class,
        'generic/deal' => SystemDealDecorator::class,
        'generic/image' => BlockDecorator::class,
        'generic/video' => BlockDecorator::class,
        'generic/video/close' => BlockDecorator::class,
        'generic/video/play' => BlockDecorator::class,
        'global/footer/link' => LinkDecorator::class,
        'global/header/nav/featured' => LinkDecorator::class,
        'global/header/nav/heading' => LinkDecorator::class,
        'global/header/nav/item' => LinkDecorator::class,
        'global/notifications/notification' => BlockDecorator::class,
        'global/page' => PageTrackingDataDecorator::class,
        'global/search-banner/button' => FallbackDecorator::class,
        'global/search-banner/popular-search' => FallbackDecorator::class,
        'listing/blocks/about' => SectionDecorator::class,
        'listing/blocks/about/visit-website' => PageInteractionDecorator::class,
        'listing/blocks/additional-content' => SectionDecorator::class,
        'listing/blocks/anchor-links/anchor' => PageInteractionDecorator::class,
        'listing/blocks/business-intro' => SectionDecorator::class,
        'listing/blocks/carousel' => SectionDecorator::class,
        'listing/blocks/contact-and-pricing' => SectionDecorator::class,
        'listing/blocks/contact-and-pricing/book-website' => PageInteractionDecorator::class,
        'listing/blocks/contact-and-pricing/contact' => PageInteractionDecorator::class,
        'listing/blocks/contact-and-pricing/covid-clean' => PageInteractionDecorator::class,
        'listing/blocks/contact-and-pricing/qualmark' => PageInteractionDecorator::class,
        'listing/blocks/contact-and-pricing/trip-advisor' => PageInteractionDecorator::class,
        'listing/blocks/contact-and-pricing/visit-website' => PageInteractionDecorator::class,
        'listing/blocks/covid-clean/find-out-more' => PageInteractionDecorator::class,
        'listing/blocks/custom-features' => SectionDecorator::class,
        'listing/blocks/custom-opening-times' => SectionDecorator::class,
        'listing/blocks/custom-pricing' => SectionDecorator::class,
        'listing/blocks/custom-services' => SectionDecorator::class,
        'listing/blocks/disclaimer' => SectionDecorator::class,
        'listing/blocks/facilities-and-features' => SectionDecorator::class,
        'listing/blocks/facilities-and-features/visit-website' => PageInteractionDecorator::class,
        'listing/blocks/highlights' => SectionDecorator::class,
        'listing/blocks/language-support' => SectionDecorator::class,
        'listing/blocks/logo-with-link' => SectionDecorator::class,
        'listing/blocks/logo-with-link/logo' => PageInteractionDecorator::class,
        'listing/blocks/opening-times' => SectionDecorator::class,
        'listing/blocks/physical-address' => SectionDecorator::class,
        'listing/blocks/pricing-and-conditions' => SectionDecorator::class,
        'listing/blocks/social' => SectionDecorator::class,
        'listing/blocks/social/book-now' => PageInteractionDecorator::class,
        'listing/blocks/social/contact' => PageInteractionDecorator::class,
        'listing/blocks/social/visit-website' => PageInteractionDecorator::class,
        'listing/blocks/socialpins/share' => PageInteractionDecorator::class,
        'listing/blocks/spotlight-map' => SectionDecorator::class,
        'listing/blocks/spotlight-map/open-larger-map' => PageInteractionDecorator::class,
        'listing/blocks/summary' => SectionDecorator::class,
        'listing/blocks/system-deals' => SystemDealWallDecorator::class,
        'listing/blocks/things-to-do' => SectionDecorator::class,
        'listing/blocks/trip-advisor-block' => SectionDecorator::class,
        'listing/blocks/useful-information' => SectionDecorator::class,
    ];

    public const TYPE_ATTRIBUTES = 'TYPE_ATTRIBUTES';

    /**
     * @param string $type the type of decoration (likely to support JSON in the future)
     * @param string $componentKey the component ID in the data-layer.yml
     * @param DataObject|null $dataObject the instance of an object if required
     * @param array|null $additionalProperties
     * @return DBField
     */
    public function process(
        string $type,
        string $componentKey,
        ?DataObject $dataObject = null,
        ?array $additionalProperties = null
    ): DBField {
        $decorator = $this->getDecorator($componentKey, $dataObject);

        if ($type !== self::TYPE_ATTRIBUTES) {
            throw new InvalidArgumentException(sprintf(
                'We only support attribute generation currently, not %s',
                $type
            ));
        }

        return $decorator->getAttributes($additionalProperties);
    }

    private function getDecorator(string $componentKey, ?DataObject $dataObject = null): AbstractDecorator
    {
        $decorators = (array) $this->config()->get('decorators');
        $missingDecoratorMapping = !array_key_exists($componentKey, $decorators);

        if ($missingDecoratorMapping && $dataObject !== null) {
            throw new InvalidArgumentException(sprintf(
                'The component used (%s) does not have a mapping in %s::decorators',
                $componentKey,
                static::class,
            ));
        }

        if ($missingDecoratorMapping) {
            return FallbackDecorator::create($componentKey, $dataObject);
        }

        $className = $decorators[$componentKey];

        return Injector::inst()->create($className, $componentKey, $dataObject);
    }
}
