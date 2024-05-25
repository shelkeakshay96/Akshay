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

use Wm\Import\Api\MapperInterface;

/**
 * Class mapper to map old and new columns
 *
 * @category Console
 * @package  Wm\Import\Model\Importer
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class Mapper implements MapperInterface
{
    /**
     * Array Mapping
     *
     * @var array
     */
    protected $mappings;

    /**
     * DataMapper constructor.
     *
     * @param array $mappings Parameter
     */
    public function __construct(
        array $mappings = []
    ) {
        $this->mappings = $mappings;
    }

    /**
     * Set mapping
     *
     * @param array $mappings Parameter
     *
     * @return void
     */
    public function setMappings(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Map method
     *
     * @param array $data Parameter
     *
     * @return void
     */
    public function map($data)
    {
        $result = [];

        foreach ($this->mappings as $field => $mapping) {
            if (is_array($mapping)) {
                $old     = isset($mapping['old']) ? $mapping['old'] : $field;
                $new     = isset($mapping['new']) ? $mapping['new'] : $field;
                $default = isset($mapping['default']) ? $mapping['default'] : null;
            } else {
                $new     = $field;
                $old     = $mapping;
                $default = null;
            }

            $origOld = null;
            if (array_key_exists($old, $data)) {
                $origOld = $data[$old];
            }

            if (array_key_exists($old, $data)) {
                $result[$new] = $data[$old];
            }

            if ((!isset($result[$new]) || $old === $new) && isset($default)) {
                $result[$new] = $default;
            }

            $data[$old] = $origOld;
        }

        return $result;
    }
}
