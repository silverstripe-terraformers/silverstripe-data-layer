<?php

namespace SilverStripe\DataLayer\DataComponentDecorators;

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
    private static $decorators = [];

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
