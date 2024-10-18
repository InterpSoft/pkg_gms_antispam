<?php
namespace Gmsantispam\Admin\Api\Controller;

use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\Utilities\ArrayHelper;
use Gmsantispam\Admin\Model\StatisticsModel;

class StatisticsController extends ApiController
{
    /**
     * Liefert Statistiken zu Spam- und Ham-Meldungen.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getStatistics()
    {
        $model = new StatisticsModel();
        $statistics = $model->getStatistics();

        return $this->setResponse($statistics);
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
            $this->setError(404, 'Statistik nicht gefunden.');
            return;
        }

        return $this->setResponse($statistic);
    }

    /**
     * Liefert die Top-X-Statistiken für Spam- oder Ham-Meldungen.
     *
     * @param   string  $type  Der Typ der Statistik (z.B. 'spam', 'ham').
     * @param   int     $limit Die Anzahl der Einträge, die zurückgegeben werden sollen.
     *
     * @return  array|void
     *
     * @since   1.0.0
     */
    public function getTopStatistics($type, $limit = 10)
    {
        $model = new StatisticsModel();
        $topStatistics = $model->getTopStatistics($type, $limit);

        if (!$topStatistics) {
            $this->setError(404, 'Keine Top-Statistiken gefunden.');
            return;
        }

        return $this->setResponse($topStatistics);
    }

    /**
     * Liefert die Zeitreihe-Statistik für Spam- oder Ham-Meldungen.
     *
     * @param   string  $type  Der Typ der Statistik (z.B. 'spam', 'ham').
     * @param   string  $range  Der Zeitraum für die Statistik (z.B. 'daily', 'weekly', 'monthly').
     *
     * @return  array|void
     *
     * @since   1.0.0
     */
    public function getTimeSeriesStatistics($type, $range)
    {
        $model = new StatisticsModel();
        $timeSeriesStatistics = $model->getTimeSeriesStatistics($type, $range);

        if (!$timeSeriesStatistics) {
            $this->setError(404, 'Keine Zeitreihe-Statistik gefunden.');
            return;
        }

        return $this->setResponse($timeSeriesStatistics);
    }
}
