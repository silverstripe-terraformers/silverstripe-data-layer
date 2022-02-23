<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

use InvalidArgumentException;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\DataLayer\DataLayerExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ValidationException;
use SilverStripe\View\ViewableData;

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
     * Mapping of components and their decorators
     * This mapping is meant to cover decorators that don't have standard source (can't be mapped to object)
     * For object / component mapping use @see DataLayerExtension::$data_layer_decorators
     * Mapping format:
     * ['component_key' => 'decorator_class_name']
     * @var array
     * @config
     */
    private static $decorators = [];

    public const TYPE_ATTRIBUTES = 'TYPE_ATTRIBUTES';

    /**
     * Process the component data based on provided decorator rules
     *
     * @param string $type the type of decoration (likely to support JSON in the future)
     * @param string $componentKey the component ID in the data-layer.yml
     * @param ViewableData|null $sourceObject the instance of an object if required
     * @param array|null $additionalProperties
     * @return DBField
     * @throws ValidationException
     */
    public function process(
        string        $type,
        string        $componentKey,
        ?ViewableData $sourceObject = null,
        ?array        $additionalProperties = null
    ): DBField {
        if ($type !== self::TYPE_ATTRIBUTES) {
            throw new InvalidArgumentException(
                sprintf(
                    'We only support attribute generation currently, not %s',
                    $type
                )
            );
        }

        if ($sourceObject !== null && !$sourceObject->hasExtension(DataLayerExtension::class)) {
            throw new ValidationException(
                sprintf('Source object is missing DataLayerExtension for component %s', $componentKey)
            );
        }

        $decorator = $this->getDecorator($componentKey, $sourceObject);

        return $decorator->getAttributes($additionalProperties);
    }

    /**
     * Determine which decorator to use for specified component
     *
     * @param string $componentKey
     * @param ViewableData|null $sourceObject
     * @return AbstractDecorator
     * @throws ValidationException
     */
    protected function getDecorator(string $componentKey, ?ViewableData $sourceObject = null): AbstractDecorator
    {
        // Object is available to act as a data source - check if there is object specific decorator available
        if ($sourceObject !== null) {
            $decorators = (array) $sourceObject->config()->get('data_layer_decorators');

            // We found a match is the source object decorators
            if (array_key_exists($componentKey, $decorators)) {
                $decoratorData = $decorators[$componentKey];

                // Source object contains mapping rules for this component
                // Create a fallback decorator as it can handle a simple field map specific to the source object
                if (is_array($decoratorData)) {
                    return FallbackDecorator::create($componentKey, $sourceObject);
                }

                if (!is_a($decoratorData, AbstractDecorator::class, true)) {
                    throw new ValidationException(
                        sprintf(
                            'Invalid source object decorator class for component %s',
                            $componentKey
                        )
                    );
                }

                // Create a decorator specified by the decorator rules
                return Injector::inst()->create($decoratorData, $componentKey, $sourceObject);
            }
        }

        // Fall back to the generic decorators which work even without the source object
        $decorators = (array) $this->config()->get('decorators');

        if (array_key_exists($componentKey, $decorators)) {
            $className = $decorators[$componentKey];

            if (!is_a($className, AbstractDecorator::class, true)) {
                throw new ValidationException(
                    sprintf('Invalid decorator class %s for component %s', $className, $componentKey)
                );
            }

            return Injector::inst()->create($className, $componentKey, $sourceObject);
        }

        // We couldn't find any specific rules so let's just use a fallback decorator
        return FallbackDecorator::create($componentKey, $sourceObject);
    }
}
