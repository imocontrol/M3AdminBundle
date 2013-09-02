<?php
namespace IMOControl\M3\AdminBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RequestListener
 */
class RequestListener
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $userlocale = null;
        $request = $event->getRequest();
        $token = $this->container->get('security.context')->getToken();
		
		if (!is_object($token)) {
			return;
		}
		$user = $token->getUser();
		
		if (!is_object($user)) {
			return;
		}
		
		$userlocale = $user->getLocale();
		if($userlocale !== NULL AND $userlocale !== '')
        {
            $request->setLocale($userlocale);
        }
    }
}