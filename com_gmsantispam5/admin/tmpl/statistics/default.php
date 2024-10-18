<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_gmsantispam&view=statistics'); ?>" method="post"
    name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <!-- Überschrift und Filter/Suche (falls erforderlich) -->
                <h1>Antispam-Statistik</h1>
                <form action="<?php echo Route::_('index.php?option=com_gmsantispam&view=statistics'); ?>" method="post"
                    name="adminForm" id="adminForm">
                    <!-- Filterfelder (Beispiel) -->
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <button class="btn btn-primary" type="submit">
                                <span class="icon-search" aria-hidden="true"></span> Suchen
                            </button>
                        </div>
                        <div class="btn-group">
                            <label for="filter_search">Suche:</label>
                            <input type="text" name="filter_search" id="filter_search"
                                value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
                        </div>
                        <!-- Weitere Filteroptionen (z.B. Datum, Typ) -->
                    </div>
                    <!-- Tabelle mit Log-Einträgen -->
                    <table class="table table-striped" id="articleList">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Datum</th>
                                <th scope="col">Typ</th>
                                <th scope="col">Details</th>
                                <!-- Weitere Spalten wie Benutzer, IP, etc. -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item): ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td><?php echo $i + 1; ?></td>
                                    <td><?php echo Joomla\Utilities\ArrayHelper::getValue($item, 'created'); ?></td>
                                    <td>
                                        <?php // Typ (z.B. Spam, Ham, etc.) ?>
                                        <?php echo Joomla\Utilities\ArrayHelper::getValue($item, 'type'); ?>
                                    </td>
                                    <td>
                                        <!-- Details zum Log-Eintrag (kürzen bei Bedarf) -->
                                        <?php echo Joomla\Utilities\ArrayHelper::getValue($item, 'details', '', 'string', Joomla\String\Punctuation::trim()); ?>
                                    </td>
                                    <!-- Weitere Tabellenspalten -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination (wenn erforderlich) -->
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </form>
            </div>
        </div>
    </div>
</form>