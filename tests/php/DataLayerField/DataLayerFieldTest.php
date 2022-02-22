<?php

namespace SilverStripe\DataLayer\Tests;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\DataLayer\Config\Manifest;
use SilverStripe\DataLayer\DataLayerField;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationException;

class DataLayerFieldTest extends SapphireTest
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::modify()->set(
            Manifest::class,
            'specifications',
            [
                'test/component/block' => [
                    'fields' => [
                        [
                            'key' => 'Type',
                            'value' => 'components/block',
                            'type' => 'String',
                        ],
                        [
                            'key' => 'Component',
                            'value' => 'test/component/block',
                            'type' => 'String',
                        ],
                        [
                            'key' => 'ID',
                            'type' => 'String',
                        ],
                        [
                            'key' => 'Title',
                            'value' => 'Default value',
                            'type' => 'String',
                        ],
                    ],
                ],
            ]
        );
    }

    public function testSerialisation(): void
    {
        $field = DataLayerField::create('DataLayerField')
            ->setComponentKey('test/component/block')
            ->addExtraProperties([
                'ID' => 'test 123',
            ]);

        $data = serialize($field->getFieldForTemplate());
        $this->assertNotNull($data, 'We expect a serialised value');

        $plainField = unserialize($data);
        $this->assertInstanceOf(DBHTMLText::class, $plainField, 'We expect a plain DB field');

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            'This field should never be serialised directly, '
            . 'use getFieldForTemplate() to get a plain field for serialisation'
        );

        serialize($field);
    }

    public function testEncoding(): void
    {
        $value = DataLayerField::create('DataLayerField')
            ->setComponentKey('test/component/block')
            ->addExtraProperties([
                'ID' => "How to make Doctor's sausage",
                'Title' => "How to make Doctor's sausage",
            ])
            ->getFieldForTemplate()
            ->forTemplate();

        $expectedData = [
            'Type' => 'components/block',
            'Component' => 'test/component/block',
            'ID' => 'How to make Doctor\'s sausage',
            'Title' => 'Default value',
        ];
        $expectedData = Convert::raw2att(json_encode($expectedData, JSON_FORCE_OBJECT));

        $this->assertEquals(
            sprintf('data-layer-data=\'%s\' ', $expectedData)
            . 'data-layer-id="How to make Doctor&#039;s sausage"',
            $value
        );
    }
}
