<?php namespace admin\layout;

use webcitron\Subframe\Layout;

class Sidebar extends Layout
{
    public function __construct () {
        $this->addBoxes('left', new \admin\box\admins\SidebarWelcome());
        $this->addBoxes('left', new \admin\box\SidebarMenu());
        $this->addBoxes('right', new \admin\box\TopMenu());
        
    }
}
