<?php $this->loadHelper('Backend.DataTable'); ?>
<?php $this->loadHelper('Backend.FlagIcon'); ?>
<?php
$table = [
    'modelClass' => 'User.UserSessions',
    'fields' => [
        'user.username' => ['label' => 'Username'],
        'sessionid',
        'user_agent' => ['formatter' => function ($val) {
            if ($val) {
                return $this->element('User.UserAgent/ua_icons', ['ua' => $val, 'template' => 'icon']);
            }

            return $val;
        }],
        'client_ip',
        'geo_country_code' => ['formatter' => function ($val) {
            if ($val) {
                return $this->FlagIcon->create($val, ['title' => $val, 'data-toggle' => 'tooltip']);
            }

            return '-';
        }],
        'geo_location' => ['formatter' => function ($val) {
            if (!empty($val)) {
                return $this->element('User.GeoIp/geo_location', ['location' => $val]);
            }

            return '-';
        }],
        'created',
    ],
    'fieldsWhitelist' => [
        //'user.username', 'created', 'user_agent', 'client_ip'
    ],
    'ajax' => false,
    'paginate' => false,
    'actions' => false,
    'rowActions' => [
        ['View', ['plugin' => 'User', 'controller' => 'UserSessions', 'action' => 'view', ':id']]
    ]
];
echo $this->DataTable->create($table, $sessions)->render();
