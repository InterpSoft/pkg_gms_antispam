<?php
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6">
        <h3><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_STATS_LAST_30_DAYS'); ?></h3>
        <table class="table table-striped">
            <tr>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_STATS_TOTAL_ATTEMPTS'); ?></th>
                <td><?php echo $this->statistics->total_attempts; ?></td>
            </tr>
            <tr>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_STATS_HONEYPOT_FILLED'); ?></th>
                <td><?php echo $this->statistics->honeypot_filled; ?></td>
            </tr>
            <tr>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_STATS_TOO_FAST'); ?></th>
                <td><?php echo $this->statistics->too_fast; ?></td>
            </tr>
            <tr>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_STATS_SUSPICIOUS_REQUEST'); ?></th>
                <td><?php echo $this->statistics->suspicious_request; ?></td>
            </tr>
            <tr>
                <th><?php echo JText::_('PLG_CAPTCHA_HONEYPOT_STATS_UNIQUE_IPS'); ?></th>
                <td><?php echo $this->statistics->unique_ips; ?></td>
            </tr>
        </table>
    </div>
</div>