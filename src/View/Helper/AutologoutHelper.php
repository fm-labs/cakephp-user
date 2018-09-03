<?php

namespace User\View\Helper;

use Cake\Event\Event;
use Cake\View\Helper;

class AutologoutHelper extends Helper
{
    public $helpers = ['Html'];

    public function beforeLayout(Event $event)
    {
        debug("beforeLayout");

        if ($this->request->session()->check('Auth.UserSession')) {
            debug("user session available");
            $this->Html->script('/user/js/autologout.js', ['block' => true]);
        }
    }
}
