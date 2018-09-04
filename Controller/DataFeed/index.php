<?php
/**
 *  To publish the DataFeed xml on a public URL
 *  Although its public, to see the content of datafeed a GET param
 *  named "client_code" its nedeed. this param must be equal to the autogenerated
 *  code on module installation. saved on "core_config_data" table.
 */
namespace Impresee\ImpreseeVisualSearch\Controller\DataFeed;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Raw as MagentoRaw;
use Magento\Store\Model\StoreManagerInterface as StoreManagerInterface;
use Magento\Framework\Exception\NotFoundException;
use Impresee\ImpreseeVisualSearch\Model\GenerateXml;
use Impresee\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class Index extends Action
{
  /**
   * @var \Magento\Framework\View\Result\PageFactory
   */
    public $resultPageFactory;
  /**
   *  To load client_code
   * @var Impresee\ImpreseeVisualSearch\Helper\Codes
   */
    public $codesHelper;
  /**
   *  Contains model functions to make the datafeed
   * @var Impresee\ImpreseeVisualSearch\Model\GenerateXml
   */
    public $generateXml;
  /**
   * Store context
   * @var StoreManagerInterface
   */
    public $storeManagerInterface;

  /**
   * To return plain text
   * @var Magento\Framework\Controller\Result\Raw
   */
    public $rawResult;

  /**
   *   Constructor
   * @param Context $context
   * @param PageFactory $resultPageFactory
   * @param Impresee\ImpreseeVisualSearch\Model\GenerateXml $generate
   * @param Impresee\ImpreseeVisualSearch\Helper\Codes $helper
   */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        MagentoRaw $rawResult,
        GenerateXml $generateXml,
        CodesHelper $codesHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->generateXml = $generateXml;
        $this->codesHelper = $codesHelper;
        $this->storeManagerInterface = $storeManager;
        $this->rawResult = $context->getResultFactory();
        parent::__construct($context);
    }

  /**
   * If client_code GET param exist in the URL, and its equal to
   * impresee/client_code on core_config_data table, loads the datafeed. else,
   * nothing load
   */
    public function execute()
    {
        $resultPage = $this->rawResult->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        $client_code = $this->codesHelper->getClientCode();
        if ($paramClientCode = $this->getRequest()->getQuery('client_code')) {
            if (strcmp($paramClientCode, $client_code) == 0) {
                $storeId = $this->storeManagerInterface->getStore()->getId();
                $catalogString = $this->generateXml->generateXmlByStore($storeId);
                header('Content-type: application/xml; charset=UTF-8');
                $resultPage->setContents($catalogString);
            } else {
                throw new NotFoundException(__('Parameter is incorrect.'));
            }
        } else {
            throw new NotFoundException(__('Parameter is incorrect.'));
        }
        return $resultPage;
    }
}
