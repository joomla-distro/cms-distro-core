<?php
namespace JDistro\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Codebase installer class
 *
 * @author  Julio Pontes <https://github.com/juliopontes>
 * @package JDistro\Composer
 */
class BaseInstaller extends LibraryInstaller
{
    /**
     * Array vars to template location
     */
    protected $vars         = array();

    /**
     * String with location path
     */
    protected $location     = null;

    /**
     * String with type
     */
    protected $support      = '';

    /**
     * Composer Config
     */
    protected $_config      = null;

    /**
     * {@inheritDoc}
     */
    public function __construct(IOInterface $io, Composer $composer, $config = array(), $type = 'library')
    {
        parent::__construct($io, $composer, $type);

        $this->_io = $io;
        $this->_composer = $composer;
        $this->_type = $type;
        $this->_config = $composer->getConfig();

        if (!empty($config['type']) && empty($this->support)) {
            $this->support = $config['type'];
        }

        if (!empty($config['location']) && is_null($this->location)) {
            $this->location = $config['location'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->getLocation($package);
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        if (method_exists($this, 'onBeforeInstall')) {
            $this->onBeforeInstall($repo, $package);
        }

        parent::install($repo, $package);

        if (method_exists($this, 'onAfterInstall')) {
            $this->onAfterInstall($repo, $package);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        if (method_exists($this, 'onBeforeUpdate')) {
            $this->onBeforeUpdate($package->getPrettyVersion($repo, $initial, $target));
        }

        parent::update($repo, $initial, $target);

        if (method_exists($this, 'onAfterUpdate')) {
            $this->onAfterUpdate($package->getPrettyVersion($repo, $initial, $target));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        if ($packageType != $this->support) {
            return false;
        }

        return true;
    }

    /**
     * Return string path
     * @return String
     */
    protected function getLocation(PackageInterface $package)
    {
        $parts = explode('/', $package->getName());
        $this->vars['vendor'] = $parts[0];
        $this->vars['package'] = $parts[1];

        $keys = array_keys($this->vars);
        //add regex {key}
        foreach ($keys as $i => $key) {
            $keys[$i] = sprintf('/{%s}/',$key);
        }

        $location = preg_replace($keys,array_values($this->vars),$this->location);

        $define = $this->_config->get('define');

        if(is_null($define) || !is_array($define)) {
            $define = array();
        }

        // check if exists on define the custom support-type
        if (array_key_exists($this->support, $define)) {
            $location = $define[$support] . '/' . $location;
        }

        return $location;
    }
}