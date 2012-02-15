<?php   defined('C5_EXECUTE') or die("Access Denied.");
# Hide & Seek
# License: v. LICENSE.TXT
# copyright (c) 2012 Christian Grauer

class HideAndSeekPackage extends Package {

    protected $pkgHandle = 'hideandseek';
    protected $appVersionRequired = '5.4.2.1';
    protected $pkgVersion = '0.9.4';

    public function getPackageDescription() {
        return t("Hide and Seek");
    }

    public function getPackageName() {
        return t("Hide & Seek");
    }
	
    public function install() {

        $pkg = parent::install();
		Loader::library('installer', 'hideandseek');
        $pkgInstaller = new PackageInstaller($pkg);

		// install blocks		
		$blockHandles = array ( 'hideandseek', 'hideandseek_stop' );
		$pkgInstaller->install_blocks( $blockHandles );
    }


    public function on_start() {

		# make event        
        Events::extend('on_before_render', 'HideAndSeek', 'hidePage', 'packages/' . $this->getPackageHandle() . '/libraries/hideandseek.php' );

    }

	public function uninstall( ) {
		
        $pkg = parent::uninstall();
	}
	

}