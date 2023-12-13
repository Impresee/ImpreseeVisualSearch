<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ImpreseeAI\ImpreseeVisualSearch\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;

class ImpreseeInViewEventData implements SectionSourceInterface
{   
    const VIEW_PRODUCT_EVENT = 'VIEW_PRODUCT';
    const VIEW_CATEGORY_EVENT = 'VIEW_CATEGORY';
    const VIEW_HOME_EVENT = 'VIEW_HOME';
    // Blog, Customer service, Q&A, etc
    const VIEW_CMS_EVENT = 'VIEW_OTHER';
    const VIEW_SEARCH_RESULTS_EVENT = 'VIEW_SEARCH_RESULTS';
    private $request;
    private $customerSession;
    private $screen;
    private $registry;
    private $codes_helper;
    private $requests_helper;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        CodesHelper $codes_helper,
        RequestsHelper $requests_helper,
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->registry = $registry;
        $this->requests_helper = $requests_helper;
        $this->codes_helper = $codes_helper;
        $this->screen = '';
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {   
        $base_url = $this->getBaseUrlData();
        $register_url = $this->requests_helper->getRegisterEventsUrl();
        $impresee_uuid = $this->codes_helper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?? '';
        $customerData = $this->customerSession->getCustomer();
        $event = $this->getCurrentPageEvent();
        $customer = [
            'id' => $customerData->getId(),
            'name' => $customerData->getName(),
            'email' => $customerData->getEmail(),
            'customer_group' => $customerData->getGroupId(),
        ];
        $section_data = [
            'customer' => $customer,
            'page_type_event'=> $event,
            'screen' => $this->screen,
            'base_url' => $base_url,
            'register_url' => $register_url,
            'impresee_uuid' => $impresee_uuid,
            'impresee_event' => $this->codes_helper->getRegisterEventsPlatformEvent(),
        ];
        
        switch ($event) {
            case 'VIEW_PRODUCT':
                $product = $this->getCurrentProduct();
                $product_data = [
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'id' => $product->getId(),
                ];
                $section_data['product'] = $product_data;
                break;
            case 'VIEW_CATEGORY':
                $category = $this->getCurrentCategory();
                $category_data = [
                    'name' => $category->getName(),
                    'url' => $category->getUrl(),
                    'id' => $category->getId(),
                ];
                $section_data['category'] = $category_data;
                break;
            default:
                break;
        }
        return $section_data;
    }

    public function getBaseUrlData(){
        $params = $this->request->getParams();
        $url_data = '';
        foreach ($params as $key => $value) {
            if (gettype($value) == 'array')
            {
                $url_data .= '&'.urlencode($key).'='.urlencode(join('|', $value));
            }
            else
            {
                $url_data .= '&'.urlencode($key).'='.urlencode($value);
            }
            
        }
        return $url_data;
    }

    public function getCurrentCategory()
    {        
        return $this->registry->registry('current_category');
    }
    
    public function getCurrentProduct()
    {        
        return $this->registry->registry('current_product');
    }

    private function getCurrentPageEvent()
    {
        if ($this->request->getFullActionName() == 'catalog_product_view') {
            return static::VIEW_PRODUCT_EVENT;
        }
        if ($this->request->getFullActionName() == 'catalog_category_view') {
            return static::VIEW_CATEGORY_EVENT;
        }
        if ($this->request->getFullActionName() == 'cms_index_index') {
            return static::VIEW_HOME_EVENT;
        }
        if ($this->request->getFullActionName() == 'catalogsearch_result_index') {
            return static::VIEW_SEARCH_RESULTS_EVENT;
        }
        $this->screen = $this->request->getFullActionName();
        return static::VIEW_CMS_EVENT;
    }
}