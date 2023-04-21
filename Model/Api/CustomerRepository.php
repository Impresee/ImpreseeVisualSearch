<?php

namespace ImpreseeAI\ImpreseeVisualSearch\Model\Api;

use ImpreseeAI\ImpreseeVisualSearch\Api\CustomerRepositoryInterface;
use ImpreseeAI\ImpreseeVisualSearch\Api\ResponseCustomerInterfaceFactory;
use \Magento\Customer\Model\Session;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

/**
 * Class ProductRepository
 */
class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     *  To load client_code
     * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
     */
    public $codesHelper;
    /**
     * Obtain customer session
     * @var Session
     */
    private $customerSession;
    /**
     * @var ResponseCustomerInterfaceFactory
     */
    private $responseItemFactory;

    /**
     * @param Session $customerSession
     * @param CodesHelper $codesHelper
     * @param ResponseCustomerInterfaceFactory $responseFactory
     */
    public function __construct(
        Session $customerSession,
        CodesHelper $codesHelper,
        ResponseCustomerInterfaceFactory $responseFactory
    ) {
        $this->customerSession = $customerSession;
        $this->codesHelper = $codesHelper;
        $this->responseItemFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     * Return logged in customer.
     *
     * @param string $id
     * @return \ImpreseeAI\ImpreseeVisualSearch\Api\ResponseCustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLoggedIdCustomer(string $id) {
        $client_code = $this->codesHelper->getClientCode();
        if (strcmp($id, $client_code) == 0) {
            $customerData = $this->customerSession->getCustomer(); 
            print_r($customerData);
            $responseItem = $this->responseItemFactory->create();
            $responseItem->setId($customerData->getId())
                ->setName($customerData->getName())
                ->setEmail($customerData->getEmail());
            return $responseItem;
        } else {
            throw new NotFoundException(__('Parameter is incorrect.'));
        }
        
    }
}
