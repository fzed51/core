<?php

namespace fzed51\Core;

/**
 * Description of MenuItem
 *
 * @author fabien.sanchez
 */
class MenuLink extends MenuElement
{

    private $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function render(MenuRender $renderer)
    {
        return $renderer->renderLink($this);
    }
}
