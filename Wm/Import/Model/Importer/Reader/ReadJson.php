<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * PHP Version 8.2
 *
 * @category Console
 * @package  Wm\Import\Model\Importer\Reader
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
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
 * @category Console
 * @package  Wm\Import\Model\Importer\Reader
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class ReadJson implements ReaderInterface
{
    /**
     * File system
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Directory Read
     *
     * @var Read;
     */
    protected $directory;

    /**
     * Json Helper
     *
     * @var JsonHelper;
     */
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem Parameter
     * @param JsonHelper $jsonHelper Parameter
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
     * @param string $filename Parameter
     * @param string $profile  Parameter
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
