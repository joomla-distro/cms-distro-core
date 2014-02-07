<?php
namespace Cms\Composer\Adapter;

use Cms\Composer\BaseInstaller;
use Composer\Package\PackageInterface;

/**
 * Cms Template Installer class
 *
 * @author  Julio Pontes <https://github.com/juliopontes>
 * @package Cms\Composer\Adapter
 */
class CmsTemplate extends BaseInstaller
{
    protected $location = 'templates/{template}';
    protected $support = 'cms-template';

    /**
     * Return string path
     * @return String
     */
    protected function getLocation(PackageInterface $package)
    {
        $parts = explode('/', $package->getName());
        $this->vars['template'] = trim($parts[1]);

    	return parent::getLocation($package);
    }
}