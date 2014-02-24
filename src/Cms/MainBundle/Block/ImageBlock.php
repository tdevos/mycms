<?php

namespace Cms\MainBundle\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

class ImageBlock extends AbstractBlock implements TranslatableInterface{

    public function getType(){
        return "cmf.block.simple";
    }

    protected $imageUrl = null;

    /**
     * @param null $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return null
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

}