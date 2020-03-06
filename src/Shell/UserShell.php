<?php

namespace User\Shell;

use Cake\Console\Shell;

/**
 * @property \user\Model\Table\UsersTable $Users
 */
class UserShell extends Shell
{
    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->description(__d('user', "Manage user"))
            ->addSubcommand('add', [
                'help' => 'Add user'
            ])
            ->addOption('email', [
                'help' => 'User email',
                'short' => 'e'
            ])
            ->addOption('password', [
                'help' => 'User password',
                'short' => 'p'
            ]);

        return $parser;
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->out("-- Setup root user --");
        foreach ($this->args as $key => $val) {
            $this->out("Arg: $key - $val");
        }

        debug($this->params);

        $email = $this->param('email');
        $password = $this->param('password');

        $this->loadModel('User.Users');

        while (strlen($email) < 1) {
            $email = trim($this->in("Enter email address: "));
            $strlen = strlen($email);
        }

        while (strlen($password) < 1) {
            $pass1 = trim($this->in("Enter password for user: "));
            if (strlen($pass1) < 1) {
                $this->out("Please enter a password");
                continue;
            }

            $pass2 = trim($this->in("Repeat password: "));

            $match = ($pass1 === $pass2);
            if (!$match) {
                $this->out("Passwords do not match. Please try again.");
                continue;
            }

            $password = $pass1;
        };

        $data = [
            'superuser' => false,
            'username' => $email,
            'email' => $email,
            'password' => $password,
            'login_enabled' => true,
            'email_verification_required' => false,
        ];

        $user = $this->Users->newEntity();
        $user->accessible(array_keys($data), true);
        $user = $this->Users->patchEntity($user, $data);

        if (!$this->Users->save($user)) {
            debug($user->getErrors());
            $this->abort("Failed to create user");
        }

        $this->out("<success>User added!</success>");
    }
}
