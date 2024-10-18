<?php
namespace Gmsantispam\Admin\Api\Controller;

use Joomla\CMS\MVC\Controller\ApiController;
use Joomla\CMS\Serializer\Serializer;
use Joomla\Utilities\ArrayHelper;
use Gmsantispam\Admin\Model\LogsModel;

class LogsController extends ApiController
{
    /**
     * Liefert eine Liste von Log-Einträgen.
     *
     * @return  array
     *
     * @since   1.0.0
     */
    public function getLogs()
    {
        $model = new LogsModel();
        $logs = $model->getItems();

        return $this->setResponse($logs);
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
            $this->setError(404, 'Log-Eintrag nicht gefunden.');
            return;
        }

        return $this->setResponse($log);
    }

    /**
     * Erstellt einen neuen Log-Eintrag.
     *
     * @return  object|void
     *
     * @since   1.0.0
     */
    public function post()
    {
        $model = new LogsModel();
        $data = Serializer::decode($this->input->json->getRaw(), Serializer::FORMAT_JSON);

        if (!$model->save($data)) {
            $this->setError(500, 'Fehler beim Erstellen des Log-Eintrags.');
            return;
        }

        return $this->setResponse($model->getItem($model->getState('log.id')));
    }

    /**
     * Aktualisiert einen bestehenden Log-Eintrag.
     *
     * @param   int  $id  Die ID des Log-Eintrags.
     *
     * @return  object|void
     *
     * @since   1.0.0
     */
    public function patch($id)
    {
        $model = new LogsModel();
        $data = Serializer::decode($this->input->json->getRaw(), Serializer::FORMAT_JSON);

        if (!$model->save($data, $id)) {
            $this->setError(500, 'Fehler beim Aktualisieren des Log-Eintrags.');
            return;
        }

        return $this->setResponse($model->getItem($id));
    }

    /**
     * Löscht einen Log-Eintrag.
     *
     * @param   int  $id  Die ID des Log-Eintrags.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function delete($id)
    {
        $model = new LogsModel();

        if (!$model->delete($id)) {
            $this->setError(500, 'Fehler beim Löschen des Log-Eintrags.');
            return;
        }

        $this->setResponse(['message' => 'Log-Eintrag erfolgreich gelöscht.']);
    }
}
