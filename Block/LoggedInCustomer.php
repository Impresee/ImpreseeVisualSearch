<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Block;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;


class LoggedInCustomer extends \Magento\Framework\View\Element\Template
{
    const VIEW_PRODUCT_EVENT = 'VIEW_PRODUCT';
    const VIEW_CATEGORY_EVENT = 'VIEW_CATEGORY';
    const VIEW_HOME_EVENT = 'VIEW_HOME';
    // Blog, Customer service, Q&A, etc
    const VIEW_CMS_EVENT = 'VIEW_OTHER';
    protected $customerSession;
    protected $context;
    public $customerData;
    protected $request;
    protected $registry;
    public $codesHelper;

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
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->context = $context;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->registry = $registry;
        $this->codesHelper = $codesHelper;
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
        if ($this->request->getFullActionName() == 'cms_page_view') {
            return static::VIEW_CMS_EVENT;
        }
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