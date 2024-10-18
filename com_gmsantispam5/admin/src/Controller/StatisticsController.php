<?php
namespace GmsAntispam\Component\Gmsantispam\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class StatisticsController extends BaseController
{
    public function display($cachable = false, $urlparams = array())
    {
        $this->input->set('view', 'statistics');

        parent::display($cachable, $urlparams);

        return $this;
    }
}
