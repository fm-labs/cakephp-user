<?php
namespace User\View\Cell;

use Cake\View\Cell;

/**
 * UserSessions cell
 *
 * @property \User\Model\Table\UserSessionsTable $UserSessions
 */
class UserSessionsCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
        $this->loadModel('User.UserSessions');

        $sessions = $this->UserSessions->find()
            ->contain(['Users'])
            ->limit(10)
            ->all()
            ->toArray();

        $this->set(compact('sessions'));
    }
}
