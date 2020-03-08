<?php

namespace User\View\Helper;

use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class UserSessionHelper extends Helper
{
    public $helpers = ['Html'];

    protected $_defaultConfig = [
        'sessionKey' => 'Auth.UserSession',
        'checkUrl' => ['_name' => 'user:checkauth'],
        'loginUrl' => ['_name' => 'user:login'],
    ];

    /**
     * @param Event $event The event object
     * @return void
     */
    public function beforeLayout(Event $event)
    {
        if ($this->request->getSession()->check($this->getConfig('sessionKey'))) {
            $script = <<<SCRIPT
(function($, _) {
    function updateSessionInfo() {
        $.getJSON('{{CHECK_URL}}', function(data) {
          var event = $.Event('user.session.update');
          $(window).trigger(event, [ data ]);
        })
        .fail(function(xhr) {
            if (xhr.status == 401 || xhr.status == 403) {
              console.log("User session check failed with status", xhr.status);

              var event = $.Event('user.session.error');
              $(window).trigger('user.session.error', [ xhr.status ]);
            }
        });
    }

    setInterval(function() {
        updateSessionInfo();
    }, 60000);

    $(window).on('user.session.update', function(ev, data) {
        //console.log("user.session.update", data, ev);
    });
    $(window).on('user.session.error', function(ev, status) {
        window.location.href = '{{LOGIN_URL}}';
    });
})(jQuery, _);
SCRIPT;
            $script = str_replace(
                ['{{LOGIN_URL}}', '{{CHECK_URL}}'],
                [
                    Router::url($this->getConfig('loginUrl')),
                    Router::url($this->getConfig('checkUrl')),
                ],
                $script
            );

            $this->Html->scriptBlock($script, ['safe' => false, 'block' => true]);
        }
    }
}
