<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details. *
 *
 * PHP Version 8.2
 *
 * @category Console_Test
 * @package  Wm\Import\Test\Unit\Model
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */

declare(strict_types=1);

namespace Wm\Import\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverInterface;
use Wm\Import\Model\Importer\Reader\ReadJson;
use Magento\Framework\Filesystem;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * ReadJsonTest Class Unit Test
 *
 * @category Console_Test
 * @package  Wm\Import\Test\Unit\Model
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class ReadJsonTest extends TestCase
{
    /**
     * Directory List
     *
     * @var DirectoryList
     */
    protected $directory;

    /**
     * Json Helper
     *
     * @var JsonHelper;
     */
    protected $jsonHelper;

    /**
     * Function Setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->directory = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->addMethods(['getDriver'])
            ->getMock();

        $this->jsonHelper = $this->getMockBuilder(JsonHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test Execute
     *
     * @return void
     */
    public function testRead()
    {
        $json = '[
            {
                "fname": "Akshay",
                "lname": "Shelke",
                "emailaddress": "myself.akshay.shelke@gmail.com"
            }
        ]';
        $filename = 'sample.json';

        $driver = $this->getMockBuilder(DriverInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $driver
            ->expects($this->any())
            ->method('fileGetContents')
            ->willReturn($json);

        $this->directory
            ->expects($this->any())
            ->method('getDriver')
            ->willReturn($driver);

        $jsonTestData = $this->directory->getDriver()->fileGetContents($filename);
        $this->assertEquals($jsonTestData, $json);

        $readJson = $this->getMockBuilder(ReadJson::class)
            ->disableOriginalConstructor()
            ->setConstructorArgs(
                [
                    $this->directory,
                    $this->jsonHelper
                ]
            )
            ->setMethods(
                [
                    'read'
                ]
            )
            ->getMock();

        $data = [
            'fname' => 'Akshay',
            'lname' => 'Shelke',
            'emailaddress' => 'myself.akshay.shelke@gmail.com'
        ];
        $readJson->method('read')->willReturn($data);

        $jsonData = $readJson->read($filename);
        $this->assertEquals($jsonData, $data);
    }
}
