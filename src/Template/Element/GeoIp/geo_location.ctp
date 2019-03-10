<?php
/**
 * Geo location element.
 * Displays geo location data array as country flag icon
 * with html tooltip
 */
?>
<?php $this->loadHelper('Backend.FlagIcon'); ?>
<div class="geo-location">
    <span class="geo-location-flag" data-toggle="tooltip-html">
        <?= $this->FlagIcon->create($location['country_iso2']); ?>
    </span>
    <div class="toggle-content-html" style="display: none;">
        <div>
            <?= h($location['country_name']); ?>
            <?= $this->FlagIcon->create($location['country_iso2']); ?>
            <ul style="margin: 0; padding: 5px;">
                <?php foreach ($location as $k => $v) : ?>
                    <li><strong><?= h($k) ?>:</strong>&nbsp;<?= h($v); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>