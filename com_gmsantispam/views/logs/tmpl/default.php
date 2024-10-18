<?php if (!empty($this->logs) && is_array($this->logs)): ?>
    <form action="<?php echo JRoute::_('index.php?option=com_gmsantispam&view=logs'); ?>" method="post" name="adminForm"
        id="adminForm">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('COM_GMSANTISPAM_LOGS_HEADER_IP'); ?></th>
                    <th><?php echo JText::_('COM_GMSANTISPAM_LOGS_HEADER_REASON'); ?></th>
                    <th><?php echo JText::_('COM_GMSANTISPAM_LOGS_HEADER_DATE'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->logs as $log): ?>
                    <tr>
                        <td><?php echo $log->ip; ?></td>
                        <td><?php echo $log->reason; ?></td>
                        <td><?php echo $log->date; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
<?php else: ?>
    <p><?php echo JText::_('COM_GMSANTISPAM_NO_LOGS_FOUND'); ?></p>
<?php endif; ?>