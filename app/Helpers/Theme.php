<?php
namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

/**
 * Themes management
 * @package My Tonic
 * @author Saiful Alam
 * @version 1.0
 */
class Theme
{
	/**
     * The File System Instance
     *
     * @var mixed
     */
	protected $file_system;
	/**
     * The active theme name
     *
     * @var string
     */
	protected $active = "default";
	/**
     * The directory separator determined by the OS
     *
     * @var string
     */
	protected $separator = DIRECTORY_SEPARATOR;

	/**
     * Theme location, load from where
     *
     * @var string
     */
	protected $theme_path;
	/**
     * Theme asset publish location
     *
     * @var string
     */
	protected $theme_assets_path;

    /**
     * Create a new theme instance.
     *
     * @return void
     */
	function __construct()
	{
		$this->file_system = new Filesystem;
		$this->theme_path = config("view.theme_path", resource_path('themes'));
		$this->theme_assets_path = config('view.theme_assets_path', public_path('themes'));
		if( $this->applicationInstalled() ) {
			$this->active = Setting::getValue('site_theme', 'default');
		}
	}

    /**
     * Active a new theme
     *
     * @param  string  $name
     * @return boolean
     */
	public function setActiveTheme($name)
	{
		if( $this->themeExists($name) || $this->isDefaultTheme($name) ){
            if( $this->applicationInstalled() ) {
			    $settings = Setting::setValue('site_theme', $name);
            }else{
                throw new Exception("Settings table is not found!", 500);
            }
			if( $settings ){
				$this->unPublishThemeAssets($this->getActiveTheme());
				try {
					$this->publishThemeAssets($name);
					$this->active = $name;
					return true;
				}catch (\Exception $e) {
					throw $e;
				}
			}
		}
		return false;
	}
	
	/**
     * Publish the active assets to public
     *
     * @param  string  $name
     * @return boolean
     */
	public function publishThemeAssets($name='default')
	{
		if( $this->themeHasAssets($name) ){
			$assetsPath = $this->getThemeAssetsPath($name);
			$publishPath = $this->getThemePublishPath($name);
			
			if( $assetsPath && $publishPath ){
				return $this->copyDir($assetsPath, $publishPath);
			}
			return false;
		}
		return true;
	}

	/**
     * Remove the old active theme assets from public
     *
     * @param  string  $name
     * @return boolean
     */
	public function unPublishThemeAssets($name='default')
	{
		if( $this->themeHasAssets($name) ){
			$publishPath = $this->getThemePublishPath($name);
			if( $publishPath && ! $this->isDefaultTheme($name) ){
				return $this->deleteDir($publishPath);
			}
			return false;
		}
		return true;
	}

	/**
     * Get the theme lists
     *
     * @return array
     */
	public function getThemeLists()
	{
		$themes = [];
		$lists = scandir($this->theme_path);
		foreach ($lists as $item) {
			if( $this->notParentDir($item) && ! is_file($item) ){
				$themes[$item] = $this->getThemeInfo($item);
			}
		}
		return $themes;
	}

	/**
     * Get the active theme name
     *
     * @return string
     */
	public function getActiveTheme()
	{
		return $this->active ?? 'default';
	}

	/**
     * Get the Theme Info
     *
     * @param  string  $name
     * @return json_object || array
     */
	public function getThemeInfo($name = 'default')
	{
		if( $this->themeExists($name) ){
			try {
				$jsonFile = $this->getThemeJsonPath($name);
				$json = file_get_contents($jsonFile);
				$decoded = json_decode($json);
				if( (json_last_error() == JSON_ERROR_NONE) ){
					return $decoded;
				}
			} catch (\Exception $e) {  }
		}
		return json_decode("{}");
	}

	/**
     * Get the assets path 
     *
     * @param  string  $name
     * @return string || boolean
     */
	public function getThemeAssetsPath($name = 'default' )
	{
		$info = $this->getThemeInfo($name);
		if( isset($info->assets) && $info->assets ){
			$assets = "assets";
			$path = "{$name}{$this->separator}{$assets}";
			if( is_dir($this->getThemePath($path)) ){
				return $this->getThemePath($path);
			}
		}
		return null;
	}

	/**
     * Get the theme location path
     *
     * @param  string  $name
     * @return string
     */
	public function getThemePath($name = 'default')
	{
		return "{$this->theme_path}{$this->separator}{$name}";
	}

	/**
     * Get a theme info JSON file path
     *
     * @param  string  $name
     * @return string | location
     */
	public function getThemeJsonPath($name='default')
	{
		return $this->getThemePath("{$name}{$this->separator}theme.json");
	}

	/**
     * Get the theme publish path 
     *
     * @param  string  $name
     * @return string | location
     */
	public function getThemePublishPath($name, $create = false){
		$path = "{$this->theme_assets_path}{$this->separator}{$name}";
		if( !is_dir( $path ) && $create ){
			try { mkdir($path, 0777, true); } catch (\Exception $e) {  }
		}
		return $path;
	}

	/**
     * Check a theme exist in the themes location
     *
     * @param  string  $name
     * @return boolean
     */
	public function themeExists($name = 'default')
	{
		$jsonFile = $this->getThemeJsonPath($name);
		return file_exists($jsonFile);
	}

	/**
     * Check the theme has assets to publish
     *
     * @param  string  $name
     * @return boolean
     */
	public function themeHasAssets($name= 'default')
	{
		$info = $this->getThemeInfo($name);
		return ! empty( $info->assets ?? null );
	}

	/**
     * Get the evaluated view contents for the given view based on theme.
     *
     * @param  string|null  $view
     * @param  Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
	public function view($view = null, $data = [], $mergeData = [])
    {
        $blade = $this->getViewName($view);
        if ( ! View::exists($blade)) {
			$blade = $view;
		}
        return view($blade, $data, $mergeData);
	}

	/**
     * Get the actual theme blade file name
     *
     * @param  string  $view
     * @return string
     */
	public function getViewName($view)
	{
		$theme = $this->getActiveTheme() ?? 'default';
		if( str()->contains($view, '::') ){
			list($namespace, $file) = explode('::', $view);
			if( $namespace && $file ){
				$namespace = strtolower($namespace);
				return "{$theme}.{$namespace}.{$file}";
			}
		}
		return "{$theme}.{$view}";
	}

	/**
     * Generate an asset path for the theme.
     *
     * @param  string  $path
     * @param  boolean|null  $secure
     * @return string
     */
	public function assetUrl($path, $use = null)
	{
		$theme = $this->getActiveTheme() ?? 'default';
		if( $use ){ $theme = $use; }
		return asset("themes/{$theme}/{$path}");
	}

	/**
     * Get the active theme layout for the application
     *
     * @param  string  $name
     * @param  boolean|null  $custom
     * @return string
     */
	public function layout($name = 'app', $custom = false)
	{
		if( $custom ){ return $name; }
		$theme = $this->getActiveTheme() ?? 'default';
		$layout = "{$theme}.layouts.{$name}";
		if ( ! View::exists($layout)) {
			$layout = "layouts.{$name}";
		}
		return $layout;
	}

	/**
     * Access the active theme directory blade
     *
     * @param  string  $name
     * @return string
     */
	public function blade($name = null)
	{
		if( $name == null ) {return '';}
		$theme = $this->getActiveTheme() ?? 'default';
		$layout = "{$theme}.{$name}";
		if ( ! View::exists($layout)) {
			$layout = "default.{$name}";
		}
		return $layout;
	}

	/**
     * Copy a directory from one location to another.
     *
     * @param  string  $source
     * @param  string  $destination
     * @return boolean
     */
    private function copyDir($source, $destination) {
        try {
        	return $this->file_system->copyDirectory($source, $destination);
        } catch (\Exception $e) {
        	return false;
        }
    }

    /**
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param  string  $directory
     * @return boolean|
     */
    private function deleteDir($path) {
        try {
        	return $this->file_system->deleteDirectory($path);
        } catch (\Exception $e) {
        	return false;
        }
    }

    /**
     * Skip the dots of a path
     *
     * @param  string  $path
     * @return boolean
     */
    private function notParentDir($path)
    {
    	return $path != "." && $path != "..";
    }

    /**
     * Check if the theme is default theme
     *
     * @param  string  $path
     * @return boolean
     */
    private function isDefaultTheme($theme)
    {
    	return $theme == 'default';
    }

    /**
     * Check if the application is migrated and installed or not
     *
     * @return boolean
     */
    private function applicationInstalled()
    {
        return Schema::hasTable('settings');
    }
}
