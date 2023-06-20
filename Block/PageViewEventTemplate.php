<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Block;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;


class PageViewEventTemplate extends \Magento\Framework\View\Element\Template
{
    const VIEW_PRODUCT_EVENT = 'VIEW_PRODUCT';
    const VIEW_CATEGORY_EVENT = 'VIEW_CATEGORY';
    const VIEW_HOME_EVENT = 'VIEW_HOME';
    // Blog, Customer service, Q&A, etc
    const VIEW_CMS_EVENT = 'VIEW_OTHER';
    const VIEW_SEARCH_RESULTS_EVENT = 'VIEW_SEARCH_RESULTS';
    protected $customerSession;
    protected $context;
    public $customerData;
    protected $request;
    protected $registry;
    public $codesHelper;
    public $requestsHelper;
    public $screen;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request, 
        \Magento\Framework\Registry $registry,
        CodesHelper $codesHelper,
        RequestsHelper $requestsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->registry = $registry;
        $this->codesHelper = $codesHelper;
        $this->requestsHelper = $requestsHelper;
        $this->screen = '';
    }

    public function getCurrentPageEvent()
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

    public function _prepareLayout()
    {
        $this->customerData = $this->customerSession->getCustomer();
        return parent::_prepareLayout();
    }
}