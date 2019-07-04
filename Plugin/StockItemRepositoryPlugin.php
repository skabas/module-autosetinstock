<?php
/**
 * Copyright (c) 2018-2019 SKABAS LIMITED. All rights reserved.
 *
 * @license LICENSE.txt
 */

namespace Skabas\AutoSetInStock\Plugin;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Skabas\AutoSetInStock\Api\Data\ConfigInterface;

/**
 * Class StockItemRepositoryPlugin
 * @package Skabas\AutoSetInStock\Plugin
 */
class StockItemRepositoryPlugin
{
    /**
     * @var StockStateProviderInterface $stockStateProvider
     */
    private $stockStateProvider;

    /**
     * @var ConfigInterface $config
     */
    private $config;

    /**
     * @param StockStateProviderInterface $stockStateProvider
     * @param ConfigInterface $config
     */
    public function __construct(
        StockStateProviderInterface $stockStateProvider,
        ConfigInterface $config
    ) {
        $this->stockStateProvider = $stockStateProvider;
        $this->config = $config;
    }

    /**
     * @param StockItemRepository $stockItemRepository
     * @param StockItemInterface $stockItem
     * @return null
     */
    public function beforeSave(StockItemRepository $stockItemRepository, StockItemInterface $stockItem)
    {
        if ($this->config->isEnabled() && $stockItem->getQty()) {
            $isInStock = $this->stockStateProvider->verifyStock($stockItem);
            if ($stockItem->getManageStock() && $isInStock && ! $stockItem->getIsInStock()) {
                $stockItem->setIsInStock(true)
                    ->setStockStatusChangedAutomaticallyFlag(true)
                    ->setLowStockDate(null);
            }
        }
        return null;
    }
}
