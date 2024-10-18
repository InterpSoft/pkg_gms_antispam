<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

class GmsantispamServiceProvider
{
    /**
     * Liefert die Anzahl der Spam-Meldungen für einen bestimmten Zeitraum.
     *
     * @param   string  $timeRange  Zeitraum (z.B. 'daily', 'weekly', 'monthly')
     * @param   integer  $limit      Anzahl der Einträge, die zurückgegeben werden sollen
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getSpamCount($timeRange, $limit = 10)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*) AS count')
            ->from($db->quoteName('#__gmsantispam_logs'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('spam'))
            ->where($db->quoteName('created') . ' >= ' . $db->quote(GmsantispamHelper::getTimestampForTimeRange($timeRange)));

        $db->setQuery($query, 0, $limit);
        $results = $db->loadObjectList();

        return ArrayHelper::getColumn($results, 'count');
    }

    /**
     * Liefert Details zu einer bestimmten Spam- oder Ham-Meldung.
     *
     * @param   integer  $id  ID der Meldung
     *
     * @return  object|boolean
     *
     * @since   1.0.0
     */
    public function getMessageDetails($id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__gmsantispam_logs'))
            ->where($db->quoteName('id') . ' = ' . $db->quote($id));

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result ?: false;
    }

    /**
     * Aktualisiert den Status einer Meldung (z.B. von 'spam' auf 'ham' oder umgekehrt).
     *
     * @param   integer  $id      ID der Meldung
     * @param   string   $newType  Neuer Typ der Meldung ('spam' oder 'ham')
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function updateMessageStatus($id, $newType)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__gmsantispam_logs'))
            ->set($db->quoteName('type') . ' = ' . $db->quote($newType))
            ->where($db->quoteName('id') . ' = ' . $db->quote($id));

        $db->setQuery($query);
        return $db->execute();
    }
}