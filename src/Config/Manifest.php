<?php

namespace SilverStripe\DataLayer\Config;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Resettable;
use SilverStripe\ORM\ValidationException;

/**
 * This is the core of the specifications, it creates each component as a @see ComponentDTO
 * which then allows you to interact with them via the `getById` which returns a ComponentDTO
 *
 * Caching:
 * - specifications use standard configuration API
 * - components are in-memory cached and loaded on demand
 */
class Manifest implements Resettable
{

    use Injectable;
    use Configurable;

    /**
     * Maximum depth of hierarchy tree for specifications
     *
     * @var int
     * @config
     */
    private static $max_hierarchy_depth = 100;

    /**
     * Tracking data specifications
     *
     * @var array
     * @config
     */
    private static $specifications = [];

    /**
     * In-memory cache for components
     *
     * @var ComponentDTO[]
     */
    private $cachedData = [];

    public function flushCachedData(): void
    {
        $this->cachedData = [];
    }

    public static function reset(): void
    {
        self::singleton()->flushCachedData();
    }

    /**
     * Find component by key
     * In-memory cached method call
     *
     * @param string $key
     * @return ComponentDTO|null
     * @throws ValidationException
     */
    public function getByKey(string $key): ?ComponentDTO
    {
        if (!array_key_exists($key, $this->cachedData)) {
            $this->composeComponent($key);
        }

        return $this->cachedData[$key];
    }

    /**
     * Composes the specified component and all of its ancestors
     *
     * @param string $key
     * @throws ValidationException
     */
    protected function composeComponent(string $key): void
    {
        $data = $this->getComponentData($key);
        $maxDepth = (int) $this->config()->get('max_hierarchy_depth');

        // Find inheritance hierarchy
        // Some components extend other components, so we need to process them in the right order
        // We will traverse this bottom-up to find the hierarchy path,
        // and then we will traverse top-down when composing components
        $toProcess = [
            [
                $key,
                $data,
            ],
        ];

        $hierarchy = [];
        $i = 0;

        while ($item = array_shift($toProcess)) {
            $i += 1;

            if ($i > $maxDepth) {
                throw new ValidationException(
                    sprintf('Hierarchy traversal exceeded the maximum depth for key %s', $key)
                );
            }

            [$componentKey, $componentData] = $item;

            // Add component key into the hierarchy path
            array_unshift($hierarchy, $item);

            if (!array_key_exists('extends', $componentData)) {
                // This component doesn't have any more hierarchy to follow, so we can end the traversal
                continue;
            }

            $parentKey = $componentData['extends'];

            if (array_key_exists($parentKey, $this->cachedData)) {
                // Parent component is already loaded,
                // so we can end the traversal as the rest of the path is already composed
                continue;
            }

            $parentData = $this->getComponentData($parentKey);

            // Push the parent component into the process list
            $toProcess[] = [
                $parentKey,
                $parentData,
            ];
        }

        // Compose components in the hierarchy path in the top-down order
        while ($item = array_shift($hierarchy)) {
            [$componentKey, $componentData] = $item;

            // Compose the component and store it in the in-memory cache
            $component = ComponentDTO::create($componentKey, $componentData);
            $this->cachedData[$componentKey] = $component;
            $parentKey = $component->getExtends();

            if ($parentKey === null) {
                // Component has no parent, so we can bail out here
                continue;
            }

            if (!array_key_exists($parentKey, $this->cachedData)) {
                // We should have the parent component available at this point as we're traversing top-down
                throw new ValidationException(
                    sprintf('Failed to find parent component %s for key %s', $parentKey, $componentKey)
                );
            }

            $parentComponent = $this->cachedData[$parentKey];
            $component->buildExtensions($parentComponent);
        }
    }

    /**
     * Get raw component data
     *
     * @param string $key
     * @return array|null
     * @throws ValidationException
     */
    protected function getComponentData(string $key): array
    {
        $specifications = (array) $this->config()->get('specifications');

        if (!array_key_exists($key, $specifications)) {
            throw new ValidationException(sprintf('Failed to find component data by key %s', $key));
        }

        $data = $specifications[$key];

        if (!is_array($data)) {
            throw new ValidationException(sprintf('Invalid component data for key %s', $key));
        }

        return $data;
    }
}
