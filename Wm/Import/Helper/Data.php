<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * PHP Version 8.2
 *
 * @category Console
 * @package  Wm\Import\Helper
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */

declare(strict_types=1);

namespace Wm\Import\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\CustomerGraphQl\Model\Customer\ValidateCustomerData;
use Psr\Log\LoggerInterface;

/**
 * Customer Import Helper
 *
 * @category Console
 * @package  Wm\Import\Helper
 * @author   Akshay Shelke <myself.akshay.shelke@gmail.com>
 * @license  http://fsf.org GNU
 * @link     http://fsf.org
 */
class Data extends AbstractHelper
{
    /**
     * Store Manager Interface
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Logger Interface
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Scope Config Interface
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Customer Interface Factory
     *
     * @var CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * Validate Customer Data
     *
     * @var ValidateCustomerData
     */
    protected $validateCustomerData;

    /**
     * Data Object Processor
     *
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Contructor
     *
     * @param Context                  $context              Parameter
     * @param StoreManagerInterface    $storeManager         Parameter
     * @param LoggerInterface          $logger               Parameter
     * @param DataObjectHelper         $dataObjectHelper     Parameter
     * @param CustomerInterfaceFactory $customerFactory      Parameter
     * @param DataObjectProcessor      $dataObjectProcessor  Parameter
     * @param ValidateCustomerData     $validateCustomerData Parameter
     * @param ScopeConfigInterface     $scopeConfig          Parameter
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        DataObjectHelper $dataObjectHelper,
        CustomerInterfaceFactory $customerFactory,
        DataObjectProcessor $dataObjectProcessor,
        ValidateCustomerData $validateCustomerData,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->storeManager          = $storeManager;
        $this->logger                = $logger;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->customerFactory      = $customerFactory;
        $this->validateCustomerData = $validateCustomerData;
        $this->dataObjectProcessor  = $dataObjectProcessor;
        $this->scopeConfig           = $scopeConfig;
    }

    /**
     * Get customer object
     *
     * @param array $data Parameter
     *
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function createCustomerToSave($data)
    {
        $customerDataObject = $this->customerFactory->create();
        $store              = $this->storeManager->getStore();
        /*
         * Add required attributes for customer entity
         */
        $requiredDataAttributes = $this->dataObjectProcessor->buildOutputDataArray(
            $customerDataObject,
            CustomerInterface::class
        );
        $data = array_merge($requiredDataAttributes, $data);
        $this->validateCustomerData->execute($data);
        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $data,
            CustomerInterface::class
        );
        $customerDataObject->setWebsiteId($store->getWebsiteId());
        $customerDataObject->setStoreId($store->getId());

        return $customerDataObject;
    }
}
