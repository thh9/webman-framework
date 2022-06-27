<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Support\View;

use think\Template;
use Webman\View;

/**
 * Class Blade
 * @package Support\View
 */
class ThinkPHP implements View
{
    /**
     * @var array
     */
    protected static $_vars = [];

    /**
     * @param $name
     * @param null $value
     */
    public static function assign($name, $value = null)
    {
        static::$_vars = \array_merge(static::$_vars, \is_array($name) ? $name : [$name => $value]);
    }

    /**
     * @param $template
     * @param $vars
     * @param string $app
     * @return mixed
     */
    public static function render($template, $vars, $app = null)
    {
        $request = request();
        $app = $app === null ? $request->app : $app;
        $config_prefix = $request->plugin ? "plugin.{$request->plugin}." : '';
        $view_suffix = \config("{$config_prefix}view.options.view_suffix", 'html');
        $base_view_path = $request->plugin ? \base_path() . "/plugin/{$request->plugin}/app" : \app_path();
        $view_path = $app === '' ? "$base_view_path/view/" : "$base_view_path/$app/view/";
        $default_options = [
            'view_path' => $view_path,
            'cache_path' => \runtime_path() . '/views/',
            'view_suffix' => $view_suffix
        ];
        $options = $default_options + \config("{$config_prefix}view.options", []);
        $views = new Template($options);
        \ob_start();
        $vars = \array_merge(static::$_vars, $vars);
        $views->fetch($template, $vars);
        $content = \ob_get_clean();
        static::$_vars = [];
        return $content;
    }
}
