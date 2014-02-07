# The CMS Distro Installer Package

## Extension Types

### `cms-assets`

Media resources used for build web application/sites.

### `cms-template`

Template for cms.

## Installation via Composer

Add `"joomla-distro/cms-distro-core": "*"` to the require block in your composer.json, make sure you have `"repository": "https://github.com/joomla-distro/packagist/raw/master/web/"` and then run `composer install`.

```json
{
	"require": {
		"joomla-distro/cms-distro-core": "*"
	},
	"repositories": [
		{
			"type": "composer",
			"url": "https://github.com/joomla-distro/packagist/raw/master/web/" 
		}
	]
}
```