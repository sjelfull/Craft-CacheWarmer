<?php
namespace Craft;

use \Guzzle\Batch\Batch;
use \Guzzle\Batch\BatchRequestTransfer;
use \Guzzle\Http\Client as Guzzle;

class CacheWarmerService extends BaseApplicationComponent
{
    protected $settings = array();

    function __construct()
    {
        $plugin = craft()->plugins->getPlugin('cachewarmer');
        if (!$plugin)
        {
            throw new Exception('Couldn’t find the Cache Warmer plugin!');
        }
        $this->settings = $plugin->getSettings();
    }

    public function warmCache() {
        $urls = array();

        // Only include enabled sections
        $enabledSections = array_filter($this->settings->enabledSections, function($item)
        {
            if (!empty($item['enabled'])) return true;
        });

        // Put section handles into an array
        $sectionHandles = array_keys($enabledSections);

        // Get elements
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->section = $sectionHandles;
        $entries = $criteria->find();

        foreach($entries as $entry) {
            $urls[] = $entry->getUrl();
        }

        // Check for commerce
        $commercePlugin = craft()->plugins->getPlugin('commerce');

        if ($commercePlugin)
        {
            // Only include enabled product types
            $enabledProductTypes = array_filter($this->settings->enabledProductTypes, function($item)
            {
                if (!empty($item['enabled'])) return true;
            });

            // Put product type ids into an array
            $productTypeIds = array_keys($enabledProductTypes);

            // Get products
            $criteria = craft()->elements->getCriteria('Commerce_Product');
            $criteria->typeId = $productTypeIds;
            $products = $criteria->find();

            foreach($products as $product) {
                $urls[] = $product->getUrl();
            }
        }

        try
        {
            // Create client
            $client = new Guzzle();

            // If the SSL bundle path is specified, use it to verify guzzle requests
            $bundlePath = $this->settings->sslBundlePath;
            if ($bundlePath) 
            {
                $client->setDefaultOption('verify', $bundlePath);
            }

            // Create a new pool and send off requests, 20 at a time
            $transferStrategy = new BatchRequestTransfer($this->settings->parallelRequests);
            $divisorStrategy = $transferStrategy;
            $batch = new Batch($transferStrategy, $divisorStrategy);

            // Create requests for every url and add them to the batch
            foreach($urls as $url) {
                $batch->add( $client->get($url) );
            }

            // Flush the queue and retrieve the flushed items
            $arrayOfTransferredRequests = $batch->flush();
        }
        catch (\Guzzle\Http\Exception\CurlException $e) 
        {
            // Throw a craft exception which displays the error cleanly
            throw new HttpException(400, '(Cache Warmer) Internet connection not available');
        }
    }

    public function isEnabledForSection($sectionHandle)
    {
        return !empty($this->settings->enabledByDefault[$sectionHandle]);
    }

    public function isEnabledForProductType($productTypeId)
    {
        return !empty($this->settings->enabledProductTypes[$productTypeId]);
    }
}
