<?php

namespace fzed51\Core;

/**
 * Description of MenuRender
 *
 * @author fabien.sanchez
 */
abstract class MenuRender
{

    abstract function renderMenu(Menu $menu);

    abstract function renderItem(MenuItem $item);

    abstract function renderLink(MenuLink $link);
}
