<?php
namespace GmsAntispam\Component\Gmsantispam\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class LogsModel extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id',
                'a.id',
                'date',
                'a.date',
                'ip',
                'a.ip',
                'reason',
                'a.reason'
            );
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__gmsantispam_log', 'a'));

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            $query->where('(a.ip LIKE ' . $search . ' OR a.reason LIKE ' . $search . ')');
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'a.date');
        $orderDirn = $this->state->get('list.direction', 'DESC');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    public function delete($pks)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->delete($db->quoteName('#__gmsantispam_log'))
            ->where($db->quoteName('id') . ' IN (' . implode(',', $pks) . ')');

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return true;
    }
}