<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Wm\Import\Api;

/**
 * Read data from the source
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
interface ReaderInterface
{
    /**
     * Read method
     *
     * @param string $filename
     * @param string $profile
     *
     * @return array
     */
    public function read($filename, $profile = '');
}
