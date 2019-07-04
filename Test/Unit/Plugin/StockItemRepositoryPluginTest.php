<?php
/**
 * Copyright (c) 2018-2019 SKABAS LIMITED. All rights reserved.
 *
 * @license LICENSE.txt
 */

namespace Skabas\AutoSetInStock\Test\Unit\Plugin;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Skabas\AutoSetInStock\Plugin\StockItemRepositoryPlugin;

/**
 * Class StockItemRepositoryPluginTest
 * @package Skabas\AutoSetInStock\Test\Unit\Plugin
 */
class StockItemRepositoryPluginTest extends TestCase
{
    /**
     * @var StockStateProviderInterface|MockObject $stockStateProvider
     */
    private $stockStateProvider;

    /**
     * @var StockItemRepository|MockObject $stockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var StockItemInterface|MockObject $stockItem
     */
    private $stockItem;

    /**
     * @var StockItemRepositoryPlugin|MockObject $plugin
     */
    private $plugin;

    public function setUp()
    {
        $this->stockStateProvider = $this->getMockBuilder(StockStateProviderInterface::class)
            ->setMethods([ 'verifyStock' ])
            ->getMockForAbstractClass();

        $this->stockItemRepository = $this->getMockBuilder(StockItemRepository::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->stockItem = $this->getMockBuilder(StockItemInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getQty',
                'getManageStock',
                'getIsInStock',
                'setIsInStock',
                'setStockStatusChangedAutomaticallyFlag',
                'setLowStockDate'
            ])
            ->getMockForAbstractClass();

        $this->plugin = $this->getMockBuilder(StockItemRepositoryPlugin::class)
            ->setConstructorArgs([ $this->stockStateProvider ])
            ->setMethodsExcept([ 'beforeSave' ])
            ->getMock();
    }

    /**
     * @param array $parameters
     * @dataProvider dataProvider
     */
    public function testBeforeSave(array $parameters)
    {
        $this->stockStateProvider->expects($parameters['qty'] ? $this->once() : $this->never())
            ->method('verifyStock')
            ->willReturn($parameters['verify_stock_result']);

        $this->stockItem->expects($this->once())
            ->method('getQty')
            ->willReturn($parameters['qty']);
        $this->stockItem->expects($parameters['qty'] ? $this->once() : $this->never())
            ->method('getManageStock')
            ->willReturn($parameters['manage_stock']);
        $this->stockItem->expects($parameters['qty'] ? $this->once() : $this->never())
            ->method('getIsInStock')
            ->willReturn($parameters['is_in_stock']);

        $this->stockItem->expects($parameters['must_change_status'] ? $this->once() : $this->never())
            ->method('setIsInStock')
            ->willReturnSelf();
        $this->stockItem->expects($parameters['must_change_status'] ? $this->once() : $this->never())
            ->method('setStockStatusChangedAutomaticallyFlag')
            ->willReturnSelf();
        $this->stockItem->expects($parameters['must_change_status'] ? $this->once() : $this->never())
            ->method('setLowStockDate')
            ->willReturnSelf();

        $this->assertNull($this->plugin->beforeSave($this->stockItemRepository, $this->stockItem));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            'item_going_in_stock' => [
                [
                    'verify_stock_result' => true,
                    'qty' => 10,
                    'manage_stock' => true,
                    'is_in_stock' => false,
                    'must_change_status' => true
                ]
            ],
            'item_already_in_stock' => [
                [
                    'verify_stock_result' => true,
                    'qty' => 10,
                    'manage_stock' => true,
                    'is_in_stock' => true,
                    'must_change_status' => false
                ]
            ],
            'item_going_out_of_stock' => [
                [
                    'verify_stock_result' => false,
                    'qty' => 0,
                    'manage_stock' => true,
                    'is_in_stock' => true,
                    'must_change_status' => false
                ]
            ],
            'item_already_out_of_stock' => [
                [
                    'verify_stock_result' => false,
                    'qty' => 0,
                    'manage_stock' => true,
                    'is_in_stock' => false,
                    'must_change_status' => false
                ]
            ]
        ];
    }
}
