<?php

namespace User;

use Settings\SettingsInterface;
use Settings\SettingsManager;

class Settings implements SettingsInterface
{
    public function buildSettings(SettingsManager $settings)
    {
        $settings->load('User.settings');
    }
}