<?php
namespace Craft;

/**
 * Class CacheWarmerPlugin
 *
 * @package Craft
 */
class CacheWarmerPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    function getName()
    {
        return 'Cache Warmer';
    }

    /**
     * @return string
     */
    function getVersion()
    {
        return '0.1';
    }

    /**
     * @return string
     */
    function getDeveloper()
    {
        return 'Fred Carlsen';
    }

    /**
     * @return string
     */
    function getDeveloperUrl()
    {
        return 'http://sjelfull.no';
    }

    /**
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'enabledSections' => AttributeType::Mixed,
            'enabledProductTypes' => AttributeType::Mixed,
            'key' => array(
                AttributeType::String,
                'required' => true
            ),
            'parallelRequests' => array(
                AttributeType::Number,
                'required' => true,
                'default' => 20,
            ),
        );
    }

    /**
     * @return mixed
     */
    public function getSettingsHtml()
    {
        $editableSections = array();
        $sections = array();
        $allSections = craft()->sections->getAllSections('name');

        foreach ($allSections as $section)
        {
            $editableSections[$section->handle] = array('section' => $section);
            $sections[] = $section;

            // Find total entries
            $criteria = craft()->elements->getCriteria(ElementType::Entry);
            $criteria->section = $section->handle;
            $count = $criteria->count();

            $sectionCount[$section->handle] = $count;
        }

        $commercePlugin = craft()->plugins->getPlugin('commerce');

        if ($commercePlugin)
        {
            $editableProductTypes = array();
            $productTypes = array();
            $allProductTypes = craft()->commerce_productTypes->getAllProductTypes();

            foreach ($allProductTypes as $productType)
            {
                $editableProductTypes[$productType->handle] = array('productType' => $productType);
                $productTypes[] = $productType;

                // Find total products
                $criteria = craft()->elements->getCriteria('Commerce_Product');
                $criteria->typeId = $productType->id;
                $count = $criteria->count();

                $productTypeCount[$productType->handle] = $count;
            }
        }

        return craft()->templates->render('cachewarmer/_settings', array(
            'settings' => $this->getSettings(),
            'sections' => $sections,
            'sectionCount' => $sectionCount,
            'productTypes' => (isset($productTypes)) ? $productTypes : array(),
            'productTypeCount' => (isset($productTypeCount)) ? $productTypeCount : 0,
        ));
    }
}
