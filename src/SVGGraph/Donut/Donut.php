<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Donut;

use Cws\Bundle\SVGGraphBundle\ISVGGraph;
use Cws\Bundle\SVGGraphBundle\Tools\Point;
use SVG\Nodes\Structures\SVGDocumentFragment;

/**
 * Class Donut
 * @package Cws\Bundle\SVGGraphBundle\Donut
 */
class Donut implements ISVGGraph
{
    const TOTAL_ANGLE = 360;
    const MIN_ARC_ANGLE = 10;

    private $data;
    private $graphStyle;
    private $drawingData;
    private $hasLegend;

    /**
     * Donut constructor.
     *
     * @param $data
     * @param $graphStyle
     */
    public function __construct($data, $graphStyle)
    {
        $this->data = $data;
        $this->graphStyle = $graphStyle;
        $this->hasLegend = isset($this->graphStyle->legend);
        $this->drawingData = $this->computeData($this->data, $this->graphStyle);
    }

    /**
     * Sort the arcs dépending on their y position
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function sortArc($a, $b)
    {
        if ($a->y == $b->y) {
            return 0;
        }

        return ($a->y < $b->y) ? -1 : 1;
    }

    /**
     * Create arcs
     *
     * @param $data
     * @param $graphStyle
     *
     * @return object
     */
    private function computeData($data, $graphStyle)
    {
        $lastAngle = $graphStyle->angleOffset;
        $count = count($data);
        $total = 0;
        $arcList = [];
        $sortedLeftArcList = [];
        $sortedRightArcList = [];

        foreach ($data as $value) {
            $total = $total + $value->value;
        };

        foreach ($data as $index => $value) {
            if ($index < $count - 1) {
                $nextAngle = $lastAngle + $value->value * Donut::TOTAL_ANGLE / $total;
            } else {
                $nextAngle = Donut::TOTAL_ANGLE + $graphStyle->angleOffset;
            }

            $nextAngle = max($nextAngle, Donut::MIN_ARC_ANGLE);

            $arcData = (object) [
                'id' => $index,
                'data' => $value,
                'total' => $total,
                'startAngle' => $lastAngle,
                'endAngle' => $nextAngle,
            ];

            $donutArcData = new DonutArc($arcData, $graphStyle);
            $donutDrawingArcData = $donutArcData->getDrawingData();

            // Used for SVG construction. Must be in the same order as original data
            $arcList[] = $donutArcData;

            if ($this->hasLegend) {
                // Used for label construction. Must be ordered depending of the y position
                if ($donutDrawingArcData->isOnRight) {
                    $sortedRightArcList[] = (object)array(
                        'id' => $index,
                        'y' => $donutDrawingArcData->legendLinePoints->point1->y,
                        'arc' => $donutArcData,
                    );
                } else {
                    $sortedLeftArcList[] = (object) [
                        'id' => $index,
                        'y' => $donutDrawingArcData->legendLinePoints->point1->y,
                        'arc' => $donutArcData,
                    ];
                }
            }

            $lastAngle = $nextAngle;
        }

        if ($this->hasLegend) {
            $height = $this->graphStyle->height;

            $this->createLegendLinePoints(
                $sortedRightArcList,
                $height,
                $this->graphStyle->width - $this->graphStyle->legend->textMaxWidth,
                $this->graphStyle->width
            );

            $this->createLegendLinePoints(
                $sortedLeftArcList,
                $height,
                $this->graphStyle->legend->textMaxWidth,
                0
            );
        }

        return (object) [
            'arcList' => $arcList,
            'center' => new Point($graphStyle->center->x, $graphStyle->center->y),
        ];
    }

    /**
     * @param array $array
     * @param $height
     * @param $x1
     * @param $x2
     */
    private function createLegendLinePoints(array &$array, $height, $x1, $x2)
    {
        usort($array, [$this, 'sortArc']);

        $count = count($array);
        $step = $count > 1 ? round($height / ($count + 1)) : round($height / 2);

        foreach ($array as $index => $value) {
            $legendY = $step + $step * $index + $this->graphStyle->legend->textHeight / 2;

            $value->arc->setLegendLinesPoints(
                new Point($x1, $legendY),
                new Point($x2, $legendY)
            );
        }
    }

    /**
     * Return the SVG string of the donut
     *
     * @param SVGDocumentFragment $svgDocument
     *
     * @return string
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        foreach ($this->drawingData->arcList as $arc) {
            $svgDocument->addChild($arc->create());
        }

        if ($this->hasLegend && $this->graphStyle->legend->lineColor) {
            foreach ($this->drawingData->arcList as $arc) {
                $svgDocument->addChild($arc->getLegendLine());
            }
        }
    }

    public function getLegend()
    {
        if ($this->hasLegend) {
            $legend = new Legend($this->data, $this, $this->graphStyle);

            return $legend->create();
        }

        return '';
    }

    public function getDrawingData()
    {
        return $this->drawingData;
    }
}
