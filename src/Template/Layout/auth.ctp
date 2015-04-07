<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<header>
    <div class="header-title">
        Auth:
        <span><?= $this->fetch('title') ?></span>
    </div>
    <div class="header-help">
    </div>
</header>
<div id="container">

    <div id="content">
        <?= $this->Flash->render() ?>
        <?= $this->Flash->render('auth') ?>

        <div class="row">
            <?= $this->fetch('content') ?>
        </div>
    </div>
    <footer>
    </footer>
</div>
</body>
</html>
