<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_gmsantispam
 *
 * @copyright   (C) 2023 Your Name or Your Company
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

// Autoload der Komponenten-Klassen
JLoader::register('GmsantispamHelper', JPATH_COMPONENT . '/helpers/gmsantispam.php');

// Controller ausführen
$controller = BaseController::getInstance('Gmsantispam');

// Aufgabe ausführen, die über die URL übergeben wurde
$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task', 'display'));

// Redirect, falls vom Controller gesetzt
$controller->redirect();