<?php

namespace SilverStripe\DataLayer\Tests;

use SilverStripe\Core\Convert;
use SilverStripe\DataLayer\Config\Manifest;
use SilverStripe\DataLayer\DataComponentDecorators\DecoratorService;
use SilverStripe\DataLayer\DataComponentDecorators\ModelDecorator;
use SilverStripe\DataLayer\DataLayerExtension;
use SilverStripe\DataLayer\DataLayerField;
use SilverStripe\DataLayer\Tests\DataLayerField\BasicModel;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationException;

class DataLayerFieldTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'DataLayerFieldTest.yml';

    /**
     * @var string[]
     */
    protected static $extra_dataobjects = [
        BasicModel::class,
    ];

    protected static $required_extensions = [
        BasicModel::class => [
            DataLayerExtension::class,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupConfiguration();
    }

    public function testDataLayerFieldSerialisation(): void
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

    public function testDataLayerFieldEncoding(): void
    {
        $value = DataLayerField::create('DataLayerField')
            ->setComponentKey('test/component/block')
            ->addExtraProperties([
                'ID' => 'Test 123',
                'Title' => "How to make Doctor's sausage",
            ])
            ->getFieldForTemplate()
            ->forTemplate();

        $expectedData = [
            'Type' => 'components/block',
            'Component' => 'test/component/block',
            'ID' => 'Test 123',
            'Title' => "How to make Doctor's sausage",
            'Location' => 0,
        ];
        $expectedData = Convert::raw2att(json_encode($expectedData, JSON_FORCE_OBJECT));

        $this->assertEquals(
            sprintf('data-layer-data=\'%s\' ', $expectedData)
            . 'data-layer-id="Test 123"',
            $value
        );
    }

    /**
     * @throws ValidationException
     */
    public function testBasicModelDecorator(): void
    {
        /** @var BasicModel|DataLayerExtension $model */
        $model = $this->objFromFixture(BasicModel::class, 'model1');
        $dataAttributes = $model
            ->DataLayerAttributes('test/component/block')
            ->forTemplate();

        $expectedData = [
            'Type' => 'components/block',
            'Component' => 'test/component/block',
            'ID' => $model->getUniqueKey(),
            'Title' => 'Model1',
            'Location' => 10,
        ];
        $expectedData = Convert::raw2att(json_encode($expectedData, JSON_FORCE_OBJECT));

        $this->assertEquals(
            sprintf('data-layer-data=\'%s\' ', $expectedData)
            . sprintf('data-layer-id="%s"', $model->getUniqueKey()),
            $dataAttributes
        );
    }

    /**
     * @throws ValidationException
     */
    public function testValueOverride(): void
    {
        /** @var BasicModel|DataLayerExtension $model */
        $model = $this->objFromFixture(BasicModel::class, 'model1');
        $dataAttributes = $model
            ->DataLayerAttributes('test/component/block')
            ->addExtraProperties([
                'Location' => 123,
            ])
            ->forTemplate();

        $expectedData = [
            'Type' => 'components/block',
            'Component' => 'test/component/block',
            'ID' => $model->getUniqueKey(),
            'Title' => 'Model1',
            'Location' => 123,
        ];
        $expectedData = Convert::raw2att(json_encode($expectedData, JSON_FORCE_OBJECT));

        $this->assertEquals(
            sprintf('data-layer-data=\'%s\' ', $expectedData)
            . sprintf('data-layer-id="%s"', $model->getUniqueKey()),
            $dataAttributes
        );
    }

    private function setupConfiguration(): void
    {
        // Setup mock configuration
        DecoratorService::config()->set('decorators', [
            'test/component/block' => ModelDecorator::class,
        ]);

        Manifest::config()->set(
            'specifications',
            [
                'test/component/block' => [
                    'fields' => [
                        [
                            'key' => 'Type',
                            'value' => 'components/block',
                            'type' => 'Identifier',
                        ],
                        [
                            'key' => 'Component',
                            'value' => 'test/component/block',
                            'type' => 'Identifier',
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
                        [
                            'key' => 'Location',
                            'value' => 0,
                            'type' => 'Integer',
                        ],
                    ],
                ],
            ]
        );
    }
}
