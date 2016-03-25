<?php namespace imagehost2\layout;

use webcitron\Subframe\Layout;

class Standard extends Layout
{
    public function __construct () {
        $this->addBoxes('topbar', new \imagehost2\box\Topbar());
    }
}
