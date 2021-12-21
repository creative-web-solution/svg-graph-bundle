<?php

namespace Cws\Bundle\SVGGraphBundle\Lines;

use Cws\Bundle\SVGGraphBundle\Tools\Point;
use SVG\Nodes\Shapes\SVGLine;
use SVG\Nodes\Structures\SVGDocumentFragment;

/**
 * Class Axes
 * @package Cws\Bundle\SVGGraphBundle\Lines
 */
class Axes
{
    private $data;
    private $graphStyle;
    private $graph;
    private $drawingData;
    private $labelList;

    /**
     * GraphLinesAxes constructor.
     *
     * @param $data
     * @param $graph
     * @param $graphStyle
     */
    public function __construct($data, $graph, $graphStyle)
    {
        $this->data = $data;
        $this->graphStyle = $graphStyle;
        $this->graph = $graph;
        $this->drawingData = $graph->getDrawingData();
        $this->labelList = [];
    }

    /**
     * Create a line between 2 points
     *
     * @param Point $point1
     * @param Point $point2
     * @param $color
     * @param $thickness
     *
     * @return SVGLine
     */
    private function getLine(Point $point1, Point $point2, $color, $thickness)
    {
        $line = new SVGLine($point1->x, $point1->y, $point2->x, $point2->y);

        $line
            ->setStyle('stroke', $color)
            ->setStyle('stroke-width', $thickness)
            ->setStyle('fill', 'none')
        ;

        return $line;
    }

    /**
     * Return the abscissal line
     *
     * @return SVGLine
     */
    private function createAbs()
    {
        $point1 = new Point(
            $this->graphStyle->canvas->left,
            $this->graphStyle->canvas->top + $this->graphStyle->canvas->height
        );
        $point2 = new Point(
            $point1->x + $this->graphStyle->canvas->width,
            $point1->y
        );

        return $this->getLine(
            $point1,
            $point2,
            $this->graphStyle->axes->abs->color,
            $this->graphStyle->axes->abs->thickness
        );
    }

    /**
     * Return the ordinate line
     *
     * @return SVGLine
     */
    private function createOrd()
    {
        $point1 = new Point(
            $this->graphStyle->canvas->left,
            $this->graphStyle->canvas->top
        );

        $point2 = new Point(
            $point1->x,
            $point1->y + $this->graphStyle->canvas->height
        );

        return $this->getLine(
            $point1,
            $point2,
            $this->graphStyle->axes->ord->color,
            $this->graphStyle->axes->ord->thickness
        );
    }

    /**
     * Create all the horizontal lines of the grid
     *
     * @param $svgDocument
     */
    private function createHorizontalGridLines(SVGDocumentFragment $svgDocument)
    {
        $min = $this->graphStyle->axes->ord->min;
        $max = $this->graphStyle->axes->ord->max;
        $delta = $max - $min;
        $step = $this->graphStyle->axes->ord->step;

        for ($linePos = $step; $linePos <= $delta; $linePos = $linePos + $step) {
            $point1 = new Point(
                $this->drawingData->globalData->left,
                $this->drawingData->globalData->bottom -
                    round($linePos * $this->drawingData->globalData->height / $delta, 3)
            );

            $point2 = new Point(
                $this->drawingData->globalData->right,
                $point1->y
            );

            $svgDocument->addChild(
                $this->getLine(
                    $point1,
                    $point2,
                    $this->graphStyle->grid->horizontal->color,
                    $this->graphStyle->grid->horizontal->thickness
                )
            );
        }
    }

    /**
     * Create all the vertical lines of the grid
     * @param $svgDocument
     */
    private function createVerticalGridLines(SVGDocumentFragment $svgDocument)
    {
        foreach ($this->drawingData->globalData->ordMinMax as $index => $ordData) {
            $point1 = new Point(
                $this->drawingData->globalData->absSteps[ $index ],
                $this->drawingData->globalData->bottom
            );

            $point2 = new Point(
                $point1->x,
                $ordData->max
            );

            $svgDocument->addChild(
                $this->getLine(
                    $point1,
                    $point2,
                    $this->graphStyle->grid->vertical->color,
                    $this->graphStyle->grid->vertical->thickness
                )
            );
        }
    }


    /**
     * @param SVGDocumentFragment $svgDocument
     *
     * @return string
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        if ($this->graphStyle->grid->horizontal->isDisplayed) {
            $this->createHorizontalGridLines($svgDocument);
        }

        if ($this->graphStyle->grid->vertical->isDisplayed) {
            $this->createVerticalGridLines($svgDocument);
        }

        if ($this->graphStyle->axes->abs->isDisplayed) {
            $svgDocument->addChild($this->createAbs());
        }

        if ($this->graphStyle->axes->ord->isDisplayed) {
            $svgDocument->addChild($this->createOrd());
        }
    }
}
