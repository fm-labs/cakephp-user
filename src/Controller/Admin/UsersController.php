<?php
namespace User\Controller\Admin;
use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \User\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * @var array
     */
    public $actions = [
        'index' => 'Backend.Index',
        'view' => 'Backend.View',
        'add' => 'Backend.Add',
        'edit' => 'Backend.Edit',
        'delete' => 'Backend.Delete'
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Action->registerInline('password_change', ['label' => __d('user', 'Change password'), 'data-icon' => 'key']);
        $this->Action->registerInline('password_reset', ['label' => __d('user', 'Reset password'), 'data-icon' => 'key']);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['UserGroups'],
            'order' => ['superuser' => 'DESC', 'username' => 'ASC']
        ];

        $this->set('fields', [
            'superuser',
            'username' => ['formatter' => function ($val, $row, $args, $view) {
                return $view->Html->link(
                    $val,
                    ['action' => 'edit', $row->id]
                );
            }],
            'user_group' => ['formatter' => function ($val, $row, $args, $view) {
                if ($val) {
                    return $view->Html->link(
                        $val->name,
                        ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'edit', $val->id]
                    );
                }
            }],
            //'email',
            'login_enabled',
            'created'
        ]);
        $this->set('filter', false);

        $this->Action->execute();
    }

    public function add()
    {
        $this->set('fields.access', '*');
        $this->set('fields.blacklist', [
            'password',
            'email_verification_expiry_timestamp', 'password_expiry_timestamp', 'password_change_timestamp',
            'password_reset_expiry_timestamp', 'login_last_login_datetime', 'login_failure_datetime',
            'login_last_login_ip', 'login_last_login_host',
            'block_datetime', 'gauth_last_verify_datetime'
        ]);
        $this->Action->execute();
    }

    public function edit()
    {
        $this->set('fields.access', '*');
        $this->set('fields.blacklist', [
            'password',
            'email_verification_expiry_timestamp', 'password_expiry_timestamp', 'password_change_timestamp',
            'password_reset_expiry_timestamp', 'login_last_login_datetime', 'login_failure_datetime',
            'login_last_login_ip', 'login_last_login_host',
            'block_datetime', 'gauth_last_verify_datetime'
        ]);
        $this->Action->execute();
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->set('entityOptions', ['contain' => ['UserGroups']]);
        $this->set('fields', [
            'email' => ['formatter' => 'email'],
            'password_reset_url' => ['formatter' => 'link']
        ]);
        $this->set('fields.blacklist', ['password']);
        $this->Action->execute();
    }

    /**
     * Change password of current user
     * @param null $userId
     * @return \Cake\Network\Response|void
     */
    public function passwordChange($userId = null)
    {
        $authUserId = $this->Auth->user('id');
        if ($userId === null) {
            $userId = $authUserId;
        } elseif ((int)$userId !== (int)$authUserId) {
            $this->Flash->error(__d('user', 'You are not allowed to do this'));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
                $this->Flash->success(__d('user', 'Your password has been changed.'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * Change password of current user
     * @param null $userId
     * @return \Cake\Network\Response|void
     */
    public function passwordReset($userId = null)
    {
        $authUserId = $this->Auth->user('id');
        if ($userId === null) {
            $userId = $authUserId;
        } elseif ($userId !== $authUserId && $authUserId !== 1) {
            $this->Flash->error(__d('user', 'Only root can do this'));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->resetPassword($user, $this->request->data)) {
                $this->Flash->success(__d('user', 'Your password has been changed.'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }
}
