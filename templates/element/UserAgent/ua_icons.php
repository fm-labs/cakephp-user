<?php
$ua = (isset($ua)) ? $ua : null;
$template = (isset($template)) ? $template : 'icon';
$this->loadHelper('User.UserAgent');
$this->UserAgent->set($ua);
?>
<?= $this->UserAgent->name(null, $template); ?>&nbsp;
<?= $this->UserAgent->os(null, $template); ?>&nbsp;
<?= $this->UserAgent->device(null, $template); ?>&nbsp;
<?= $this->UserAgent->model(null, $template); ?>&nbsp;
<?= $this->UserAgent->bot(null, $template); ?>&nbsp;
