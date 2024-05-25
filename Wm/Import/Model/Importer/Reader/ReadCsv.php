<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Wm\Import\Model\Importer\Reader;

use Wm\Import\Api\ReaderInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadFactory;

/**
 * Read CSV data
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
class ReadCsv implements ReaderInterface
{
    /**
     * DirectoryList
     *
     * @var DirectoryList
     */
    protected $dir;

    /**
     * ReadFactory
     *
     * @var ReadFactory
     */
    protected $directoryReadFactory;

    /**
     * Constructor
     *
     * @param DirectoryList $dir
     * @param ReadFactory   $directoryReadFactory
     */
    public function __construct(
        DirectoryList $dir,
        ReadFactory $directoryReadFactory,
    ) {
        $this->dir                  = $dir;
        $this->directoryReadFactory = $directoryReadFactory;
    }

    /**
     * Read CSV
     *
     * @param string $filename
     * @param string $profile
     *
     * @return array
     */
    public function read($filename, $profile = '')
    {
        $data          = [];
        $path          = $this->dir->getRoot();
        $directoryRead = $this->directoryReadFactory->create($path);
        $file          = $directoryRead->openFile($filename);
        $count         = 0;
        $header        = [];
        while (($row = $file->readCsv()) !== false) {
            if ($count == 0) {
                $header = $this->getHeader($row);
            } elseif (is_array($row) && count($row) > 1 && $count > 0) {
                $currentRow = [];
                foreach ($row as $key => $value) {
                    $currentRow[$header[$key]] = $value;
                }

                $data[] = $currentRow;
            }

            $count++;
        }

        return $data;
    }

    /**
     * Get Header value
     *
     * @param array $row
     *
     * @return array
     */
    private function getHeader($row)
    {
        $header = [];
        foreach ($row as $key => $value) {
            $header[$key] = $value;
        }

        return $header;
    }
}
