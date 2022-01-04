<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Tools;

class Text
{
    public $text;
    public $color;
    public $coords;
    public $cssClass;
    public $isRightPositioned;

    /**
     * Text constructor.
     *
     * @param $text
     * @param Point $coords
     * @param $cssClass
     * @param string $color
     * @param bool $isRightPositioned
     */
    public function __construct($text, Point $coords, $cssClass, $color = '', $isRightPositioned = false)
    {
        $this->text = $text;
        $this->coords = $coords;
        $this->cssClass = $cssClass;
        $this->color = $color;
        $this->isRightPositioned = $isRightPositioned;
    }

    /**
     * @return string
     */
    public function create(): string
    {
        $result = [];

        $result[] = '<div ';
        $result[] = "class=\"$this->cssClass\" ";
        $result[] = 'style="';
        $result[] = 'top:' . $this->coords->y . 'px;';

        if ($this->isRightPositioned) {
            $result[] = 'right:' . $this->coords->x . 'px;';
        } else {
            $result[] = 'left:' . $this->coords->x . 'px;';
        }

        if ('' !== $this->color) {
            $result[] = 'color:' . $this->color . ';';
        }

        $result[] = '"';
        $result[] = '><span>';
        $result[] = $this->text;
        $result[] = '</span></div>';

        return implode('', $result);
    }
}
