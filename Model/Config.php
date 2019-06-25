<?php
/**
 * Copyright (c) 2018-2019 SKABAS LIMITED. All rights reserved.
 *
 * @license LICENSE.txt
 */

namespace Skabas\AutoSetInStock\Model;

use Skabas\AutoSetInStock\Api\Data\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 * @package Skabas\AutoSetInStock\Model
 */
class Config implements ConfigInterface
{
    /**
     * @var ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(ConfigInterface::CONFIG_PATH_ENABLED);
    }
}
