<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

class GmsantispamViewOverview extends HtmlView
{
    public function display($tpl = null)
    {
        // Hier kannst du Daten für die Overview-Ansicht abrufen
        #$this->logs = $this->get('Logs');
        // Lade das Logs-Layout
        #$this->setLayout('logs'); // Hier wird das Layout gesetzt

        // Hier kannst du Daten laden und das Template rendern
        parent::display($tpl);
    }
}