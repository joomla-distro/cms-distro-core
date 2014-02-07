<?php
namespace Cms\Composer\Adapter;

use Cms\Composer\BaseInstaller;
use Composer\Package\PackageInterface;

/**
 * Cms Assets Installer class
 *
 * @author  Julio Pontes <https://github.com/juliopontes>
 * @package Cms\Composer\Adapter
 */
class CmsAssets extends BaseInstaller
{
    protected $location = 'assets/{vendor}/{package}';
    protected $support = 'cms-assets';
}