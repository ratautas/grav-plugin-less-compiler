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
            $css_dir = $this->grav['config']->get('plugins.less-compiler.css_dir');
            $less_entry = $this->grav['config']->get('plugins.less-compiler.less_entry');
            $theme_name = $this->grav['theme']['name'];
            $theme_path = './user/themes/'.$theme_name.'/';
            $css_fullpath = $theme_path.$css_dir;
            if (!file_exists($css_fullpath)) mkdir($css_fullpath, 0755);
            if (file_exists($theme_path.$less_entry )) {
                $css_compiled = \Less_Cache::Get(
                    array( $theme_path.$less_entry => null),
                    array( 'cache_dir' => $theme_path.$css_dir.'cache', 'sourceMap' => true )
                );
                rename($theme_path.$css_dir.'cache/'.$css_compiled, $theme_path.$css_dir.'less-compiled.css');
                $this->grav['assets']->addCss($theme_path.$css_dir.'less-compiled.css');
            }
        }
    }
}