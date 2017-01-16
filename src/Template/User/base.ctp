<style>
    .user-view {
        max-width: 500px;
        margin: 0 auto;
    }
    form .required label::after {
        content:"*"
    }
</style>
<div class="user-view view">
    <?= $this->Flash->render('auth'); ?>
    <h1 class="heading"><?= $this->fetch('heading', $this->fetch('title')); ?></h1>
    <?= $this->fetch('content'); ?>
</div>