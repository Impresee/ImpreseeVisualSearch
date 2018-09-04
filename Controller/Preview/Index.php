<?php
/**
 *  Used to Preview in admin side the changes to the frontview
 */
namespace Impresee\ImpreseeVisualSearch\Controller\Preview;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NotFoundException;
use Impresee\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class Index extends Action
{
  /**
   *   To load client_code from db
   * @var Impresee\ImpreseeVisualSearch\Helper\Codes
   */
    public $codesHelper;
  /**
   *   Constructor
   * @param \Magento\Framework\App\Action\Context $context
   * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
   */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CodesHelper $codesHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->codesHelper = $codesHelper;
        parent::__construct($context);
    }
  /**
   *  When runs, search on URL the client_code GET param. if exist, compare
   *  with the client_code on db, and if equals, display the preview page
   * @return Magento\Framework\View\Result\Page
   */
    public function execute()
    {
        $client_code = $this->codesHelper->getClientCode();
        if ($paramClientCode = $this->getRequest()->getParam('client_code')) {
            if (strcmp($paramClientCode, $client_code) == 0) {
                $resultPage = $this->resultPageFactory->create();
                return  $resultPage;
            } else {
                throw new NotFoundException(__('Parameter is incorrect.'));
            }
        } else {
            throw new NotFoundException(__('Parameter is incorrect.'));
        }
    }
}
