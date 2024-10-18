<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplication;

/**
 * View to edit GMS AntiSpam Logs
 *
 * @since 1.0
 */
class GmsantispamViewLogs extends HtmlView
{
    protected $logs;
    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // Get data from the model
        #$this->items = $this->get('Items');
        // Lade die Logs aus dem Modell
        $this->logs = $this->get('Logs');

        // Check for errors.
        $errors = $this->get('Errors');
        if (count($errors ?? [])) {
            throw new \Exception(implode('<br />', $errors), 500);
        }

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since 1.6
     */
    protected function addToolBar()
    {
        $title = JText::_('COM_HONEYPOT_MANAGER_LOGS');
        $user = Factory::getUser();  // Verwende Factory::getUser() statt den DI-Container

        if ($this->getLayout() !== 'modal') {
            JToolBarHelper::title($title, 'gmsantispam');
        } else {
            JToolBarHelper::title($title, 'honeypot.png');
        }

        if ($user->authorise('core.admin', 'com_gmsantispam')) {
            JToolBarHelper::preferences('com_gmsantispam');
        }
    }
}