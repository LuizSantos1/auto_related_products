<?php

class Product_Autorelated_Block_Autorelated extends Mage_Catalog_Block_Product_View
{
    /**
     * Retrieve a collection of related products from the same category
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection|array
     */
    public function getRelatedCollection()
    {
        $collection = [];

        /** @var Product_Autorelated_Helper_Data $helper */
        $helper = $this->helper('product_autorelated');

        $category = $this->_getProductCategory();
        if (!$category) {
            return $collection;
        }

        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->addFieldToFilter('entity_id', ['nin' => [$this->getProduct()->getId()]])
            ->addCategoryFilter($category);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $collection->setPageSize($helper->getCollectionSize())->setCurPage(1);

        return $collection;
    }

    /**
     * Get the last (most specific) active category assigned to the product
     *
     * @return Mage_Catalog_Model_Category|null
     */
    protected function _getProductCategory()
    {
        $product = $this->getProduct();
        $storeId = Mage::app()->getStore()->getId();

        $categoryIds = $product->getCategoryIds();
        if (empty($categoryIds)) {
            return null;
        }

        $categories = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($storeId)
            ->addAttributeToFilter('is_active', 1)
            ->addFieldToFilter('entity_id', ['in' => $categoryIds])
            ->addAttributeToSelect('*');

        return $categories->getLastItem() ?: null;
    }
}
