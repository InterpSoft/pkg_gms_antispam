<?php
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_honeypot&view=logs'); ?>" method="post" name="adminForm"
    id="adminForm">
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%"><?php echo JHtml::_('grid.checkall'); ?></th>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_LOG_DATE'); ?></th>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_LOG_IP'); ?></th>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_LOG_REASON'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->logs as $i => $log): ?>
                <tr>
                    <td><?php echo JHtml::_('grid.id', $i, $log->id); ?></td>
                    <td><?php echo $log->date; ?></td>
                    <td><?php echo $log->ip; ?></td>
                    <td><?php echo $log->reason; ?></td>
                </tr>
            <?php endforeach; ?>

            <?php foreach ($this->items as $log): ?>
                <tr>
                    <td><?php echo $log->ip; ?></td>
                    <td><?php echo $log->reason; ?></td>
                    <td><?php echo $log->form_id; ?></td>
                    <td><?php echo $log->date; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>