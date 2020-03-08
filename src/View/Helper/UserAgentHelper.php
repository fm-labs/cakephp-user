<?php
namespace User\View\Helper;

use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * UserAgent helper
 *
 */
class UserAgentHelper extends Helper
{
    use StringTemplateTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * @var \User\Util\UserAgent
     */
    protected $_userAgent;

    /**
     * @var string User agent string
     */
    protected $_ua;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $this->templater()->add([
            'ua_label' => '<span class="ua-info-os" data-toggle="tooltip" data-title="{{content}}" {{attrs}}>{{label}}: <i class="fa fa-{{icon}}"></i></span>',
            'ua_icon' => '<span class="ua-info-os" data-toggle="tooltip" data-title="{{content}}" {{attrs}}><i class="fa fa-{{icon}}"></i></span>',
        ]);

        $this->set($this->getView()->getResponse()->getHeaderLine('User-Agent'));
    }

    /**
     * Direct access to UserAgent instance
     *
     * @return \User\Util\UserAgent
     */
    public function userAgent()
    {
        if (!$this->_userAgent) {
            $this->_userAgent = new \User\Util\UserAgent($this->_ua, false);
        }

        return $this->_userAgent;
    }

    /**
     * Set user agent string
     *
     * @param string $ua User agent string
     * @return $this
     */
    public function set($ua)
    {
        if ($ua !== null) {
            $this->_ua = $ua;
            $this->_userAgent = null;
        }

        return $this;
    }

    /**
     * @param string $val Operating system name
     * @param string $template Template name suffix
     * @param array $options Additional options
     * @return string
     */
    public function os($val = null, $template = 'icon', $options = [])
    {
        if ($val === null) {
            $val = $this->userAgent()->os_name;
        }
        switch (strtolower($val)) {
            case 'ubuntu':
            case 'redhat':
            case 'debian':
            case 'linux':
                $icon = 'linux';
                break;
            case 'win':
            case 'windows':
                $icon = 'windows';
                break;
            case 'mac':
            case 'macintosh':
            case 'ios':
                $icon = 'apple';
                break;
            case 'android':
                $icon = 'android';
                break;
            default:
                $icon = 'question';
                break;
        }

        return $this->templater()->format('ua_' . $template, [
            'label' => __('OS'),
            'icon' => $icon,
            'content' => $val,
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }

    /**
     * @param string $val Device type
     * @param string $template Template name suffix
     * @param array $options Additional options
     * @return string
     */
    public function device($val = null, $template = 'icon', $options = [])
    {
        if ($val === null) {
            $val = $this->userAgent()->device;
        }
        switch (strtolower($val)) {
            case 'desktop':
                $icon = 'desktop';
                break;
            case 'phone':
            case 'smartphone':
                $icon = 'mobile';
                break;
            case 'tablet':
                $icon = 'tablet';
                break;
            default:
                $icon = 'question';
                break;
        }

        return $this->templater()->format('ua_' . $template, [
            'label' => __('Device'),
            'icon' => $icon,
            'content' => $val,
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }

    /**
     * @param string $val Device type
     * @param string $template Template name suffix
     * @param array $options Additional options
     * @return string
     */
    public function model($val = null, $template = 'icon', $options = [])
    {
        if ($val === null) {
            $val = $this->userAgent()->model;
        }
        switch (strtolower($val)) {
            case 'safari':
                $icon = 'compass';
                break;
            case 'ie':
                $icon = 'internet-explorer';
                break;
            case 'chrome':
            case 'chromium':
                $icon = 'chrome';
                break;
            case 'firefox':
                $icon = 'firefox';
                break;
            default:
                $icon = 'question';
                break;
        }

        return $this->templater()->format('ua_' . $template, [
            'label' => __('Model'),
            'icon' => $icon,
            'content' => $val,
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }

    /**
     * @param string $val Device type
     * @param string $template Template name suffix
     * @param array $options Additional options
     * @return string
     */
    public function bot($val = null, $template = 'icon', $options = [])
    {
        if ($val === null) {
            $val = $this->userAgent()->is_bot;
        }

        if (!$val) {
            return '';
        }

        //$icon = ($val == true) ? 'android' : 'close';
        $icon = 'android';

        return $this->templater()->format('ua_' . $template, [
            'label' => __('Bot'),
            'icon' => $icon,
            'content' => $val,
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }

    /**
     * @param string $val Device type
     * @param string $template Template name suffix
     * @param array $options Additional options
     * @return string
     */
    public function name($val = null, $template = 'icon', $options = [])
    {
        if ($val === null) {
            $val = $this->userAgent()->name;
        }

        if (!$val) {
            return '';
        }

        //$icon = ($val == true) ? 'android' : 'close';
        $icon = 'info';

        return $this->templater()->format('ua_' . $template, [
            'label' => '',
            'icon' => $icon,
            'content' => $val,
            'attrs' => $this->templater()->formatAttributes($options),
        ]);
    }
}
