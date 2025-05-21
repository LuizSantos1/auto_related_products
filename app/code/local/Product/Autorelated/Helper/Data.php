<?php

class Product_Autorelated_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Default number of products to display
     */
    const PRODUCTS_COLLECTION_COUNT = 10;

    /**
     * XML config path for collection count
     */
    const XML_PATH_COLLECTION_COUNT = 'catalog/autorelated_group/count';

    /**
     * Get the number of products to display in the related collection
     *
     * @return int
     */
    public function getCollectionSize()
    {
        $config = (int) Mage::getStoreConfig(self::XML_PATH_COLLECTION_COUNT, Mage::app()->getStore());

        return ($config > 0) ? $config : self::PRODUCTS_COLLECTION_COUNT;
    }
}
