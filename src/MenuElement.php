<?php

namespace fzed51\Core;

/**
 * Description of MenuElement
 *
 * @author fabien.sanchez
 */
abstract class MenuElement {

    abstract function render(MenuRender $renderer);
}
