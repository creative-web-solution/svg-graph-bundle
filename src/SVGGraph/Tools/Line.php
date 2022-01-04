<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Tools;

/**
 * Class Line
 * @package Cws\Bundle\SVGGraphBundle\Tools
 */
class Line
{
    /**
     * @param Point $point
     *
     * @return string
     */
    public static function lineFrom(Point $point)
    {
        return sprintf('M %s,%s', $point->x, $point->y);
    }

    /**
     * @param Point $point
     *
     * @return string
     */
    public static function lineTo(Point $point)
    {
        return sprintf('L %s,%s', $point->x, $point->y);
    }

    /**
     * @param Point $point
     * @param Point $point2
     *
     * @return string
     */
    public static function lineFromTo(Point $point, Point $point2)
    {
        return sprintf('M %s,%s L %s,%s', $point->x, $point->y, $point2->x, $point2->y);
    }
}
