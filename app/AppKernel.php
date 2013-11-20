<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Tixi\HomeBundle\TixiHomeBundle(),
            new Tixi\Pub\AboutBundle\TixiPubAboutBundle(),
            new Tixi\Pub\HelpBundle\TixiPubHelpBundle(),
            new Tixi\Pub\SupportBundle\TixiPubSupportBundle(),
            new Tixi\Pub\LogoutBundle\TixiPubLogoutBundle(),
            new Tixi\Pub\CustomerBundle\TixiPubCustomerBundle(),
            new Tixi\SecurityBundle\TixiSecurityBundle(),
            new Tixi\App\PreferencesBundle\TixiAppPreferencesBundle(),
            new Tixi\App\TeamBundle\TixiAppTeamBundle(),
            new Tixi\App\VehicleDataBundle\TixiAppVehicleDataBundle(),
            new Tixi\App\VehicleServiceplanBundle\TixiAppVehicleServiceplanBundle(),
            new Tixi\App\MysqlDumpBundle\TixiAppMysqlDumpBundle(),
            new Tixi\App\OviDatenBundle\TixiAppOviDatenBundle(),
            new Tixi\App\OviDetailsBundle\TixiAppOviDetailsBundle(),
            new Tixi\App\DriverDataBundle\TixiAppDriverDataBundle(),
            new Tixi\App\DriverDetailsBundle\TixiAppDriverDetailsBundle(),
            new Tixi\App\DriverVacationBundle\TixiAppDriverVacationBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
