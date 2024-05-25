<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Wm\Import\Api;

/**
 * Map the columns from file to db
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
interface MapperInterface
{
    /**
     * Map method
     *
     * @param array $data
     *
     * @return void
     */
    public function map($data);
}
