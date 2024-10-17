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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_honeypot')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require the helper file
JLoader::register('HoneypotHelper', __DIR__ . '/helpers/honeypot.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Honeypot');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();