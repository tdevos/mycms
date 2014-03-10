<?php

namespace Cms\MainBundle;

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CmsMainBundle extends Bundle{

    public function build(ContainerBuilder $container){
        $compilerPass = DoctrinePhpcrMappingsPass::createXmlMappingDriver(array(
            realpath(__DIR__ . '/Resources/config/') => 'Cms\MainBundle\Block',
        ));

        $container->addCompilerPass($compilerPass);

        $compilerPass = DoctrinePhpcrMappingsPass::createXmlMappingDriver(array(
            realpath(__DIR__ . '/Resources/config/') => 'Cms\MainBundle\Page',
        ));

        $container->addCompilerPass($compilerPass);

    }

}