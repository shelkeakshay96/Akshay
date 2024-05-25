<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Wm\Import\Model\Importer;

use Wm\Import\Api\ReaderInterface;

/**
 * Read class from the source
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
class Reader implements ReaderInterface
{
    /**
     * @var array
     */
    protected $allowedProfiles;

    /**
     * Constructor
     *
     * @param array $allowedProfiles
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
     * @param string $filename
     * @param string $profile
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
