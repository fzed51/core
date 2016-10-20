<?php

namespace fzed51\Core;

/**
 * Description of MenuItem
 *
 * @author fabien.sanchez
 */
class MenuItem extends MenuElement
{

    private $element;
    private $libelle;
    private $options;

    public function __construct($libelle, MenuElement $element)
    {
        $this->libelle = $libelle;
        $this->element = $element;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function render(MenuRender $renderer)
    {
        return $renderer->renderItem($this);
    }
}
