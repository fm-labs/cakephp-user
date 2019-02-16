<?php

namespace User\View\Helper;

use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class AutologoutHelper extends Helper
{
    public $helpers = ['Html'];

    /**
     * @param Event $event The event object
     * @return void
     */
    public function beforeLayout(Event $event)
    {
        if ($this->request->session()->check('Auth.UserSession')) {
            //$this->Html->script('/user/js/autologout.js', ['block' => true]);

            $script = <<<SCRIPT
(function() {
    console.log("Loading user script: autologout.js");

    setInterval(function() {

        $.getJSON('{{CHECK_URL}}', function(data) {
            console.log("CheckAuth", data)

            if (!data || !data.l || data.l !== 1) {
              console.log("Logged out. Redirecting to", "{{LOGIN_URL}}");
              window.location.href = '{{LOGIN_URL}}';
            }

        })
        .fail(function(xhr) {
            console.log("CheckAuth failed", xhr);
            if (xhr.status == 401 || xhr.status == 403) {
              console.log("Redirecting to", "{{LOGIN_URL}}");
              window.location.href = '{{LOGIN_URL}}';
            }
        });

    }, 10000);
})();
SCRIPT;
            $script = str_replace(
                ['{{LOGIN_URL}}', '{{CHECK_URL}}'],
                [
                    Router::url(['_name' => 'user:login']),
                    Router::url(['_name' => 'user:checkauth']),
                ],
                $script
            );

            $this->Html->scriptBlock($script, ['safe' => false, 'block' => true]);
        }
    }
}
