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
 * Honeypot Logs Model
 *
 * @since  1.0
 */
class HoneypotModelLogs extends JModelList
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
     * Method to get a list of logs.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   1.0
     */
    public function getItems()
    {
        // Get the database object
        $db = $this->getDbo();

        // Create a new query object
        $query = $db->getQuery(true);

        // Select the required fields from the database
        $query->select('*')
            ->from($db->quoteName('#__honeypot_logs'))
            ->order($db->quoteName('date') . ' DESC');

        // Get the paginated results
        $db->setQuery($query);
        $items = $db->loadObjectList();

        // Return the items
        return $items;
    }
}