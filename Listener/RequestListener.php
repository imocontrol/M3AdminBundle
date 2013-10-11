<?php
namespace IMOControl\M3\AdminBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * RequestListener
 */
class RequestListener
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $userlocale = null;
        $request = $event->getRequest();
        $token = $this->securityContext->getToken();

        if (!is_object($token)) {
            return;
        }
        $user = $token->getUser();

        if (!is_object($user)) {
            return;
        }

        $userlocale = $user->getLocale();
        if ($userlocale !== NULL AND $userlocale !== '') {
            $request->setLocale($userlocale);
        }
    }

}
