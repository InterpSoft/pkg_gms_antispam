<?php
namespace GmsAntispam\Component\Gmsantispam\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class StatisticsModel extends BaseDatabaseModel
{
    public function getStatistics()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('COUNT(*) as total')
            ->from($db->quoteName('#__gmsantispam_log'));
        $db->setQuery($query);
        $total = $db->loadResult();

        $query->clear()
            ->select('COUNT(DISTINCT ' . $db->quoteName('ip') . ') as unique_ips')
            ->from($db->quoteName('#__gmsantispam_log'));
        $db->setQuery($query);
        $uniqueIps = $db->loadResult();

        $query->clear()
            ->select('COUNT(*) as today_count')
            ->from($db->quoteName('#__gmsantispam_log'))
            ->where($db->quoteName('date') . ' >= ' . $db->quote(date('Y-m-d')));
        $db->setQuery($query);
        $todayCount = $db->loadResult();

        $query->clear()
            ->select('reason, COUNT(*) as count')
            ->from($db->quoteName('#__gmsantispam_log'))
            ->group($db->quoteName('reason'))
            ->order($db->quoteName('count') . ' DESC');
        $db->setQuery($query);
        $reasonStats = $db->loadObjectList();

        return array(
            'total' => $total,
            'unique_ips' => $uniqueIps,
            'today_count' => $todayCount,
            'reason_stats' => $reasonStats
        );
    }
}
