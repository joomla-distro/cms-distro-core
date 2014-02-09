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
        // Vendor Directory
        $vendorDir = rtrim($composer->getConfig()->get('vendor-dir'), '/');

        $composer_installers = $config->get('cms-package-installer');

        $local_config = json_decode(file_get_contents(dirname(__DIR__) . '/etc/packages.json'), true);
        $local_installers = $local_config['config']['cms-package-installer'];

        // merge to package list
        if (!empty($composer_installers)) {
            $local_array = array();
            foreach ($local_installers as $local_installer) {
                $local_array[$local_installer['type']] = $local_installer;
            }
            $composer_array = array();
            foreach ($composer_installers as $composer_installer) {
                $composer_array[$composer_installer['type']] = $composer_installer;     
            }
        }

        $packages = array_merge($local_array, $composer_array);

        foreach ($packages as $package) {
            if (isset($package['type']) || array_key_exists('type', $package)) {
                $class_config = array();
                $class_exists = isset($package['class']) || array_key_exists('class', $package);
                $class_name   = $class_exists ? $package['class'] : __NAMESPACE__ . '\\BaseInstaller' ;

                // Setup class config with custom settings
                if ($class_name == __NAMESPACE__ . '\\BaseInstaller') {
                    if (!isset($package['location'])) {
                        continue;
                    }
                    $class_config = $package;
                }

                // require package installer class
                if ($class_exists) {
                    if (!isset($package['require'])) {
                        continue;
                    }
                    $class_path = $vendorDir . '/' . $package['require'];
                    if (file_exists($class_path)) {
                        require_once $class_path;
                    } else {
                        continue;
                    }
                }

                $installer = new $class_name($io, $composer, $class_config);
                $composer->getInstallationManager()->addInstaller($installer);
            }
        }
    }
}