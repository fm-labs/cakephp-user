<?php
use Cake\Core\Configure;

if (!Configure::read('debug')) {
    return;
} elseif (!isset($template)) {
    return 'Customize: No \'template\' path set';
}

?>
<p style="margin: 1em 0; padding: 1em; background-color: #F8F8F8;">
    <span style="font-style: italic; font-size: 90%;">Debug Notice</span><br />
    If you want to customize this page, create
    <span style="font-style: italic;">'<?= $template ?>'</span>
</p>
