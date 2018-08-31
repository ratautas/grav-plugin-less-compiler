<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class LessCompilerPlugin
 * @package Grav\Plugin
 */
class LessCompilerPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) return;

        // Enable the main event we are interested in
        $this->enable(['onAssetsInitialized'   => ['onAssetsInitialized', 0]]);
    }


    public function onAssetsInitialized()
    {
        if (!$this->isAdmin()) {
            require_once 'vendor/lessphp/Less.php';
            $theme_name = $this->grav['theme']['name'];
            $theme_path = './user/themes/'.$theme_name.'/';
            $css_dir = $this->grav['config']->get('plugins.scss-compiler.css_dir');
            $css_file = $this->grav['config']->get('plugins.less-compiler.css_file');
            $less_dir = $this->grav['config']->get('plugins.less-compiler.less_dir');
            $less_file = $this->grav['config']->get('plugins.less-compiler.less_file');
            $css_dir_path = $theme_path.$css_dir;
            $css_file_path = $css_dir_path.$css_file;
            $less_dir_path = $theme_path.$less_dir;
            $less_file_path = $less_dir_path.$less_file;
            if (!file_exists($css_dir_path)) mkdir($css_dir_path, 0755);
            dump($less_file_path);
            if (file_exists($less_file_path)) {
                $css_compiled = \Less_Cache::Get(
                    array( $less_file_path => null),
                    array( 'cache_dir' => $css_dir_path.'cache', 'sourceMap' => true )
                );
                rename($css_dir_path.'cache/'.$css_compiled, $css_file_path);
                $this->grav['assets']->addCss($css_file_path);
            }
        }
    }
}