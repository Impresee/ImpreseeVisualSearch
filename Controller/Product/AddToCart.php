<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
* Class AddToCart
* @package Magenest\BookingReservation\Controller\Product
*/
class AddToCart extends Action implements HttpPostActionInterface
{
   /** @var \Magento\Checkout\Model\SessionFactory */
   private $checkoutSession;

   /** @var \Magento\Quote\Api\CartRepositoryInterface */
   private $cartRepository;

   /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
   private $productRepository;

   /** @var \Magento\Framework\Serialize\Serializer\Json */
   private $json;

   /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable */
   private $configurableType;

   /**
     * AddToCart constructor.
     * @param Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     */
   public function __construct(
       Context $context,
       \Magento\Framework\Serialize\Serializer\Json $json,
       \Magento\Checkout\Model\SessionFactory $checkoutSession,
       \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
       \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
   ) {
       $this->checkoutSession = $checkoutSession;
       $this->cartRepository = $cartRepository;
       $this->productRepository = $productRepository;
       $this->json = $json;
       $this->configurableType = $configurableType;
       parent::__construct($context);
   }

   /**
    * @return ResultInterface
    * @throws LocalizedException
    */
   public function execute()
   {
       $product = $this->productRepository->get($this->getRequest()->getParam('sku'));
       $qty = $this->getRequest()->getParam('qty');

       $session = $this->checkoutSession->create();
       $quote = $session->getQuote();
       $quote->addProduct($product, $qty);

       $this->cartRepository->save($quote);
       $session->replaceQuote($quote)->unsLastRealOrderId();
   }
}