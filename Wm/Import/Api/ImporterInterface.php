<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Wm\Import\Api;

/**
 * Import data from the source
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
interface ImporterInterface
{
    /**
     * Execute method
     *
     * @param string $filename
     * @param string $profile
     *
     * @return void
     */
    public function execute($filename, $profile);
}
