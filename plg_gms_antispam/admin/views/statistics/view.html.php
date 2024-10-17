<?php
/*defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

class HoneypotViewStatistics extends HtmlView
{
    protected $statistics;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->statistics = $model->getStatistics();

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JToolbarHelper::title(JText::_('PLG_CAPTCHA_HONEYPOT_STATISTICS'), 'chart');
    }
}*/
?>

<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_honeypot
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * View to edit Honeypot Statistics
 *
 * @since  1.0
 */
class HoneypotViewStatistics extends JViewLegacy
{
    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        // Get data from the model
        $this->stats = $this->get('Statistics');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));

            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolBar()
    {
        $title = JText::_('COM_HONEYPOT_MANAGER_STATISTICS');
        $user = JFactory::getUser();

        if ($this->getLayout() !== 'modal') {
            JToolBarHelper::title($title, 'honeypot');
        } else {
            JToolBarHelper::title($title, 'honeypot.png');
        }

        if ($user->authorise('core.admin', 'com_honeypot')) {
            JToolBarHelper::preferences('com_honeypot');
        }
    }
}