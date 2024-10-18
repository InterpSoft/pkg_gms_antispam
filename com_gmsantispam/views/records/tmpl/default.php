<?php
defined('_JEXEC') or die;
?>
<h1>GMS AntiSpam Records</h1>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Datum</th>
            <th>IP</th>
            <!-- Weitere Felder -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $item): ?>
            <tr>
                <td><?php echo $item->id; ?></td>
                <td><?php echo $item->date; ?></td>
                <td><?php echo $item->ip; ?></td>
                <!-- Weitere Felder -->
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>