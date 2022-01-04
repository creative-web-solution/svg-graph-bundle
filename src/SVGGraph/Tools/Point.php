<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Tools;

class Point
{
    public $x;
    public $y;

    /**
     * Point constructor.
     *
     * @param $x
     * @param $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function toArray(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * @param Point $center
     * @param $radius
     * @param $angle
     *
     * @return Point
     */
    public static function angleToPoint(Point $center, $radius, $angle): Point
    {
        return new Point(
            round($center->x + $radius * cos(deg2rad($angle)), 3),
            round($center->y + $radius * sin(deg2rad($angle)), 3)
        );
    }
}
