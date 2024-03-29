<?php
/**
 *   Display client code in system configuration page
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class DatafeedComment extends Field
{
    /**
     *  To fetch client code from db
     * @var CodesHelper
     */
    public $codesHelper;
    /**
     * To fetch base url of the client server
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;
    /**
     * Id of current store
     * @var int
     */
    protected $_storeId;
    /**
     * @param Context
     * @param ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
     */
    public function __construct(
        Context $context,
        CodesHelper $helper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_storeId = (int) $request->getParam('store', 0);
        $this->codesHelper = $helper;
        parent::__construct($context);
        $this->_storeManagerInterface = $this->_storeManager;
    }
    /**
     * Displays datasource comment on config
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html  = '<div class="comment">';
        $html .= '1. Create your account in <a href="https://console.impresee.com/Console\" target=\"_blank\">Impresee Console</a>.<br>';
        $html .= '2. Sign in and create a new project.<br>';
        $html .= '3. Create a new catalog on the project using your store view data feed:';
        $html .= '  <h4>Data feed URL for this store view:<br>';
        $html .=      '<strong id="impresee-data-url" style="color:red">' . $this->getDataSourceUrl();
        $html .= '     </strong></h4>';
        $html .= '<a id="impresee-copy-url" style="padding-left:2.8rem; cursor:pointer">Copy to clipboard.</a><br><br>';
        $html .= '4. Once your catalog is ready, you need to visit the <a href="https://console.impresee.com/Console\" target=\"_blank\">catalog panel</a>, <br>';
        $html .= 'click on details and copy the application UUID.<br>';
        $html .= '5. Paste the application UUID in the search configuration section below.'; 
        $html .= '</div>';
        return $html;
    }
    /**
     * To generate the url where the datefeed will be displayed
     * @return string
     */
    private function getDataSourceUrl()
    {
        return $this->_storeManagerInterface->getStore($this->_storeId)->getBaseUrl() . "impresee/DataFeed/?client_code=" . $this->codesHelper->getClientCode().'&page=1';
    }
}
