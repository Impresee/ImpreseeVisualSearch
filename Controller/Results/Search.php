<?php
/**
 *  Retreive a search results from impresee, with "search_uid" and "app_uuid"
 *  params given with get method. and save the data in singleton registry,
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Controller\Results;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use \Magento\Store\Model\StoreManagerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Model\MakeCollection;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Search extends Action
{
  /**
  * our rest service to retreive data from a previous search.
  * @const string
  */
    const BASE_URL_REST_SERVICE = "https://api.impresee.com/ImpreseeSearch/api/v1/rest/data/get_search/";


  /**
   * To create a page
   * @var Magento\Framework\View\Result\PageFactory
   */
    public $resultPageFactory;

  /**
   * To store search results
   * @var Magento\Framework\Registry
   */
    public $registry;

  /**
   * To load product data
   * @var  \Magento\Catalog\Model\ResourceModel\Product\Collection
   */
    public $productCollection;
  /**
   *   To load collections functions
   * @var ImpreseeAI\ImpreseeVisualSearch\Model\MakeCollection
   */
    public $makeCollection;

  /**
   * load codes of our app
   * @var ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
   */
    protected $_codesHelper;
  /**
   * Store Context
   * @var Magento\Store\Model\StoreManagerInterface
   */
    protected $_storeManagerInterface;
  /**
   * Scope context
   * @var Magento\Framework\App\Config\ScopeConfigInterface
   */
    protected $_scopeConfig;


  /**
   *   Constructor
   * @param \Magento\Framework\App\Action\Context $context
   * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
   * @param Magento\Framework\Registry
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\MakeCollection
   */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        MakeCollection $makeCollection,
        CodesHelper $codes,
        StoreManagerInterface $storeManagerInterface,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->makeCollection = $makeCollection;
        $this->_codesHelper = $codes;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
  /**
   *  Display search results
   * @return resultPage (page with results)
   */
    public function execute()
    {
        // if there are search_uid and app_uuid get params
        if ((($searchUid = $this->getRequest()->getParam('search_uid'))) &&
        (($type = $this->getRequest()->getParam('type')))) {
            //get app code for type
            $appUuid = $this->getAppUuid($type, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            //call our api service
            $data = $this->getProductDataByUid($searchUid, $appUuid);
            //if call ok!
            if ($data['status'] == 0) {
                //save results on registry
                $this->registry->register('searchResults', $data);
                //make a id's array from products
                $skus = $this->getSkuArray($data['products']);
                //save array of (rank , url) in registry
                $this->registry->register('rankData', $this->getRankAndUrl($data['products'], $type));
                //if order parameter == 'similarity'
                if (($sortOrder = $this->getRequest()->getParam('product_list_order') ) &&
                (strcmp($sortOrder, 'similarity') == 0)) {
                    //if product_list_dir param exist
                    if (($dir = $this->getRequest()->getParam('product_list_dir')) &&
                    (strcmp($dir, "desc")==0)) {
                        $skus = array_reverse($skus);
                    }
                    $this->productCollection = $this->makeCollection->
                    makeCollectionBySkuSorted($skus);
                  //if order parameter != 'similarity' (or doesn't exist)
                } else {
                    $this->productCollection = $this->makeCollection->
                    makeCollectionBySku($skus);
                }
              //if error on call
            } else {
                //make empty collection
                $this->productCollection = $this->makeCollection->
                makeCollectionBySku([]);
            }
          // If doesn't exist Impresee Get Params
        } else {
            //make empty collection
            $this->productCollection = $this->makeCollection->
            makeCollectionBySku([]);
        }
        $resultPage = $this->resultPageFactory->create();
        $list = $resultPage->getLayout()->getBlock('impresee.products.list');
        $list->setProductCollection($this->productCollection);
        return  $resultPage;
    }
  /**
   *   Get data from a specific Impresee search uid with an app uuid code
   * @param $searchUid : string
   * @param $appUuid : string
   * @return string (search results in Json format)
   */
    public function getProductDataByUid($searchUid, $appUuid)
    {
        $headers = [];
        $headers[] = "Content-Type: application/json";
        $curlCall = curl_init();
        curl_setopt($curlCall, CURLOPT_URL, $this::BASE_URL_REST_SERVICE . $appUuid . "/" . $searchUid);
        curl_setopt($curlCall, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlCall, CURLOPT_HTTPGET, true);
        curl_setopt($curlCall, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curlCall);
        if (curl_errno($curlCall)) {
            return null;
        } else {
            $result = json_decode($result, true);
            return $result;
        }
    }
  /**
   * Retreives the sku from products obtained in a search
   * @return array (with products ids)
   */
    protected function getSkuArray($productsData)
    {
        $resultArray = [];
        if (isset($productsData)) {
            foreach ($productsData as $product) :
                {
                $resultArray[] = $product['code'];
                }
            endforeach;
        }
        return $resultArray;
    }
  /**
   * Makes an array of (url,rank) elements and the type of search
   * @param array $products  of products
   * @param string $type type of search
   * @return array of url,rank elements
   */
    public function getRankAndUrl($products, $type)
    {
        $data = [];
        if ($products != null) {
            foreach ($products as $product) :
                {
                $data[] = [$product['url'],$product['rank']];
                }
            endforeach;
            $data[] = ["type", "'".$type."'"];
        }
        return $data;
    }
  /**
   * Get the right Impresee code for searching
   * @param string $type. Type of search
   * @param string $store. \Magento\Store\Model\ScopeInterface::SCOPE_STORE
   * @return string The Impresee App code for the type of search and store Scope
   * selected.
   */
    protected function getAppUuid($type, $store)
    {
        $url     = "";
        if ($type =="sketch") {
            $url = $this->_codesHelper->getSketchUrl($store);
        }
        if ($type =="photo") {
            $url = $this->_codesHelper->getPhotoUrl($store);
        }
        return $this->_codesHelper->getCode($url);
    }
}
