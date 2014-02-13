<?php

namespace Acme\MainBundle;

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeMainBundle extends Bundle
{
    public function build(ContainerBuilder $container){
        $compilerPass = DoctrinePhpcrMappingsPass::createXmlMappingDriver(array(
            realpath(__DIR__ . '/Resources/config/') => 'Acme\MainBundle\Block',
        ));

        $container->addCompilerPass($compilerPass);

    }
}

// ./vendor/symfony-cmf/block-bundle/Symfony/Cmf/Bundle/BlockBundle/Resources/config/doctrine-phpcr/SimpleBlock.phpcr.xml