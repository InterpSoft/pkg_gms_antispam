<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class GmsantispamController extends BaseController
{
    /*public function display($cachable = false, $urlparams = array())
    {
        $view = $this->getView('overview', 'html');
        $view->display();
    }*/

    public function display($cachable = false, $urlparams = array())
    {
        // Setze den Default-View auf 'overview', falls nicht anders angegeben
        $view = $this->input->getCmd('view', 'overview');

        // Lade die View
        $viewInstance = $this->getView($view, 'html');

        // Setze die View-Model-Daten falls nötig
        #$viewInstance->setModel($this->getModel('overview'), true);

        // Rufe die Display-Methode auf
        $viewInstance->display();
    }

    /*public function display($cachable = false, $urlparams = array())
    {
        // Lade die View
        $view = $this->input->get('view', 'overview');
        $this->setRedirect(JRoute::_('index.php?option=com_gmsantispam&view=' . $view, false));

        // Hier könnte auch eine Überprüfung auf das Vorhandensein der View erfolgen
        parent::display($cachable, $urlparams);
    }*/
}