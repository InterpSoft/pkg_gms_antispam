<?php
namespace GmsAntispam\Component\Gmsantispam\Administrator\View\Statistics;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $statistics;

    public function display($tpl = null)
    {
        $this->statistics = $this->get('Statistics');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title('GMS Antispam: Statistics', 'chart');
    }
}
