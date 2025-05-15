<?php
declare(strict_types=1);

namespace User\Util;

use Cake\Utility\Inflector;
use DeviceDetector\DeviceDetector;

/**
 * @property string $name
 * @property bool $is_bot
 * @property string $os_name
 * @property string $os_version
 * @property string $os_platform
 * @property string $device
 * @property string $brand
 * @property string $model
 */
class UserAgent
{
    /**
     * @var array Parsed user agent info
     */
    protected array $_details = [
        'is_bot' => null,
        'os_name' => null,
        'os_version' => null,
        'os_platform' => null,
        'device' => null,
        'brand' => null,
        'model' => null,
    ];

    /**
     * @var string Raw user agent string
     */
    protected string $_ua;

    /**
     * Constructor.
     *
     * @param string $ua User agent string
     * @param bool $useDeviceDetector If TRUE, Piwik/Matomo's DeviceDetector is used
     */
    public function __construct(string $ua, bool $useDeviceDetector = true)
    {
        $this->_ua = $ua;

        if ($useDeviceDetector && class_exists('\DeviceDetector\DeviceDetector')) {
            $parsed = $this->parseWithDeviceDetector($ua);
            $this->_set($parsed);
        }

        // always fallback to simple parser
        $parsed = $this->parseSimple($ua);
        $this->_set($parsed);
    }

    /**
     * Merges parsed details without overwriting already known details
     *
     * @param array $parsed Parse result
     * @return void
     */
    protected function _set(array $parsed): void
    {
        foreach ($parsed as $k => $v) {
            // skip empty values
            if (!$v) {
                continue;
            }

            if (!isset($this->_details[$k])) {
                $this->_details[$k] = $v;
            }
        }
    }

    /**
     * Magic property accessor
     *
     * @param string $key Property name
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        if ($key == 'name') {
            return $this->_ua;
        }

        if (isset($this->_details[$key])) {
            return $this->_details[$key];
        }

        return null;
    }

    /**
     * Magic property accessor
     *
     * @param string $method Method name
     * @param array $args Method args
     * @return mixed
     */
    public function __call(string $method, array $args): mixed
    {
        if (preg_match('/^([\w]+)$/', $method, $matches)) {
            $key = Inflector::underscore($matches[1]);

            return $this->__get($key);
        }

        return null;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->_details;
    }

    /**
     * Parse the user agent string
     *
     * @param string $ua User agent string
     * @return array
     */
    protected function parseSimple(string $ua): array
    {
        $isBot = false;
        $osName = $osVersion = $osPlatform = null;
        $device = $brand = $model = null;

        if (preg_match('/(bot|crawler|spider)/i', $ua)) {
            $isBot = true;
        }

        if (!$isBot) {
            if (preg_match('/(android)/i', $ua)) {
                $osName = 'Android';
                $device = 'phone';
            } elseif (preg_match('/(ios|ipad|iphone)/i', $ua)) {
                $osName = 'iOS';
                $device = 'phone';
            } elseif (preg_match('/(windows|wow)/i', $ua)) {
                $osName = 'Windows';
                $device = 'desktop';

                if (preg_match('/(windows\sNT\s[0-9\.]+);/i', $ua, $matches)) {
                    $osVersion = $matches[1];
                }
            } elseif (preg_match('/(macintosh|intel\smac)/i', $ua)) {
                $osName = 'Macintosh';
                $device = 'desktop';
            } elseif (preg_match('/(linux)/i', $ua)) {
                $osName = 'Linux';
                $device = 'desktop';
            }

            if (preg_match('/(i686)/i', $ua)) {
                $osPlatform = 'i686';
            } elseif (preg_match('/(i386)/i', $ua)) {
                $osPlatform = 'i386';
            } elseif (preg_match('/(win64|ia64|x64|x64_86|wow64)/i', $ua)) {
                $osPlatform = 'x64';
            }

            if (preg_match('/(msie|trident|windows-rss|edge)/i', $ua)) {
                $brand = 'Microsoft';
                $model = 'IE';
            } elseif (preg_match('/(chromium\/)/i', $ua)) {
                $brand = 'Google';
                $model = 'Chromium';
            } elseif (preg_match('/(chrome\/)/i', $ua)) {
                $brand = 'Google';
                $model = 'Chrome';
            } elseif (preg_match('/(safari)/i', $ua)) {
                $brand = 'Apple';
                $model = 'Safari';
            } elseif (preg_match('/(mozilla|firefox|gecko)/i', $ua)) {
                $brand = 'Mozilla';
                $model = 'Firefox';
            }
        }

        return [
            'is_bot' => $isBot,
            'os_name' => $osName,
            'os_version' => $osVersion,
            'os_platform' => $osPlatform,
            'device' => $device,
            'brand' => $brand,
            'model' => $model,
        ];
    }

    /**
     * Parse the user agent string using Piwik/Matomo's DeviceDetector library
     *
     * @param string $ua User agent string
     * @return array
     */
    protected function parseWithDeviceDetector(string $ua): array
    {
        // OPTIONAL: Set version truncation to none, so full versions will be returned
        // By default only minor versions will be returned (e.g. X.Y)
        // for other options see VERSION_TRUNCATION_* constants in DeviceParserAbstract class
        //\DeviceDetector\Parser\Device\DeviceParserAbstract::setVersionTruncation(\DeviceDetector\Parser\Device\DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        $dd = new DeviceDetector($ua);

        // OPTIONAL: Set caching method
        // By default static cache is used, which works best within one php process (memory array caching)
        // To cache across requests use caching in files or memcache
        // $dd->setCache(new Doctrine\Common\Cache\PhpFileCache('./tmp/'));

        // OPTIONAL: Set custom yaml parser
        // By default Spyc will be used for parsing yaml files. You can also use another yaml parser.
        // You may need to implement the Yaml Parser facade if you want to use another parser than Spyc or [Symfony](https://github.com/symfony/yaml)
        // $dd->setYamlParser(new DeviceDetector\Yaml\Symfony());

        // OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
        $dd->discardBotInformation();

        // OPTIONAL: If called, bot detection will completely be skipped (bots will be detected as regular devices then)
        // $dd->skipBotDetection();

        $dd->parse();

        /*
        if ($dd->isBot()) {
            // handle bots,spiders,crawlers,...
            $botInfo = $dd->getBot();
        } else {
            $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
            $osInfo = $dd->getOs();
            $device = $dd->getDeviceName();
            $brand = $dd->getBrandName();
            $model = $dd->getModel();
        }
        */

        $data = [
            'is_bot' => $dd->isBot(),
            'os_name' => $dd->getOs('name'),
            'os_version' => $dd->getOs('version'),
            'os_platform' => $dd->getOs('platform'),
            'device' => $this->_mapDevice($dd->getDeviceName()),
            'brand' => $dd->getBrandName(),
            'model' => $dd->getModel(),
        ];

        return array_filter($data, function ($val) {
            return $val && $val != 'UNK' ? true : false;
        });
    }

    /**
     * @param string $val Device value
     * @return string
     */
    protected function _mapDevice(string $val): string
    {
        $map = [
            'smartphone' => 'phone',
        ];

        return $map[$val] ?? $val;
    }
}
