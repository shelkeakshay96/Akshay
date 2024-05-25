<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
 * @author Akshay Shelke <myself.akshay.shelke@gmail.com>
 */
class Data extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var ValidateCustomerData
     */
    private $validateCustomerData;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * Contructor
     *
     * @param Context                  $context
     * @param StoreManagerInterface    $storeManager
     * @param LoggerInterface          $logger
     * @param DataObjectHelper         $dataObjectHelper
     * @param CustomerInterfaceFactory $customerFactory
     * @param DataObjectProcessor      $dataObjectProcessor
     * @param ValidateCustomerData     $validateCustomerData
     * @param ScopeConfigInterface     $scopeConfig
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
     * @param array $data
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
        $data                   = array_merge($requiredDataAttributes, $data);
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
