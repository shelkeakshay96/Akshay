<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Wm\Import\Model\Importer\Reader;

use Wm\Import\Api\ReaderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Read JSON data
 *
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
class ReadJson implements ReaderInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Read;
     */
    protected $directory;

    /**
     * @var JsonHelper;
     */
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Filesystem $filesystem,
        JsonHelper $jsonHelper
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->jsonHelper = $jsonHelper;
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
        $data     = [];
        $driver = $this->directory->getDriver();
        $jsonData = $driver->fileGetContents($filename);
        $data = $this->jsonHelper->jsonDecode($jsonData);

        return $data;
    }
}
