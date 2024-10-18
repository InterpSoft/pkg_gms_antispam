<?php
namespace GmsAntispam\Component\Gmsantispam\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class GmsantispamHelper
{
    public static function addSubmenu($vName)
    {
        $app = Factory::getApplication();
        $app->getMenu()->addItem('Logs', 'index.php?option=com_gmsantispam&view=logs', $vName === 'logs');
        $app->getMenu()->addItem('Statistics', 'index.php?option=com_gmsantispam&view=statistics', $vName === 'statistics');
    }

    public static function getActions($categoryId = 0)
    {
        $user = Factory::getUser();
        $result = new \JObject;

        $assetName = 'com_gmsantispam';

        $actions = array(
            'core.admin',
            'core.manage',
            'core.create',
            'core.edit',
            'core.edit.own',
            'core.edit.state',
            'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
}