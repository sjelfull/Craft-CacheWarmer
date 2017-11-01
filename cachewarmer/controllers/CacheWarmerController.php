<?php
namespace Craft;

/**
 * Cache Warmer controller
 */
class CacheWarmerController extends BaseController
{
    /**
     * @var Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array('actionFire');

    /**
     * Handle the action to clear the cache.
     */
    public function actionFire()
    {
        $settings = $this->getSettings();

        $key = craft()->request->getParam('key');

        if (!$settings->key OR $key != $settings->key) {
            die('Unauthorized key');
        }

        craft()->cacheWarmer->warmCache();

        if (craft()->request->getPost('redirect')) {
            $this->redirectToPostedUrl();
        }

        die('All urls are all warmed up');
    }

    public function getSettings()
    {
        if (!$plugin = craft()->plugins->getPlugin('cachewarmer')) {
            die('Could not find the plugin');
        }

        return $plugin->getSettings();
    }

}
