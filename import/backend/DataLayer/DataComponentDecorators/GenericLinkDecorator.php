<?php

namespace App\Tracking\DataLayer\DataComponentDecorators;

/**
 * Provides defaults for optional fields for links
 */
class GenericLinkDecorator extends AbstractDecorator
{
    protected function produceData(?array $additionalProperties = null): array
    {
        $optionalFields = [
            'ListingID',
            'OperatorName',
        ];

        // Add default fields with empty values in case they are missing
        foreach ($optionalFields as $field) {
            if (array_key_exists($field, $additionalProperties)) {
                continue;
            }

            $additionalProperties[$field] = '';
        }

        return parent::produceData($additionalProperties);
    }

    protected function shouldUseId(): bool
    {
        return false;
    }
}
