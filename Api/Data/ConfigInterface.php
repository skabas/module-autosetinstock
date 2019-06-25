<?php
/**
 * Copyright (c) 2018-2019 SKABAS LIMITED. All rights reserved.
 *
 * @license LICENSE.txt
 */

namespace Skabas\AutoSetInStock\Api\Data;

/**
 * Interface ConfigInterface
 * @package Skabas\AutoSetInStock\Api\Data
 */
interface ConfigInterface
{
    const CONFIG_PATH_ENABLED = '';

    /**
     * @return bool
     */
    public function isEnabled(): bool;
}
