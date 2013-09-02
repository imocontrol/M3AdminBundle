# iMOControl M3AdminBundle Installation

The installation is quite simple. Execute the following line in your terminal:

	$ php composer.phar require imocontrol/m3-admin-bundle:dev-master
	
Register the bundle in the AppKernel:  
_app/AppKernel.php_
	
	new IMOControl\M3\AdminBundle\IMOControlM3AdminBundle(),
	
Load routing config:  
_config/routing.yml_

		admin:
    	  resource: '@IMOControlM3AdminBundle/Resources/config/routing/imoc_admin.yml'
    	  prefix: /imoc  

Notice the /imoc prefix is main entry point for the iMOControl Adminpanel (your-domain.com/imoc/dashboard)    	

	