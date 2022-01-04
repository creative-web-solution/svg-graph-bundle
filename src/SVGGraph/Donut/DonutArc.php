<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Donut;

use Cws\Bundle\SVGGraphBundle\Tools\Arc;
use Cws\Bundle\SVGGraphBundle\Tools\Line;
use Cws\Bundle\SVGGraphBundle\Tools\Point;
use SVG\Nodes\Shapes\SVGPath;
use SVG\Nodes\Shapes\SVGPolyline;

/**
 * Class DonutArc
 * @package Cws\Bundle\SVGGraphBundle\Donut
 */
class DonutArc
{
    private $data;
    private $graphStyle;
    private $drawingData;

    /**
     * DonutArc constructor.
     *
     * @param $data
     * @param $graphStyle
     */
    public function __construct($data, $graphStyle)
    {
        $this->data = $data;
        $this->graphStyle = $graphStyle;
        $this->drawingData = $this->computeDrawData($data, $graphStyle);
    }

    /**
     * @param $data
     * @param $graphStyle
     * @return object
     */
    private function computeDrawData($data, $graphStyle)
    {
        $startAngle = $data->startAngle;
        $endAngle = $data->endAngle;
        $centerAngle = $startAngle + ($endAngle - $startAngle) / 2;
        $externalArcRadius = $graphStyle->radius + $graphStyle->thickness;
        $center = new Point($graphStyle->center->x, $graphStyle->center->y);
        $centerArcPoint = Point::angleToPoint(
            $center,
            $graphStyle->radius + $graphStyle->thickness / 2,
            $centerAngle
        );

        return (object) [
            'data' => $data->data,
            'radius' => $graphStyle->radius,
            'externalRadius' => $externalArcRadius,
            'startAngle' =>             $startAngle,
            'endAngle' => $endAngle,
            'center' => $center,
            'centerAngle' => $centerAngle,
            'centerArcPoint' => $centerArcPoint,
            'largeArcFlag' => abs($endAngle - $startAngle) > 180 ? '1' : '0',
            'isOnRight' => $centerArcPoint->x > $center->x,
            'isOnBottom' => $centerArcPoint->y > $center->y,
            'legendLinePoints' => (object) array(
                'point1' => $centerArcPoint,
                'point2' => null,
                'point3' => null
            ),
            'internalStartPoint' => Point::angleToPoint($center, $graphStyle->radius, $startAngle),
            'internalEndPoint' => Point::angleToPoint($center, $graphStyle->radius, $endAngle),
            'externalStartPoint' => Point::angleToPoint($center, $externalArcRadius, $startAngle),
            'externalEndPoint' => Point::angleToPoint($center, $externalArcRadius, $endAngle)
        ];
    }

    /**
     * @param Point $point2
     * @param Point $point3
     */
    public function setLegendLinesPoints(Point $point2, Point $point3)
    {
        $this->drawingData->legendLinePoints->point2 = $point2;
        $this->drawingData->legendLinePoints->point3 = $point3;
    }

    /**
     * Return the comple SVG string to draw a donut
     *
     * @return string
     */
    private function makeSVGDonutString()
    {
        $internalArc = Arc::arcFromTo(
            $this->drawingData->internalStartPoint,
            $this->drawingData->internalEndPoint,
            $this->drawingData->radius,
            0,
            $this->drawingData->largeArcFlag,
            1
        );

        $line1 = Line::lineTo($this->drawingData->externalEndPoint);

        $externalArc = Arc::arcTo(
            $this->drawingData->externalStartPoint,
            $this->drawingData->externalRadius,
            0,
            $this->drawingData->largeArcFlag,
            0
        );

        $line2 = Line::lineTo($this->drawingData->internalStartPoint);

        return implode(' ', array($internalArc, $line1, $externalArc, $line2));
    }

    /**
     * Create and return the SVGPath of one arc
     *
     * @return SVGPath
     */
    public function create()
    {
        $graph = new SVGPath($this->makeSVGDonutString($this->graphStyle));
        $graph->setStyle('fill', $this->data->data->color);

        return $graph;
    }

    /**
     * @return SVGPolyline
     */
    public function getLegendLine()
    {
        $line = new SVGPolyline([
            $this->drawingData->legendLinePoints->point1->toArray(),
            $this->drawingData->legendLinePoints->point2->toArray(),
            $this->drawingData->legendLinePoints->point3->toArray(),
        ]);

        $line->setStyle('stroke', $this->graphStyle->legend->lineColor);
        $line->setStyle('fill', 'none');

        return $line;
    }

    /**
     * @return object
     */
    public function getDrawingData()
    {
        return $this->drawingData;
    }
}
