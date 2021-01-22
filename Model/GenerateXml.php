<?php
/**
 *  Generate datafeed xml with Impresee format
 */

namespace ImpreseeAI\ImpreseeVisualSearch\Model;

use ImpreseeAI\ImpreseeVisualSearch\Model\Products as ProductCollection;
use \Magento\Catalog\Model\Category as Category;
use \Magento\Store\Model\App\Emulation;
use \Magento\Store\Model\StoreManagerInterface;
use \Psr\Log\LoggerInterface;
use \Magento\Catalog\Api\ProductAttributeRepositoryInterface as ProductAttributeRepositoryInterface;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface  as Timezone;

class GenerateXml
{
  /**
   * Product features saved on the xml file
   * @var string[]
   */
    protected $PRODUCT_ATTRIBUTES = ["sku", "name", "price", "special_price", "special_from_date", "special_to_date"];
  /**
   * Collection of Products
   * @var ImpreseeAI\ImpreseeVisualSearch\Model\Products
   */
    protected $_productCollection;
  /**
   * To load categories
   * @var Magento\Catalog\Model\Category
   */
    protected $_category;
  /**
   * To emulate a particulary Store
   * @var Magento\Store\Model\App\Emulation
   */
    protected $_appEmulation;
  /**
   * Store context
   * @var \Magento\Store\Model\StoreManagerInterface
   */
    protected $_storeManagerInterface;
  /**
   * Root category of Magento
   * @var Magento\Catalog\Model\Category
   */
    protected $_rootCategory;

    /**
   * Logger
   * @var \Psr\Log\LoggerInterface
   */
    protected $logger;

    /**
    * max number of images found on a singfle product
    */
    private $_maxNumberImages;
    /**
    * attributes ids (we use them for the configurable products)
    */
    private $attributesIds;
    /*
    Used to get the current date for the store
    */
    private $timezone;

  /**
   * Constructor
   * @var ImpreseeAI\ImpreseeVisualSearch\Model\Products $ProductCollection
   * @var Magento\Catalog\Model\Category $category
   * @var Magento\Store\Model\App\Emulation $appEmulation
   * @var Magento\Store\Model\StoreManagerInterface $storeManagerInterface
   * @var Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
   */
    public function __construct(
        ProductCollection $collection,
        Category $category,
        Emulation $appEmulation,
        StoreManagerInterface $storeManagerInterface,
        LoggerInterface $logger,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        Timezone $timezone
    ) {
        $this->_productCollection = $collection;
        $this->_category = $category;
        $this->_rootCategory = $category;
        $this->_appEmulation = $appEmulation;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->logger = $logger;
        $this->_maxNumberImages = 0;
        $this->timezone = $timezone;
        $this->attributesIds = array();
        foreach ($this->PRODUCT_ATTRIBUTES as $attributeCode) {
          $attribute = $productAttributeRepository->get($attributeCode);
          $attributeId = $attribute->getAttributeId();
          array_push($this->attributesIds, $attributeId);
        }
    }
  /**
   * Convert an array of strings to an array of ints
   * (useful to generate catalog collections)
   * @param array of string (numbers of ids)
   * @return array of int (numbers of ids)
   */
    public function toIntArray($stringArray)
    {
        return array_map(function($val) { return (int)$val; },
          $stringArray);
    }
  /**
   * Main XML generation function
   * @param store id (int)
   * @return string (with all products on Impresee xml format)
   */
    public function generateXmlByStore($store)
    {
        $resultString = "";
        $initialEnvironmentInfo = $this->_appEmulation
        ->startEnvironmentEmulation($store);
        $collection = $this->_productCollection
        ->getCollection()
        ->setStore($store)
        ->addStoreFilter($store)
        ->addAttributeToSelect($this->PRODUCT_ATTRIBUTES)
        ->addMediaGalleryData();
        $resultString = $this->getXml($collection);
        $this->_appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        return $resultString;
    }
  /**
   * Return a string with the datafeed on a XML file with Impresee schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    public function getXml($products)
    {
        $resultString  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>";
        $resultString .= "<catalog>";
        $resultString .= "<products>";
        $resultString .= $this->makeProductsTags($products);
        $resultString .= "</products>";
        $resultString .= "<types>";
        $resultString .= $this->makeTypeTags($products);
        $resultString .= "</types>";
        $resultString .= "</catalog>";
        return $resultString;
    }
  /**
   * Make XML products tags for all product according to Impresee Schema
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    public function makeProductsTags($products)
    {
        $resultString = "";
        foreach ($products as $product) :
            {
              $product_url = $product->getProductUrl();
              if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
              {
                $productTypeInstance = $product->getTypeInstance();
                $usedProducts = $productTypeInstance->getUsedProducts($product, $this->attributesIds);
                foreach ($usedProducts  as $child) {
                    $resultString .= $this->parseSimpleProduct($child, $product_url);
                }
              }
              else
              {
                $resultString .= $this->parseSimpleProduct($product, $product_url);
              }
              
            }
        endforeach;
        return $resultString;
    }

   private function parseSimpleProduct($product, $product_url)
   {
      $resultString = "";
      $resultString .= "<product code=\"".htmlspecialchars(strip_tags($product->getSku()))."\" url=\"".htmlspecialchars($product_url)."\">";
      $resultString .= "<categories>";
      $resultString .= $this->makeCategoriesTags($product);
      $resultString .= "</categories>";

      $resultString .= "<texts>";
      $resultString .= $this->makeAttributesTags($product);
      $resultString .= "</texts>";

      $resultString .= "<images>";
      $resultString .= $this->makeImageTags($product);
      $resultString .= "</images>";

      $resultString .= "</product>";
      return $resultString;

   }
  /**
   * Make categories XML tags for a single product according to Impresee XML
   * schemma
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeCategoriesTags($product)
    {
        $resultString = "";
        foreach ($product->getCategoryIds() as $category) :
            {
            if ($category) {
                $resultString .= "<category ref_code=\"".htmlspecialchars(strip_tags($category))."\"/>";
            }
            }
        endforeach;
        return $resultString;
    }

   private function productHasSpecialPriceAvailable($product)
   {
        $product_special_price_from = $product->getData("special_from_date");
        $product_special_price_to = $product->getData("special_to_date");
        $current_date = $this->timezone->date()->format('Y-m-d H:i:s');
        $special_from = strtotime($product_special_price_from);
        $special_to = strtotime($product_special_price_to);
        $current = strtotime($current_date);
        $check_from = !$special_from || $current >= $special_from;
        $check_to = !$special_to || $current <= $special_to;
        $is_special_price_available = $check_from && $check_to;
        return $is_special_price_available;
   } 
  /**
   * Make text XML tags for a single product according to Impresee XML schema
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeAttributesTags($product)
    {
        $resultString = "";
        $in_sale = $this->productHasSpecialPriceAvailable($product);
        foreach ($this->PRODUCT_ATTRIBUTES as $attribute) :
            {
            $attribute_name = $attribute;
            if ($attribute_name == "special_from_date" || $attribute_name == "special_to_date"){
              continue;
            }
            if ($info=$product->getData($attribute)) {
                if ($attribute_name == "special_price" && !$in_sale) continue;
                if ($attribute_name == "special_price" && $in_sale ) {
                  $attribute_name = "price";
                } else if ($attribute_name == "price" && $product->getData("special_price") && $in_sale) {
                  $attribute_name = "price_from";
                }
                $resultString .= "<text ref_code=\"".htmlspecialchars(strip_tags($attribute_name))."\">".htmlentities(strip_tags($info), ENT_XML1, "UTF-8")."</text>";
            }
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make image XML tags for a single product according to Impresee XML schema
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeImageTags($product)
    {
        $resultString = "";
        $images = $this->getImageUrl($product);
        if(!$images) { return; }
        $count = 1;
        foreach ($images as $image) :
            {
              $resultString .= "<image ref_code=\"image".$count."\" url_image=\"".htmlspecialchars(strip_tags($image['url']))."\"/>";
              $count++;
            }
        endforeach;
        $this->_maxNumberImages = max($this->_maxNumberImages, $count);
        return $resultString;
    }
  /**
   * Creates types XML tags according Impresee XML schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    private function makeTypeTags($products)
    {
        $resultString  = "<categories>";
        $resultString .= $this->makeHierarchyCategoriesTags($products);
        $resultString .= "</categories>";

        $resultString .= "<texts>";
        $resultString .= $this->makeValidTextsTag();
        $resultString .= "</texts>";

        $resultString .= "<images>";
        $resultString .= $this->makeValidImagesTag();
        $resultString .= "</images>";
        return $resultString;
    }
  /**
   * Make hierarchy categories xml tags according to Impresee XML schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    private function makeHierarchyCategoriesTags($products)
    {
        $resultString = "";
        $store = $this->_storeManagerInterface->getStore();
        $rootCategoryId = $store->getRootCategoryId();
        $rootCategory = $this->_rootCategory->load($rootCategoryId);
        $categoriesIdArray = $this->toIntArray($rootCategory->getAllChildren(true));
        $resultString .= "<category code=\"".htmlspecialchars(strip_tags($rootCategoryId))."\" name=\"".htmlspecialchars(strip_tags($rootCategory->getName()))."\"/>";
        foreach ($categoriesIdArray as $categoryId) :
            {
            /** ignores root category (already added)*/
            if ($rootCategoryId == $categoryId) {
                continue;
            }
            $category = $this->_category->load($categoryId);
            $name = htmlspecialchars(strip_tags($category->getName()));
            $resultString .= "<category code=\"".htmlspecialchars(strip_tags($category->getId()))."\" name=\"".htmlspecialchars(strip_tags($name))."\">";
            $resultString .= "<parent ref_code=\"".htmlspecialchars(strip_tags($category->getParentId()))."\"/>";
            $resultString .= "</category>";
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make text xml types tags according to Impresee XML schemma
   * @return string (XML like)
   */
    private function makeValidTextsTag()
    {
        $resultString = "";
        foreach ($this->PRODUCT_ATTRIBUTES as $attribute) :
        {
            $attribute_name = $attribute;
            if ($attribute_name == "special_price") {
                  $attribute_name = "price";
            } else if ($attribute_name == "price") {
              $attribute_name = "price_from";
            }  
            $resultString .= "<text code=\"".htmlspecialchars(strip_tags($attribute_name))."\" name=\"".str_replace("_", " ", htmlspecialchars(strip_tags($attribute_name)))."\"/>";
        }
        endforeach;
        return $resultString;
    }
  /**
   * Make image xml types tags according to Impresee XML schemma
   * @return string (XML like)
   */
    private function makeValidImagesTag()
    {
        $resultString = "";
        $resultString .= "<image code=\"image1\" name=\"Main Image \"/>";
        for ($i = 2; $i <= $this->_maxNumberImages; $i++) {
            $resultString .= "<image code=\"image".$i."\" name=\"Image ".$i."\"/>";
        }
        return $resultString;
    }
    /**
     * To get images from a single product
     * @param Magento\Catalog\Model\Product
     */

    protected function getImageUrl($product)
    {
        return $product->getMediaGalleryImages();
    }
}
