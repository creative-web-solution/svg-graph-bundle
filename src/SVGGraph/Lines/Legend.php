<?php

namespace Cws\Bundle\SVGGraphBundle\Lines;

use Cws\Bundle\SVGGraphBundle\IGraphLegend;
use Cws\Bundle\SVGGraphBundle\Tools\Point;
use Cws\Bundle\SVGGraphBundle\Tools\Text;

/**
 * Class Legend
 * @package Cws\Bundle\SVGGraphBundle\Lines
 */
class Legend implements IGraphLegend
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
     * Create all the vertical lines of the grid
     */
    private function createHorizontalLabels()
    {
        $html = [];
        $html[] = sprintf('<div class="%s">', $this->graphStyle->axes->abs->wrapperCssClass);

        foreach ($this->drawingData->globalData->ordMinMax as $index => $ordData) {
            $point1 = new Point(
                $this->drawingData->globalData->absSteps[$index],
                $this->drawingData->globalData->bottom
            );

            $html[] = $this->createLabels(
                $this->graphStyle->axes->abs->labels[$index]->label,
                $point1,
                $this->graphStyle->axes->abs->labelCssClass
            );
        }

        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Create all the vertical lines of the grid
     */
    private function createVerticalLabels()
    {
        $html = [];
        $min = $this->graphStyle->axes->ord->min;
        $max = $this->graphStyle->axes->ord->max;
        $delta = $max - $min;
        $step = $this->graphStyle->axes->ord->step;

        $html[] = sprintf('<div class="%s">', $this->graphStyle->axes->ord->wrapperCssClass);

        for ($linePos = 0; $linePos <= $delta; $linePos = $linePos + $step) {
            $point1 = new Point(
                $this->drawingData->globalData->left,
                $this->drawingData->globalData->bottom -
                    round($linePos * $this->drawingData->globalData->height / $delta, 3)
            );

            $html[] = $this->createLabels(
                $linePos + $this->graphStyle->axes->ord->min,
                $point1,
                $this->graphStyle->axes->ord->labelCssClass
            );
        }

        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * @param Point $point1
     * @param $label
     * @param $cssClass
     *
     * @return Text
     */
    private function createLabels($label, Point $point1, $cssClass)
    {
        $text = new Text($label, $point1, $cssClass);

        return $text->create();
    }

    /**
     * @return string
     */
    public function create()
    {
        $html = '';
        $html .= $this->createHorizontalLabels();
        $html .= $this->createVerticalLabels();

        return $html;
    }
}
