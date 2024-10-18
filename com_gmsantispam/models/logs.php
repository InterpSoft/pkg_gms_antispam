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
class GmsantispamModelLogs extends JModelList
{
    public function getLogs()
    {
        // Deine Logik für das Abrufen der Logs, z.B. aus der Datenbank
        /*$db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__gmsantispam_logs'));
        $db->setQuery($query);*/

        $db = Factory::getDbo();
        $query = $db->createQuery();
        $query->select('*')
            ->from('#__gmsantispam_logs');
        $db->setQuery($query);

        return $db->loadObjectList();  // Gibt ein Array von Objekten zurück
    }
}