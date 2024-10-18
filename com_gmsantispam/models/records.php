<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class GmsantispamModelRecords extends ListModel
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        /*$db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->quoteName('#__gmsantispam_records')); // Annahme, dass Ihre Tabelle so heißt*/

        $db = Factory::getDbo();
        $query = $db->createQuery();
        $query->select('*')
            ->from('#__gmsantispam_records');
        $db->setQuery($query);

        return $query;
    }
}