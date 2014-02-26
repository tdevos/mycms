<?php

namespace Cms\MainBundle\Controller;

use Cms\MainBundle\Block\ImageBlock;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\Util\PathHelper;
use PHPCRProxies\__CG__\Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ImagineBlock;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
use Symfony\Cmf\Bundle\MediaBundle\CmfMediaBundle;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Directory;
use Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class GalleryController extends Controller
{

    public function findAction($blockName){
        $parameters = $this->container->getParameter('fm_elfinder');
        $editor = $parameters['editor'];
        $locale = $parameters['locale'] ?: $this->getRequest()->getLocale();
        $fullscreen = $parameters['fullscreen'];
        $includeAssets = $parameters['include_assets'];
        $compression = $parameters['compression'];
        $prefix = ($compression ? '/compressed' : '');
        return $this->render('CmsMainBundle:Gallery:find.html.twig', array(
            'locale' => $locale,
            'fullscreen' => $fullscreen,
            'includeAssets' => $includeAssets,
            "blockName" => $blockName
        ));
    }

    public function linkAction(){
        $request = $this->getRequest();
        $block = $request->request->get("block");
        $image = $request->request->get("image");

        $blockName = str_replace("_", "/", $block);

        $dm = $this->get("doctrine_phpcr.odm.document_manager");

        try{
            $parentPath = PathHelper::getParentPath("/cms/media/" . $image);
            $block = $dm->find(null, $blockName);

            $imBlock = new ImagineBlock();
            $imBlock->setParentDocument($block);
            $imBlock->setName($image);
            $imBlock->setLinkUrl($parentPath . "/" . $image);

            $dm->persist($imBlock);

            $dm->flush();
        }catch (\Exception $e){
            return new Response(get_class($e));
        }
        return new Response(1);
    }

    public function unlinkAction(){
        $request = $this->getRequest();
        $blockName = $request->request->get("block");
        $imageName = $request->request->get("imageName");
        $blockName = str_replace("_", "/", $blockName);

        $dm = $this->get("doctrine_phpcr.odm.document_manager");

        try{
            $block = $dm->find(null, $blockName . "/" . $imageName);
            $dm->remove($block);
            $dm->flush();
        }catch (\Exception $e){
            return new Response($e->getMessage());
        }
        return new Response(1);
    }

    public function indexAction(ActionBlock $block){
        $dm = $this->get("doctrine_phpcr.odm.document_manager");
        return $this->render("CmsMainBundle:Gallery:index.html.twig", array(
            "imageBlocks" => $dm->getChildren($block),
            "blockName" => str_replace("/", "_", $block->getId())
        ));
    }

}