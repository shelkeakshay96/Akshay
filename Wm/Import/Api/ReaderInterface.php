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

namespace Wm\Import\Api;

/**
 * Read data from the source
 *
 * @category Console
 * @package  Wm\Import\Api
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
interface ReaderInterface
{
    /**
     * Read method
     *
     * @param string $filename Parameter
     * @param string $profile  Parameter
     *
     * @return array
     */
    public function read($filename, $profile = '');
}
