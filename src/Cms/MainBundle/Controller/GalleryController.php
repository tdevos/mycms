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

class GalleryController extends Controller
{

    protected $images = array();

    public function getImages()
    {
        return $this->images;
    }

    public function indexAction(ActionBlock $block){
        $dm = $this->get("doctrine_phpcr.odm.document_manager");

/*        $imBlock = new ImagineBlock();
        $imBlock->setParentDocument($block);
        $imBlock->setName("image");

        $dm->persist($imBlock);

        $image = new Image();
        $image->setFileContentFromFilesystem('/home/thomas/Desktop/1382187_10151789410622972_657139286_n.jpg');
        $imBlock->setImage($image);
        $dm->flush();
*/
        $blockChildren = $dm->getChildren($block);
        foreach($blockChildren as $child){
//            var_dump($child);
        }
        $this->images = array(
            array("url" => "http://lorempixel.com/100/100"),
        );

        return $this->render("CmsMainBundle:Gallery:index.html.twig", array(
            "images" => $this->getImages(),
            "blockChildren" => $blockChildren
        ));

    }
}