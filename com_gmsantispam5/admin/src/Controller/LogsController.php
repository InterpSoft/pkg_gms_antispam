<?php
namespace GmsAntispam\Component\Gmsantispam\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;

class LogsController extends AdminController
{
    public function getModel($name = 'Log', $prefix = 'Administrator', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function delete()
    {
        // Check for request forgeries
        $this->checkToken();

        $cid = $this->input->get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1) {
            $this->setMessage(Text::_('COM_GMSANTISPAM_NO_ITEM_SELECTED'), 'warning');
        } else {
            $model = $this->getModel('Logs');

            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(Text::plural('COM_GMSANTISPAM_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError(), 'error');
            }
        }

        $this->setRedirect('index.php?option=com_gmsantispam&view=logs');
    }
}