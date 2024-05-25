<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * PHP Version 8.2
 *
 * @category Console
 * @package  Wm\Import\Model
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */

declare(strict_types=1);

namespace Wm\Import\Model;

use Wm\Import\Helper\Data as Helper;
use Psr\Log\LoggerInterface;
use Wm\Import\Api\ReaderInterface;
use Wm\Import\Api\ImporterInterface;
use Wm\Import\Api\MapperInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Importer Class to import customers
 *
 * @category Console
 * @package  Wm\Import\Model
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class Importer implements ImporterInterface
{
    /**
     * Logger Interface
     *
     * @var LoggerInterface
     */
    protected $logger;

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
     * Customer Repository
     *
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * Customer Helper
     *
     * @var Helper
     */
    protected $helper;

    /**
     * Importer Constructor
     *
     * @param LoggerInterface    $logger             Parameter
     * @param ReaderInterface    $reader             Parameter
     * @param MapperInterface    $mapper             Parameter
     * @param CustomerRepository $customerRepository Parameter
     * @param Helper             $helper             Parameter
     *
     * @return void
     */
    public function __construct(
        LoggerInterface $logger,
        ReaderInterface $reader,
        MapperInterface $mapper,
        CustomerRepository $customerRepository,
        Helper $helper
    ) {
        $this->logger             = $logger;
        $this->reader             = $reader;
        $this->mapper             = $mapper;
        $this->customerRepository = $customerRepository;
        $this->helper             = $helper;
    }

    /**
     * Execute method
     *
     * @param string $filename Parameter
     * @param string $profile  Parameter
     *
     * @return void
     */
    public function execute($filename, $profile)
    {
        try {
            $data = $this->reader->read($filename, $profile);
        } catch (\Exception $e) {
            $this->logger->error(
                __(
                    'Error while reading the customer import data:  %1',
                    $e->getMessage()
                )
            );
        }

        if (!$data) {
            $this->logger->info('The current batch does not contain any data');
            return;
        }

        $this->logger->info(__('%1 records to update', count($data)));

        foreach ($data as $itemData) {
            try {
                $record   = $this->mapper->map($itemData);
                $customer = $this->createCustomer($record);
                if ($customer->getId()) {
                    $this->logger->info(
                        __(
                            'Customer imported with email %1',
                            $customer->getEmail(),
                        )
                    );
                }
            } catch (AlreadyExistsException $e) {
                $this->logger->error(
                    __(
                        'Error occured while importing customer `%1` : %2',
                        $record['email'] ?? '',
                        $e->getMessage()
                    )
                );
                continue;
            }
        }

        $this->logger->info(
            __(
                '%1 records imported successfully',
                count($data)
            )
        );
    }

    /**
     * Create customer
     *
     * @param array $data Parameter
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface;
     */
    protected function createCustomer($data)
    {
        $customer = $this->helper->createCustomerToSave($data);

        return $this->customerRepository->save($customer);
    }
}
