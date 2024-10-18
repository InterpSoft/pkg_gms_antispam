<?php
defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Gmsantispam\Admin\Controller\LogsController;
use Gmsantispam\Admin\Controller\StatisticsController;

class GmsantispamComponent extends BaseController implements ComponentInterface
{
    /**
     * Der Name der Komponente.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $name = 'Gmsantispam';

    /**
     * Der Dispatcher für die Komponente.
     *
     * @var    ComponentDispatcher
     * @since  1.0.0
     */
    protected $dispatcher;

    /**
     * Konstruktor.
     *
     * @param   array  $config  An array of configuration options.
     *
     * @since   1.0.0
     * @throws  \Exception
     */
    public function __construct($config = array())
    {
        // Der Name der Komponente.
        $this->name = 'Gmsantispam';

        // Der Dispatcher für die Komponente.
        $this->dispatcher = new ComponentDispatcher($this);

        // Die Konfiguration der Komponente.
        $this->setConfig($config);

        // Die Sprachdatei laden.
        $this->loadLanguage();

        // Der Parent-Konstruktor aufrufen.
        parent::__construct($this->dispatcher, $config);
    }

    /**
     * Die Komponente initialisieren.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function initialize()
    {
        // Der Parent-Initialisierer aufrufen.
        parent::initialize();

        // Die Komponente-Konfiguration laden.
        $params = ComponentHelper::getParams($this->name);

        // Die Datenbank-Instanz laden.
        $this->db = Factory::getDbo();
    }

    /**
     * Die Haupt-Aktion für die Komponente ausführen.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function execute()
    {
        // Die Aktion ausführen.
        $this->dispatcher->execute($this->input->getCmd('task', 'display'));
    }

    /**
     * Die Benutzer-Autorisierung für die Komponente überprüfen.
     *
     * @param   string  $task  Die Aktion, die ausgeführt werden soll.
     *
     * @return  void
     *
     * @since   1.0.0
     * @throws  \Exception
     */
    public function checkAccess($task)
    {
        // Die Benutzer-Gruppe laden.
        $user = Factory::getUser();
        $groupId = $user->get('aid');

        // Die Autorisierung für die Komponente überprüfen.
        if ($groupId != 8) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }
    }

    /**
     * Die Komponente-Konfiguration laden.
     *
     * @return  object
     *
     * @since   1.0.0
     */
    public function getParams()
    {
        // Die Komponente-Konfiguration laden.
        $params = ComponentHelper::getParams($this->name);

        return $params;
    }

    /**
     * Die Logs-Controller-Instanz laden.
     *
     * @return  LogsController
     *
     * @since   1.0.0
     */
    public function getLogsController()
    {
        return new LogsController();
    }

    /**
     * Die Statistics-Controller-Instanz laden.
     *
     * @return  StatisticsController
     *
     * @since   1.0.0
     */
    public function getStatisticsController()
    {
        return new StatisticsController();
    }
}