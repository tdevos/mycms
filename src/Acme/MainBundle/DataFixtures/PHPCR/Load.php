<?php

namespace Acme\MainBundle\DataFixtures\PHPCR;

use Doctrine\Common\Persistence\ObjectManager;

use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\Yaml\Parser;

use Symfony\Cmf\Bundle\SimpleCmsBundle\DataFixtures\Phpcr\AbstractLoadPageData;
use Symfony\Cmf\Bundle\SimpleCmsBundle\Doctrine\Phpcr\MultilangRedirectRoute;
use Symfony\Cmf\Bundle\SimpleCmsBundle\Doctrine\Phpcr\MultilangRoute;

use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Phpcr\MenuNode;

class Load extends AbstractLoadPageData
{
    private $yaml;

    public function getOrder()
    {
        return 0;
    }

    protected function getData()
    {
        return $this->yaml->parse(file_get_contents(__DIR__.'/../../Resources/data/page.yml'));
    }

    protected function createPageInstance($className)
    {
        return new $className(true, false, true);
    }

    public function load(ObjectManager $dm)
    {
        $this->yaml = new Parser();

        $session = $dm->getPhpcrSession();

        $basepath = $this->container->getParameter('cmf_media.persistence.phpcr.media_basepath');
        NodeHelper::createPath($session, $basepath);

        $basepath = $this->container->getParameter('cmf_content.persistence.phpcr.content_basepath');
        NodeHelper::createPath($session, $basepath);

        $basepath = $this->getBasePath();
        NodeHelper::createPath($session, preg_replace('#/[^/]*$#', '', $basepath));

        $data = $this->getData();

        $defaultClass = $this->getDefaultClass();

        foreach ($data['static'] as $overview) {
            $class = isset($overview['class']) ? $overview['class'] : $defaultClass;

            $parent = (isset($overview['parent']) ? trim($overview['parent'], '/') : '');
            $name = (isset($overview['name']) ? trim($overview['name'], '/') : '');

            $path = $basepath
                .(empty($parent) ? '' : '/' . $parent)
                .(empty($name) ? '' : '/' . $name);

            $page = $dm->find($class, $path);
            if (!$page) {
                $page = $this->createPageInstance($class);
                $page->setId($path);
            }

            if (isset($overview['formats'])) {
                $page->setDefault('_format', reset($overview['formats']));
                $page->setRequirement('_format', implode('|', $overview['formats']));
            }

            if (!empty($overview['template'])) {
                $page->setDefault(RouteObjectInterface::TEMPLATE_NAME, $overview['template']);
            }

            if (!empty($overview['controller'])) {
                $page->setDefault(RouteObjectInterface::CONTROLLER_NAME, $overview['controller']);
            }

            if (!empty($overview['options'])) {
                $page->setOptions($overview['options']);
            }

            $dm->persist($page);

            if (is_array($overview['title'])) {
                foreach ($overview['title'] as $locale => $title) {
                    $page->setTitle($title);
                    if (isset($overview['label'][$locale]) && $overview['label'][$locale]) {
                        $page->setLabel($overview['label'][$locale]);
                    } elseif (!isset($overview['label'][$locale])) {
                        $page->setLabel($title);
                    }
                    $page->setBody($overview['body'][$locale]);
                    $dm->bindTranslation($page, $locale);
                }
            } else {
                $page->setTitle($overview['title']);
                if (isset($overview['label'])) {
                    if ($overview['label']) {
                        $page->setLabel($overview['label']);
                    }
                } elseif (!isset($overview['label'])) {
                    $page->setLabel($overview['title']);
                }
                $page->setBody($overview['body']);
            }

            if (isset($overview['create_date'])) {
                $page->setCreateDate(date_create_from_format('U', strtotime($overview['create_date'])));
            }

            if (isset($overview['publish_start_date'])) {
                $page->setPublishStartDate(date_create_from_format('U', strtotime($overview['publish_start_date'])));
            }

            if (isset($overview['publish_end_date'])) {
                $page->setPublishEndDate(date_create_from_format('U', strtotime($overview['publish_end_date'])));
            }

            if (isset($overview['blocks'])) {
                foreach ($overview['blocks'] as $name => $block) {
                    $this->loadBlock($dm, $page, $name, $block);
                }
            }
        }

        $dm->flush();


        // ---------------------------------------------------------------------------------------


        $data = $this->yaml->parse(file_get_contents(__DIR__ . '/../../Resources/data/external.yml'));

        $basepath = $this->getBasePath();
        $home = $dm->find(null, $basepath);

        $route = new MultilangRoute();
        $route->setPosition($home, 'dynamic');
        $route->setDefault('_controller', 'AcmeMainBundle:Demo:dynamic');

        $dm->persist($route);

        foreach ($data['static'] as $name => $overview) {
            $menuItem = new MenuNode();
            $menuItem->setName($name);
            $menuItem->setParent($home);
            if (!empty($overview['route'])) {
                if (!empty($overview['uri'])) {
                    $route = new MultilangRedirectRoute();
                    $route->setPosition($home, $overview['route']);
                    $route->setUri($overview['uri']);
                    $dm->persist($route);
                } else {
                    $route = $dm->find(null, $basepath.'/'.$overview['route']);
                }
                $menuItem->setRoute($route->getId());
            } else {
                $menuItem->setUri($overview['uri']);
            }

            $dm->persist($menuItem);
            foreach ($overview['label'] as $locale => $label) {
                $menuItem->setLabel($label);
                if ($locale) {
                    $dm->bindTranslation($menuItem, $locale);
                }
            }
        }

        $dm->flush();
    }

    /**
     * Load a block from the fixtures and create / update the node. Recurse if there are children.
     *
     * @param ObjectManager $manager the document manager
     * @param string $parentPath the parent of the block
     * @param string $name the name of the block
     * @param array $block the block definition
     */
    private function loadBlock(ObjectManager $manager, $parent, $name, $block)
    {
        $className = $block['class'];
        $document = $manager->find(null, $this->getIdentifier($manager, $parent) . '/' . $name);
        $class = $manager->getClassMetadata($className);
        if ($document && get_class($document) != $className) {
            $manager->remove($document);
            $document = null;
        }
        if (!$document) {
            $document = $class->newInstance();

            // $document needs to be an instance of BaseBlock ...
            $document->setParentDocument($parent);
            $document->setName($name);
            $manager->persist($document);
        }

        if ($className == 'Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock') {
            $referencedBlock = $manager->find(null, $block['referencedBlock']);
            if (null == $referencedBlock) {
                throw new \Exception('did not find '.$block['referencedBlock']);
            }
            $document->setReferencedBlock($referencedBlock);
        } elseif ($className == 'Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock') {
            $document->setActionName($block['actionName']);
        }

        // set properties
        if (isset($block['properties'])) {
            foreach ($block['properties'] as $propName => $prop) {
                $class->reflFields[$propName]->setValue($document, $prop);
            }
        }
        // create children
        if (isset($block['children'])) {
            foreach ($block['children'] as $childName => $child) {
                $this->loadBlock($manager, $document, $childName, $child);
            }
        }
    }

    private function getIdentifier($manager, $document)
    {
        $class = $manager->getClassMetadata(get_class($document));
        return $class->getIdentifierValue($document);
    }
}

/**
if (isset($overview['blocks'])) {
    foreach ($overview['blocks'] as $name => $block) {
        $this->loadBlock($manager, $page, $name, $block);
    }
}
 */