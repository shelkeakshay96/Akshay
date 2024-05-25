<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * PHP Version 8.2
 *
 * @category Console
 * @package  Wm\Import\Model\Importer
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */

declare(strict_types=1);

namespace Wm\Import\Model\Importer;

use Wm\Import\Api\ReaderInterface;

/**
 * Read class from the source
 *
 * @category Console
 * @package  Wm\Import\Model\Importer
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class Reader implements ReaderInterface
{
    /**
     * Array Allowed Profiles
     *
     * @var array
     */
    protected $allowedProfiles;

    /**
     * Constructor
     *
     * @param array $allowedProfiles Parameter
     *
     * @return void
     */
    public function __construct(
        array $allowedProfiles
    ) {
        $this->allowedProfiles = $allowedProfiles;
    }

    /**
     * Read method
     *
     * @param string $filename Parameter
     * @param string $profile  Parameter
     *
     * @return array
     */
    public function read($filename, $profile = '')
    {
        $data   = [];
        $reader = $this->allowedProfiles[$profile] ?? null;
        if ($reader instanceof ReaderInterface) {
            $data = $reader->read($filename);
        }

        return $data;
    }
}
