<?php
namespace JDistro\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Composer installer plugin
 *
 * @author  Julio Pontes <https://github.com/juliopontes>
 * @package JDistro\Composer
 */
class Installer implements PluginInterface
{
    /**
     * Apply plugin modifications to composer
     *
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        // Package Type List
        $packages = array();
        // Read Composer Config
        $config = $composer->getConfig();

        $composer_installers = $config->get('cms-package-installer');

        $local_config = json_decode(file_get_contents(dirname(__DIR__) . '/src/packages.json'), true);
        $local_installers = $local_config['config']['cms-package-installer'];

        // merge to package list
        $packages = array_merge($composer_installers, $local_installers);

        foreach ($packages as $package) {
            if ((isset($package['type']) || array_key_exists('type', $package)) && ((isset($package['location']) || array_key_exists('location', $package))) {
                $class_config = array();
                $class_exists = isset($package['class']) || array_key_exists('class', $package);
                $class_name   = $class_exists ? __NAMESPACE__ . '\\Package\\' . $package['class'] : 'BaseInstaller' ;

                // Setup class config with custom settings
                if ($class_name == 'BaseInstaller') {
                    $class_config = $package;
                }

                $installer = new $class_name($io, $composer, $class_config);
                $composer->getInstallationManager()->addInstaller($installer);
            }
        }
    }
}