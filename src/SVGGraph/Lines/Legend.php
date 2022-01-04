<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Lines;

use Cws\Bundle\SVGGraphBundle\SVGGraph\IGraphLegend;
use Cws\Bundle\SVGGraphBundle\SVGGraph\Tools\Point;
use Cws\Bundle\SVGGraphBundle\SVGGraph\Tools\Text;

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

    private function createHorizontalLabels(): string
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

    private function createVerticalLabels(): string
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
     * @return string
     */
    private function createLabels($label, Point $point1, $cssClass): string
    {
        $text = new Text($label, $point1, $cssClass);

        return $text->create();
    }

    public function create()
    {
        $html = '';
        $html .= $this->createHorizontalLabels();
        $html .= $this->createVerticalLabels();

        return $html;
    }
}
