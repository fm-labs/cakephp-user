<?php
declare(strict_types=1);

namespace User\Controller\Admin;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use User\Mailer\UserMailerAwareTrait;

/**
 * Users Controller
 *
 * @property \User\Model\Table\UsersTable $Users
 * @property \Admin\Controller\Component\ActionComponent $Action
 */
class UsersController extends AppController
{
    use UserMailerAwareTrait;

    public ?string $defaultTable = 'User.Users';

    /**
     * @var array
     */
    public array $actions = [
        'index' => 'Admin.Index',
        'view' => 'Admin.View',
        'add' => 'Admin.Add',
        'edit' => 'Admin.Edit',
        'delete' => 'Admin.Delete',
    ];

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        //$this->Action->registerInline('password_change', [
        //    'label' => __d('user', 'Change password'),
        //    'attrs' => ['data-icon' => 'key'],
        //    'scope' => ['form', 'table']]);
        $this->Action->registerInline('password_reset', [
            'label' => __d('user', 'Set password'),
            'attrs' => ['data-icon' => 'key'],
            'scope' => ['form', 'table']]);
        $this->Action->registerInline('emails', [
            'label' => __d('user', 'Emails'),
            'attrs' => ['data-icon' => 'envelope-o'],
            'scope' => ['form', 'table']]);

        $this->loadComponent('User.Auth');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            //'contain' => ['UserGroups'],
            'order' => ['superuser' => 'DESC', 'username' => 'ASC'],
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
            'created',
        ]);
        $this->set('filter', false);
        $this->set('toolbar.actions', [
            [
                __d('user', 'View User Groups'),
                ['plugin' => 'User', 'controller' => 'UserGroups', 'action' => 'index'],
                ['data-icon' => 'users'],
            ],
        ]);

        $this->Action->execute();
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add()
    {
        $action = $this->Action->getAction('add');
        $action->setConfig('allowAccess', '*');
        $action->setConfig('exclude', [
            //'password',
            'email_verification_expiry_timestamp', 'password_expiry_timestamp', 'password_change_timestamp',
            'password_reset_expiry_timestamp', 'login_last_login_datetime', 'login_failure_datetime',
            'login_last_login_ip', 'login_last_login_host',
            'block_datetime', 'gauth_last_verify_datetime',
        ]);

        return $this->Action->dispatch($action);
        //$this->Action->execute();
    }

    /**
     * Edit method
     *
     * @return void
     */
    public function edit()
    {
        $this->set('fields.access', '*');
        $this->set('fields.blacklist', [
            'password',
            'email_verification_expiry_timestamp', 'password_expiry_timestamp', 'password_change_timestamp',
            'password_reset_expiry_timestamp', 'login_last_login_datetime', 'login_failure_datetime',
            'login_last_login_ip', 'login_last_login_host',
            'block_datetime', 'gauth_last_verify_datetime',
        ]);
        $this->Action->execute();
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $this->set('related', ['UserGroups']);
        $this->set('entityOptions', ['contain' => ['UserGroups']]);
        $this->set('fields', [
            'email' => ['formatter' => 'email'],
            'password_reset_url' => ['formatter' => 'link'],
        ]);
        $this->set('fields.blacklist', ['password']);
        $this->Action->execute();
    }

    /**
     * Change password of current user
     *
     * @param $userId
     * @return \Cake\Http\Response|void
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
            if ($this->Users->changePassword($user, $this->request->getData())) {
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
     *
     * @param $userId
     * @return \Cake\Http\Response|void
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
            if ($this->Users->setPassword($user, $this->request->getData())) {
                $this->Flash->success(__d('user', 'Your password has been changed.'));
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('user', 'Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * User emails
     *
     * @param $id
     * @return \Cake\Http\Response|void
     */
    public function emails($id = null)
    {
        $emailTypes = array_keys((array)Configure::read('User.Email'));
        $emailTypes = array_combine($emailTypes, $emailTypes);

        $defaultLang = I18n::getDefaultLocale();
        $availableLangs = array_keys((array)Configure::read('Multilang.Locales'));

        $user = $this->Users->get($id);
        if ($this->request->is('post')) {
            $emailType = $this->request->getData('email_type');
            if (array_key_exists($emailType, $emailTypes)) {
                $mailer = $this->getUserMailer();

                if ($this->request->getData('debug_only')) {
                    $this->Flash->info('Debug Only');

                    /*
                    if (Plugin::isLoaded('Mailman')) {
                        $mailerConfig = ['originalClassName' => 'Debug'];
                        $mailer->transport(new \Mailman\Mailer\Transport\MailmanTransport($mailerConfig));
                    } else {
                        $mailer->transport(new DebugTransport());
                    }
                    */
                }
                $result = $mailer->send($emailType, [$user]);
                $this->set('result', $result);

                $this->Flash->success("Sent email of type $emailType to $user->email");
            } else {
                $this->Flash->error('Invalid email type');
            }
        }

        $this->set('user', $user);
        $this->set('emailTypes', $emailTypes);
    }
}
