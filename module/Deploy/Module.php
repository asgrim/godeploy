<?php

namespace Deploy;

use Zend\EventManager\EventInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class Module
{
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();

        $eventManager = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $routeWhitelist = [
            'zfcuser/login',
            'add-user',
        ];
        $serviceManager = $application->getServiceManager();
        $authenticationService = $serviceManager->get('zfcuser_auth_service');

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function ($e) use ($routeWhitelist, $authenticationService) {
            $match = $e->getRouteMatch();

            // No route match, this is a 404
            if (!$match instanceof RouteMatch) {
                return;
            }

            // Route is whitelisted
            $name = $match->getMatchedRouteName();
            if (in_array($name, $routeWhitelist)) {
                return;
            }

            // User is authenticated
            if ($authenticationService->hasIdentity()) {
                return;
            }

            // Redirect to the user login page, as an example
            $router   = $e->getRouter();
            $url      = $router->assemble(array(), array(
                'name' => 'zfcuser/login'
            ));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }, -100);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }
}
