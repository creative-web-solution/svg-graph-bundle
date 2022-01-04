<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Tools;

class Line
{
    public static function lineFrom(Point $point): string
    {
        return sprintf('M %s,%s', $point->x, $point->y);
    }

    public static function lineTo(Point $point): string
    {
        return sprintf('L %s,%s', $point->x, $point->y);
    }

    public static function lineFromTo(Point $point, Point $point2): string
    {
        return sprintf('M %s,%s L %s,%s', $point->x, $point->y, $point2->x, $point2->y);
    }
}
