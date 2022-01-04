<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Tools;

class Arc
{
    /**
     * @param Point $startPoint
     * @param Point $endPoint
     * @param $radius
     * @param int $axeRotation
     * @param int $largeArcFlag
     * @param int $sweepFlag
     *
     * @return string
     */
    public static function arcFromTo(
        Point $startPoint,
        Point $endPoint,
        $radius,
        $axeRotation = 0,
        $largeArcFlag = 0,
        $sweepFlag = 0
    ): string {
        return sprintf(
            'M %s,%s A %s %s %s %s %s %s,%s',
            $startPoint->x,
            $startPoint->y,
            $radius,
            $radius,
            $axeRotation,
            $largeArcFlag,
            $sweepFlag,
            $endPoint->x,
            $endPoint->y
        );
    }

    /**
     * @param Point $endPoint
     * @param $radius
     * @param int $axeRotation
     * @param int $largeArcFlag
     * @param int $sweepFlag
     *
     * @return string
     */
    public static function arcTo(
        Point $endPoint,
        $radius,
        $axeRotation = 0,
        $largeArcFlag = 0,
        $sweepFlag = 0
    ): string {
        return sprintf(
            'A %s %s %s %s %s %s,%s',
            $radius,
            $radius,
            $axeRotation,
            $largeArcFlag,
            $sweepFlag,
            $endPoint->x,
            $endPoint->y
        );
    }
}
