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
 * Map the columns from file to db
 *
 * @category Console
 * @package  Wm\Import\Api
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
interface MapperInterface
{
    /**
     * Map method
     *
     * @param array $data Parameter
     *
     * @return void
     */
    public function map($data);
}
