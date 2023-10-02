<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Block;


class LoggedInCustomer extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;
    public $customerData;

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
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
    }

    public function _prepareLayout()
    {
        $this->customerData = $this->customerSession->getCustomer();
        return parent::_prepareLayout();
    }
}