<?php
namespace Gmsantispam\Services\Provider;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Gmsantispam\Models\LogsModel;
use Gmsantispam\Models\StatisticsModel;

class ServiceProvider
{
    /**
     * Liefert eine Liste von Log-Einträgen.
     *
     * @param   array  $params  Parameter für die Abfrage.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getLogs($params = [])
    {
        $model = new LogsModel();
        $logs = $model->getItems($params);

        return $logs;
    }

    /**
     * Liefert einen bestimmten Log-Eintrag.
     *
     * @param   int  $id  Die ID des Log-Eintrags.
     *
     * @return  object|void
     *
     * @since   1.0.0
     */
    public function getLog($id)
    {
        $model = new LogsModel();
        $log = $model->getItem($id);

        if (!$log) {
            throw new \Exception(Text::_('LOG_ENTRY_NOT_FOUND'), 404);
        }

        return $log;
    }

    /**
     * Erstellt einen neuen Log-Eintrag.
     *
     * @param   array  $data  Die Daten für den neuen Log-Eintrag.
     *
     * @return  object|void
     *
     * @since   1.0.0
     */
    public function createLog($data)
    {
        $model = new LogsModel();
        $log = $model->save($data);

        if (!$log) {
            throw new \Exception(Text::_('FAILED_TO_CREATE_LOG_ENTRY'), 500);
        }

        return $log;
    }

    /**
     * Aktualisiert einen bestehenden Log-Eintrag.
     *
     * @param   int   $id   Die ID des Log-Eintrags.
     * @param   array  $data  Die aktualisierten Daten.
     *
     * @return  object|void
     *
     * @since   1.0.0
     */
    public function updateLog($id, $data)
    {
        $model = new LogsModel();
        $log = $model->save($data, $id);

        if (!$log) {
            throw new \Exception(Text::_('FAILED_TO_UPDATE_LOG_ENTRY'), 500);
        }

        return $log;
    }

    /**
     * Löscht einen Log-Eintrag.
     *
     * @param   int  $id  Die ID des Log-Eintrags.
     *
     * @return  bool
     *
     * @since   1.0.0
     */
    public function deleteLog($id)
    {
        $model = new LogsModel();
        $result = $model->delete($id);

        if (!$result) {
            throw new \Exception(Text::_('FAILED_TO_DELETE_LOG_ENTRY'), 500);
        }

        return true;
    }

    /**
     * Liefert Statistiken zu Spam- und Ham-Meldungen.
     *
     * @param   array  $params  Parameter für die Abfrage.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getStatistics($params = [])
    {
        $model = new StatisticsModel();
        $statistics = $model->getStatistics($params);

        return $statistics;
    }

    /**
     * Liefert eine spezifische Statistik.
     *
     * @param   string  $type  Der Typ der Statistik (z.B. 'spam', 'ham').
     *
     * @return  object|void
     *
     * @since   1.0.0
     */
    public function getStatistic($type)
    {
        $model = new StatisticsModel();
        $statistic = $model->getStatistic($type);

        if (!$statistic) {
            throw new \Exception(Text::_('STATISTIC_NOT_FOUND'), 404);
        }

        return $statistic;
    }
}
