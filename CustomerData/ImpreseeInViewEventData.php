<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ImpreseeAI\ImpreseeVisualSearch\CustomerData;

use Exception;
use Magento\Customer\CustomerData\SectionSourceInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;
use Magento\Company\Api\CompanyManagementInterface;
class ImpreseeInViewEventData implements SectionSourceInterface
{   
    private $customerSession;
    private $codes_helper;
    private $requests_helper;
    private $companyManagement;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        CodesHelper $codes_helper,
        CompanyManagementInterface $companyManagement,
        RequestsHelper $requests_helper,
    ) {
        $this->customerSession = $customerSession;
        $this->companyManagement = $companyManagement;
        $this->requests_helper = $requests_helper;
        $this->codes_helper = $codes_helper;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {   
        $register_url = $this->requests_helper->getRegisterEventsUrl();
        $impresee_uuid = $this->codes_helper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?? '';
        $customerData = $this->customerSession->getCustomer();
        $companyId = $this->getCompanyId($customerData);
        
        $customer = [
            'id' => $customerData->getId(),
            'name' => $customerData->getName(),
            'email' => $customerData->getEmail(),
            'customer_group' => $customerData->getGroupId(),
            'company_id' => $companyId
        ];
        $section_data = [
            'customer' => $customer,
            'register_url' => $register_url,
            'impresee_uuid' => $impresee_uuid,
            'impresee_event' => $this->codes_helper->getRegisterEventsPlatformEvent(),
        ];   
        return $section_data;
    }

    public function getCompanyId($customerData)
    {
        $companyId = null;
        try {
            $customerId = $customerData->getId();
            if ($customerId) {
                $company = $this->companyManagement->getByCustomerId($customerId);
                if ($company) {
                    $companyId = $company->getId();
                }
            }
            return $companyId;
        } catch (Exception $e) {
            return $companyId;
        }
    }

}