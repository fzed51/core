<?php

namespace fzed51\Core;

use Closure;

/**
 * Description of Menu
 *
 * @author fabien.sanchez
 */
class Menu extends MenuElement
{

    protected $items = [];

    public function __construct(callable $constructor = null)
    {
        if (!is_null($constructor)) {
            $this->executeConstructor($constructor);
        }
    }

    protected function executeConstructor(callable $constructor)
    {
        return call_user_func(Closure::bind($constructor, $this, static::class));
    }

    public function addItem($libelle, MenuElement $element, $options = [])
    {
        $menuitem = new MenuItem($libelle, $element);
        $menuitem->setOptions($options);
        $this->items[] = $menuitem;
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function render(MenuRender $renderer)
    {
        return $renderer->renderMenu($this);
    }
}
