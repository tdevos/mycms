<?php

namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller{

    private $_menu = array();

    protected function getMenu()
    {
        return $this->_menu;
    }

    protected function addToMenu($label, $route){
        $this->_menu[] = array(
            "label" => $label,
            "route" => $route
        );
    }

    public function showAction(){
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(!is_object($user))
            $this->addToMenu("login", "fos_user_security_login");
        else
            $this->addToMenu("logout", "fos_user_security_logout");
        return $this->render("CmsUserBundle:Menu:show.html.twig", array(
            "menu" => $this->getMenu()
        ));
    }

}