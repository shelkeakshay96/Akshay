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
use Wm\Import\Api\ReaderInterface;
use Wm\Import\Api\MapperInterface;
use Wm\Import\Model\Importer;
use Magento\Customer\Model\CustomerFactory;
use Wm\Import\Helper\Data as Helper;

/**
 * ImporterTest Class Unit Test
 *
 * @category Console_Test
 * @package  Wm\Import\Test\Unit\Model
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class ImporterTest extends TestCase
{
    /**
     * Customer Factory
     *
     * @var CustomerFactory|MockObject
     */
    protected $customerFactory;

    /**
     * Reader Interface
     *
     * @var ReaderInterface
     */
    protected $reader;

    /**
     * Mapper Interface
     *
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * Data
     *
     * @var array
     */
    protected $data;

    /**
     * Customer Helper
     *
     * @var Helper
     */
    protected $helper;

    /**
     * Function Setup
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->customerFactory = $this->createPartialMock(
            CustomerFactory::class,
            ['create']
        );
        $this->reader = $this->createMock(ReaderInterface::class);
        $this->mapper = $this->createMock(MapperInterface::class);
        $this->helper = $this->createMock(Helper::class);
        $this->data = [
            0 => [
                'fname' => 'Akshay',
                'lname' => 'Shelke',
                'emailaddress' => 'myself.akshay.shelke@gmail.com'
            ]
        ];
    }

    /**
     * Test Execute
     *
     * @return void
     */
    public function testExecuteReader()
    {
        $this->reader->method('read')->willReturn($this->data);

        $filename = 'root/sample.json';
        $profile = 'sample-json';
        $this->assertEquals($this->reader->read($filename, $profile), $this->data);
    }

    /**
     * Test Execute
     *
     * @return void
     */
    public function testExecuteMapper()
    {
        $mapperData = [
            0 => [
                'firstname' => 'Akshay',
                'lastname' => 'Shelke',
                'email' => 'test@example.com'
            ]
        ];

        $this->mapper->method('map')->willReturn($mapperData);
        $this->assertEquals($this->mapper->map($this->data), $mapperData);
    }

    /**
     * Test create customer
     *
     * @return void
     */
    public function testCreateCustomer()
    {
        $customerModel = $this->getMockBuilder(
            \Magento\Customer\Model\Customer::class
        )
            ->disableOriginalConstructor()
            ->addMethods(['getFirstName'])
            ->onlyMethods(['getId'])
            ->getMock();

        $customerModel->method('getFirstName')
            ->willReturn('Akshay');
        $customerModel->method('getId')
            ->willReturn('1');

        $this->helper->method('createCustomerToSave')->willReturn($customerModel);
        $customer = $this->helper->createCustomerToSave($this->data);

        $this->assertEquals($customer->getFirstName(), 'Akshay');
        $this->assertEquals($customer->getId(), '1');
    }
}
