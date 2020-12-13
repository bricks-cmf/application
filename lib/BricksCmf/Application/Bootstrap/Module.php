<?php

/** @copyright Sven Ullmann <kontakt@sumedia-webdesign.de> **/namespace BricksCmf\Application\Bootstrap;

use BricksCmf\ConfigService\ConfigService;
use BricksCmf\DiService\DiService;
use BricksFramework\Bootstrap\BootstrapInterface;
use BricksFramework\Bootstrap\Module\AbstractModule;

class Module extends AbstractModule
{
    public function bootstrap(BootstrapInterface $bootstrap): void
    {
        /** @var DiService $di */
        $diService = $bootstrap->getService(DiService::SERVICE_NAME);
        if (!$diService) {

            $configService = $bootstrap->getService(ConfigService::SERVICE_NAME);
            if (!$configService) {
                $configServiceFactory = $bootstrap->getInstance('BricksCmf\\ConfigService\\Factory\\ConfigServiceFactory');
                $configService = $configServiceFactory->get($bootstrap->getContainer(), 'BricksCmf\\ConfigService\\ConfigService');
            }
            $config = $configService->getConfig();

            $diServiceFactory = $bootstrap->getInstance('BricksCmf\\DiService\\Factory\\DiServiceFactory');
            $diService = $diServiceFactory->get($bootstrap->getContainer(), 'BricksCmf\\DiService\\DiService', [
                'config' => $config
            ]);
        }

        $application = $diService->get('BricksFramework\\Application\\Application', [
            $bootstrap
        ]);
        $bootstrap->getContainer()->set('bricks/application', $application);
    }

    public function postBootstrap(BootstrapInterface $bootstrap): void
    {
        $application = $bootstrap->getContainer()->get('bricks/application');
        $bootstrap->getContainer()->set('run', [$application, 'run']);
    }
}
