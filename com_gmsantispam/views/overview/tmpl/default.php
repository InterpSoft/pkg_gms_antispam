<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
// Load the necessary Joomla framework
JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.framework');

// Sample data for tabs
$tabs = [
    'logs' => 'Erster Tab',
    'statistics' => 'Zweiter Tab',
];

// Get the active tab from the request or default to the first one
$activeTab = Factory::getApplication()->input->get('tab', 'logs');
?>

<div class="tabbable">
    <ul class="nav nav-tabs" id="myTab">
        <?php foreach ($tabs as $key => $title): ?>
            <li class="<?php echo $key === $activeTab ? 'active' : ''; ?>">
                <a href="<?php echo JRoute::_('index.php?option=com_gmsantispam&view=overview&tab=' . $key); ?>">
                    <?php echo $title; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">
        <div class="tab-pane <?php echo $activeTab === 'logs' ? 'active' : ''; ?>" id="logs">
            <?php
            // Hier wird die Logs-View angezeigt
            #$logsView = new GmsantispamViewLogs(); // Instanziiere die Logs-View
            $logsView = $this->getView('logs', 'html'); // Holen der Logs-View
            $logsView->setLayout('default'); // Setze das Layout auf 'default'
            $logsView->display(); // Zeige die Logs-View an
            ?>
            <?php #echo $this->loadTemplate('logs'); // Lädt die Ansicht für den ersten Tab ?>
        </div>
        <div class="tab-pane <?php echo $activeTab === 'statistics' ? 'active' : ''; ?>" id="statistics">
            <?php echo $this->loadTemplate('statistics'); // Lädt die Ansicht für den zweiten Tab ?>
        </div>
    </div>
</div>