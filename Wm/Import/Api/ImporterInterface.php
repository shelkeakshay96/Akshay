<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * PHP Version 8.2
 *
 * @category Console
 * @package  Wm\Import\Api
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */

declare(strict_types=1);

namespace Wm\Import\Api;

/**
 * Import data from the source
 *
 * @category Console
 * @package  Wm\Import\Api
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
interface ImporterInterface
{
    /**
     * Execute method
     *
     * @param string $filename Parameter
     * @param string $profile  Parameter
     *
     * @return void
     */
    public function execute($filename, $profile);
}
