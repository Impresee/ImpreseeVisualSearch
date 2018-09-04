<?php
/**
 *   Display client code in system configuration page
 */
namespace Impresee\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Impresee\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Backend\Block\Template\Context;

class DatafeedUrl extends Field
{

    /**
     * To fetch client code from db
     * @var CodesHelper
     */
    public $codesHelper;
    /**
     * To fetch base url of the client server
     */
    protected $_storeManagerInterface;
    /**
     * Id of current store
     * @var int
     */
    protected $_storeId;
    /**
     * @param Context
     * @param Impresee\ImpreseeVisualSearch\Helper\Codes
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
     * Displays clientCode in system config
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html  = '<tr>';
        $html .= '  <td class="label">Datasource URL for this store</td>';
        $html .= '  <td class="value" style="color:red">';
        $html .=       $this->_storeManagerInterface->getStore($this->_storeId)->getBaseUrl() . "/impresee/Feed/?client_code=" . $this->codesHelper->getClientCode();
        $html .= '     <p class="note">';
        $html .= '         <span>Sign-in/Register in <a href="http://www.impresee.com" target="_blank">Impresee</a> and create a new catalog with this URL</span>';
        $html .= '     </p>';
        $html .= '  </td>';
        $html .= '  <td></td>';
        $html .= '</tr>';
        return $html;
    }
}
