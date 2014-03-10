<?php

namespace Cms\MainBundle\Page;
use Symfony\Cmf\Bundle\SimpleCmsBundle\Doctrine\Phpcr\Page;

/**
 * {@inheritDoc}
 */
class Promotions extends Page
{
    public $node;

    public $description;
    public $plans;
    public $architect;
    public $details;
    public $price;

    /**
     * @param mixed $architect
     */
    public function setArchitect($architect)
    {
        $this->architect = $architect;
    }

    /**
     * @return mixed
     */
    public function getArchitect()
    {
        return $this->architect;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $plans
     */
    public function setPlans($plans)
    {
        $this->plans = $plans;
    }

    /**
     * @return mixed
     */
    public function getPlans()
    {
        return $this->plans;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
}
