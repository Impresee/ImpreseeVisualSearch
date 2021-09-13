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
        $resultString .= "<feed>";
        $resultString .= $this->makeProductsTags($products);
        $resultString .= "</feed>";
        return $resultString;
    }
  /**
   * Make XML products tags for all product according to Impresee Schema
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    public function makeProductsTags($products)
    {
      $categories = $this->makeHierarchyCategories($products);
        $resultString = "";
        foreach ($products as $product) :
            {
              $product_url = $product->getProductUrl();
              if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
              {
                $productTypeInstance = $product->getTypeInstance();
                $usedProducts = $productTypeInstance->getUsedProducts($product);
                foreach ($usedProducts  as $child) {
                    $resultString.= "<product>";
                    $resultString.= "<parent_id>".$product->getSku()."</parent_id>";
                    $resultString .= $this->parseSimpleProduct($child, $product_url, $categories);
                    $resultString.= "</product>";
                }
              }
              else
              {
                $resultString.= "<product>";
                $resultString .= $this->parseSimpleProduct($product, $product_url, $categories);
                $resultString.= "</product>";
              }
            }
        endforeach;
        return $resultString;
    }

   private function parseSimpleProduct($product, $product_url, $categories)
   {
      $resultString = "";
      $resultString .= "<id>";
      $resultString .= htmlspecialchars(strip_tags($product->getSku()));
      $resultString .= "</id>";
      $resultString .= "<url>";
      $resultString .= htmlspecialchars(strip_tags($product_url));
      $resultString .= "</url>";

      // Get images
      $resultString .= $this->makeImageTags($product);
      // Get Texts
      $resultString .= $this->makeAttributesTags($product);
      // Get categories and extra attributes
      $resultString .= $this->makeCategoriesTags($product, $categories);
      $resultString .= $this->makeAttributes($product);
      return $resultString;

   }

   private function makeAttributes($product)
   {
      $resultString = "";
      $attributes = $product->getAttributes();
      foreach($attributes as $a)
      {
          $attribute_code = $a->getAttributeCode();
          $attribute_name = $a->getName();
          $attribute_value = $product->getData($attribute_code);
          $resultString .= '<'.htmlspecialchars(strip_tags($attribute_name)).'>'.htmlspecialchars(strip_tags($attribute_value)).'</'.htmlspecialchars(strip_tags($attribute_name)).'>';
      }
      return $resultString;
   }

  /**
   * Make categories XML tags for a single product according to Impresee XML
   * schemma
   * @param Magento\Catalog\Model\Product
   * @return string (XML like)
   */
    private function makeCategoriesTags($product, $categories)
    {
        $resultString = "";
        $first = true;
        $count = 0;
        foreach ($product->getCategoryIds() as $category_id) :
            {
            if ($category_id && array_key_exists($category_id, $categories)) {
              if ( array_key_exists('parent', $categories[$category_id])){
                $parent_id = $categories[$category_id]['parent'];
                if (array_key_exists($parent_id, $categories)) {
                  $parent_name = strtolower($categories[$parent_id]['name']);
                  $pos = strpos($parent_name, 'marca');
                  if ($pos === false && $first) {
                    $first = false;
                    $resultString .= "<main_category>".htmlspecialchars(strip_tags($categories[$category_id]['name']))."</main_category>";
                  }
                  else if ($pos === false && !$first) {
                    $resultString .= "<secondary_category".$count.">".htmlspecialchars(strip_tags($categories[$category_id]['name']))."</secondary_category".$count.">";
                    $count++;
                  }
                  else {
                    $resultString .= "<brand>".htmlspecialchars(strip_tags($categories[$category_id]['name']))."</brand>";
                  }
                } 
              }
              else {
                $pos = strpos(strtolower($categories[$category_id]['name']), 'marca');
                if ($pos !== false) {
                 continue;
                }
                $resultString .= "<secondary_category".$count.">".htmlspecialchars(strip_tags($categories[$category_id]['name']))."</secondary_category".$count.">";
                $count++;
              }
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
                $resultString .= "<".htmlspecialchars(strip_tags($attribute_name)).">".htmlentities(strip_tags($info), ENT_XML1, "UTF-8")."</".htmlspecialchars(strip_tags($attribute_name)).">";
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
        $first = true;
        foreach ($images as $image) :
            {
              if ($first) {
                $first = false;
                $resultString .= "<main_image>". htmlspecialchars(strip_tags($image['url']))."</main_image>";
              }
              else {
                $resultString .= "<secondary_image".$count.">".htmlspecialchars(strip_tags($image['url']))."</secondary_image".$count.">";
                $count++;
              }
            }
        endforeach;
        return $resultString;
    }
  /**
   * Make hierarchy categories xml tags according to Impresee XML schemma
   * @param ImpreseeAI\ImpreseeVisualSearch\Model\Products Collection $products collection of products
   * @return string (XML like)
   */
    private function makeHierarchyCategories($products)
    {
        $categories = array();
        $store = $this->_storeManagerInterface->getStore();
        $rootCategoryId = $store->getRootCategoryId();
        $rootCategory = $this->_rootCategory->load($rootCategoryId);
        $categoriesIdArray = $this->toIntArray($rootCategory->getAllChildren(true));
        foreach ($categoriesIdArray as $categoryId) :
            {
            /** ignores root category (already added)*/
            if ($rootCategoryId == $categoryId) {
                continue;
            }
            $category = $this->_category->load($categoryId);
            $name = htmlspecialchars(strip_tags($category->getName()));
            $categories[$category->getId()] = array('name' => htmlspecialchars(strip_tags($category->getName())));
            if ($rootCategoryId != $category->getParentId()){
              $categories[$category->getId()]['parent'] = $category->getParentId();
            }
            }
        endforeach;
        return $categories;
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
