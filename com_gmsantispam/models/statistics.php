<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_honeypot
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Honeypot Statistics Model
 *
 * @since  1.0
 */
class HoneypotModelStatistics extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JControllerLegacy
     * @since   1.6
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to get the statistics data.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.0
     */
    public function getStatistics()
    {
        // Get the plugin object
        $plugin = JPluginHelper::getPlugin('captcha', 'honeypot');

        // Call the plugin's getStatistics method
        return $plugin->getStatistics();
    }
}