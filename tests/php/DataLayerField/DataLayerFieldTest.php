<?php

namespace SilverStripe\DataLayer\Tests;

use Exception;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\DataLayer\Config\Manifest;
use SilverStripe\DataLayer\DataComponentDecorators\DataObjectDecorator;
use SilverStripe\DataLayer\DataComponentDecorators\DecoratorService;
use SilverStripe\DataLayer\DataComponentDecorators\FallbackDecorator;
use SilverStripe\DataLayer\DataLayerExtension;
use SilverStripe\DataLayer\DataLayerField;
use SilverStripe\DataLayer\TemplateHelper;
use SilverStripe\DataLayer\Tests\DataLayerField\BasicModel;
use SilverStripe\DataLayer\Tests\DataLayerField\MockDecorator;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\DataObject;
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

    /**
     * @var string[][]
     */
    protected static $required_extensions = [
        DataObject::class => [
            DataLayerExtension::class,
        ],
    ];

    /**
     * @param array $expected
     * @param array $config
     * @param array $extraData
     * @param string|null $modelIdentifier
     * @throws ValidationException
     * @throws Exception
     * @dataProvider genericDataLayerComponentProvider
     */
    public function testDataLayerAttributes(
        array   $expected,
        array   $config,
        array   $extraData = [],
        ?string $modelIdentifier = null
    ): void {
        foreach ($config as $class => $configData) {
            foreach ($configData as $key => $value) {
                Config::modify()->set($class, $key, $value);
            }
        }

        $components = array_keys($config[Manifest::class]['specifications']);
        $componentKey = array_shift($components);

        if ($modelIdentifier === null) {
            $dataLayerField = TemplateHelper::getGenericDataLayerAttributes($componentKey);
        } else {
            /** @var BasicModel|DataLayerExtension $model */
            $model = $this->objFromFixture(BasicModel::class, $modelIdentifier);
            $dataLayerField = $model->DataLayerAttributes('test/component/block');
        }

        $dataLayerAttributes = $dataLayerField
            ->addExtraProperties($extraData)
            ->forTemplate();

        $expectedID = array_key_exists('ID', $expected) ? $expected['ID'] : null;
        $expectedData = Convert::raw2att(json_encode($expected, JSON_FORCE_OBJECT));

        if ($expectedID !== null) {
            $this->assertContains(
                sprintf('data-layer-id="%s"', $expectedID),
                $dataLayerAttributes,
                'We expect a data layer ID to be present in the data layer attributes'
            );
        }

        $this->assertContains(
            sprintf("data-layer-data='%s'", $expectedData),
            $dataLayerAttributes,
            'We expect a data layer data to be present in the data layer attributes'
        );
    }

    /**
     * @param string $exceptionClass
     * @param string $message
     * @param array $config
     * @param array $extraData
     * @param string|null $modelIdentifier
     * @throws ValidationException
     * @dataProvider dataLayerErrorsProvider
     */
    public function testDataLayerAttributeErrors(
        string   $exceptionClass,
        string   $message,
        array   $config,
        array   $extraData = [],
        ?string $modelIdentifier = null
    ): void {
        foreach ($config as $class => $configData) {
            foreach ($configData as $key => $value) {
                Config::modify()->set($class, $key, $value);
            }
        }

        $components = array_keys($config[Manifest::class]['specifications']);
        $componentKey = array_shift($components);

        if ($modelIdentifier === null) {
            $dataLayerField = TemplateHelper::getGenericDataLayerAttributes($componentKey);
        } else {
            /** @var BasicModel|DataLayerExtension $model */
            $model = $this->objFromFixture(BasicModel::class, $modelIdentifier);
            $dataLayerField = $model->DataLayerAttributes('test/component/block');
        }

        $this->expectExceptionMessage($message);
        $this->expectException($exceptionClass);

        $dataLayerField
            ->addExtraProperties($extraData)
            ->forTemplate();
    }

    /**
     * @throws ValidationException
     */
    public function testDataLayerFieldSerialisation(): void
    {
        $this->setupBasicMockConfiguration();
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
        $this->setupBasicMockConfiguration();
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

    private function setupBasicMockConfiguration(): void
    {
        // Setup mock configuration
        DecoratorService::config()->set('decorators', [
            'test/component/block' => DataObjectDecorator::class,
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

    public function genericDataLayerComponentProvider(): array
    {
        return [
            'global component / identifiers only' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'global component / default value' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'ID' => 'Default ID',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'value' => 'Default ID',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'global component / extra data' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'ID' => 'Override ID',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'value' => 'Default ID',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'ID' => 'Override ID',
                ],
            ],
            'global component / decorator provided value' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Title' => 'Mock value',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    DecoratorService::class => [
                        'decorators' => [
                            'test/component/block' => MockDecorator::class,
                        ],
                    ],
                ],
            ],
            'model component / identifiers only' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                ],
                            ],
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / default value' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Heading' => 'Default heading',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Heading',
                                        'value' => 'Default heading',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / extra data' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Heading' => 'Override heading',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Heading',
                                        'value' => 'Default heading',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'Heading' => 'Override heading',
                ],
                'model1',
            ],
            'model component / implicit field mapping' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Title' => 'Model1',
                    'Location' => 10,
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                    [
                                        'key' => 'Location',
                                        'type' => 'Integer',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / explicit field mapping' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Title' => 'Model1',
                    'Location' => 10,
                    'AltTitle' => 'Model1',
                    'AltLocation' => 10,
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                    [
                                        'key' => 'Location',
                                        'type' => 'Integer',
                                    ],
                                    [
                                        'key' => 'AltTitle',
                                        'type' => 'String',
                                    ],
                                    [
                                        'key' => 'AltLocation',
                                        'type' => 'Integer',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    BasicModel::class => [
                        'data_layer_decorators' => [
                            'test/component/block' => [
                                'AltTitle' => 'getTestRelation.Title',
                                'AltLocation' => 'Location',
                            ],
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / generic decorator' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'CustomTitle' => 'Model1',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'CustomTitle',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    FallbackDecorator::class => [
                        'field_map' => [
                            'CustomTitle' => 'Title',
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / custom decorator' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Title' => 'Model1',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    BasicModel::class => [
                        'data_layer_decorators' => [
                            'test/component/block' => DataObjectDecorator::class,
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / explicit field mapping with decorator fallback' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Title' => 'Model1',
                    'Location' => 10,
                    'AltTitle' => 'Model1',
                    'AltLocation' => 10,
                    'CustomTitle' => 'Model1',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                    [
                                        'key' => 'Location',
                                        'type' => 'Integer',
                                    ],
                                    [
                                        'key' => 'AltTitle',
                                        'type' => 'String',
                                    ],
                                    [
                                        'key' => 'AltLocation',
                                        'type' => 'Integer',
                                    ],
                                    [
                                        'key' => 'CustomTitle',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    BasicModel::class => [
                        'data_layer_decorators' => [
                            'test/component/block' => [
                                'AltTitle' => 'getTestRelation.Title',
                                'AltLocation' => 'Location',
                            ],
                        ],
                    ],
                    FallbackDecorator::class => [
                        'field_map' => [
                            'CustomTitle' => 'Title',
                            // This field should be overridden by the mapping above
                            'AltTitle' => 'Location',
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / decorator provided value' => [
                [
                    'Type' => 'components/block',
                    'Component' => 'test/component/block',
                    'Title' => 'Mock value',
                ],
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    BasicModel::class => [
                        'data_layer_decorators' => [
                            'test/component/block' => MockDecorator::class,
                        ],
                    ],
                ],
                [],
                'model1',
            ],
        ];
    }

    public function dataLayerErrorsProvider(): array
    {
        return [
            'global component / default value' => [
                ValidationException::class,
                'Unable to find ID field on null class for test/component/block',
                [
                    Manifest::class => [
                        'specifications' => [
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
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'global component / decorator provided value' => [
                ValidationException::class,
                'Invalid decorator class WrongDecorator for component test/component/block',
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    DecoratorService::class => [
                        'decorators' => [
                            'test/component/block' => 'WrongDecorator',
                        ],
                    ],
                ],
            ],
            'model component / default value' => [
                ValidationException::class,
                'Unable to find Heading field on SilverStripe\DataLayer\Tests\DataLayerField\BasicModel class for test/component/block',
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Heading',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [],
                'model1',
            ],
            'model component / decorator provided value' => [
                ValidationException::class,
                'Invalid source object decorator class for component test/component/block',
                [
                    Manifest::class => [
                        'specifications' => [
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
                                        'key' => 'Title',
                                        'type' => 'String',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    BasicModel::class => [
                        'data_layer_decorators' => [
                            'test/component/block' => 'WrongDecorator',
                        ],
                    ],
                ],
                [],
                'model1',
            ],
        ];
    }
}
