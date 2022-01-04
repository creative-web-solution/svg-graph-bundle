<?php

namespace Cws\Bundle\SVGGraphBundle\SVGGraph\Lines;

use Cws\Bundle\SVGGraphBundle\ISVGGraph;
use Cws\Bundle\SVGGraphBundle\Tools\Line;
use Cws\Bundle\SVGGraphBundle\Tools\Point;
use SVG\Nodes\Shapes\SVGPath;
use SVG\Nodes\Structures\SVGDocumentFragment;

/**
 * Class Lines
 * @package Cws\Bundle\SVGGraphBundle\Lines
 */
class Lines implements ISVGGraph
{
    private $data;
    private $drawingData;
    private $graphStyle;
    private $linesList;

    /**
     * Lines constructor.
     *
     * @param $data
     * @param $graphStyle
     */
    public function __construct($data, $graphStyle)
    {
        $this->data  = $data;
        $this->graphStyle = $graphStyle;
        $this->drawingData = $this->computeDrawingData($data, $graphStyle);
    }


    /**
     * Compute points list, min and max value of ONE line
     *
     * @param $lineData
     * @param $graphStyle
     * @param $horizontalStep
     *
     * @return object
     */
    private function computePointsList($lineData, $graphStyle, $horizontalStep)
    {
        $pointsList = $ordMinMax = $absSteps = [];
        $min = $graphStyle->axes->ord->min;
        $max = $graphStyle->axes->ord->max;
        $delta = $max - $min;

        foreach ($lineData->values as $key => $value) {
            $pointsList[] = new Point(
                $graphStyle->canvas->left + $graphStyle->canvas->marginX + $horizontalStep * $key,
                $graphStyle->canvas->top + $graphStyle->canvas->height -
                    round(($value->value - $min) * $graphStyle->canvas->height / $delta, 3)
            );

            if (isset($ordMinMax[$key])) {
                // /!\ WARNING here: The higher the value is the smaller is top coordinate is.
                // So min and max are inverted
                $ordMinMax[$key]->min = max($ordMinMax[$key]->min, $pointsList[$key]->y);
                $ordMinMax[$key]->max = min($ordMinMax[$key]->max, $pointsList[$key]->y);
            } else {
                $ordMinMax[] = (object) [
                    'min' => $pointsList[$key]->y,
                    'max' => $pointsList[$key]->y,
                ];
            }

            $absSteps[] = $pointsList[$key]->x;
        }

        return (object) array(
            'pointsList' => $pointsList,
            'ordMinMax' => $ordMinMax,
            'absSteps' => $absSteps
        );
    }

    /**
     * Compute the min, max coordinate over ALL lines
     *
     * @param $drawingData
     *
     * @return object
     */
    private function computeGlobalDrawingData($drawingData)
    {
        $globalOrdMinMax = [];
        $globalAbsSteps = [];

        foreach ($drawingData as $currentLineData) {
            foreach ($currentLineData->ordMinMax as $key => $ordMinMax) {
                if (isset($globalOrdMinMax[$key])) {
                    // /!\ WARNING here: The higher the value is the smaller is top coordinate is.
                    // So min and max are inverted
                    $globalOrdMinMax[$key]->min = max($globalOrdMinMax[$key]->min, $ordMinMax->min);
                    $globalOrdMinMax[$key]->max = min($globalOrdMinMax[$key]->max, $ordMinMax->max);
                } else {
                    $globalOrdMinMax[] = (object) [
                        'min' => $ordMinMax->min,
                        'max' => $ordMinMax->max,
                    ];
                }
            }

            foreach ($currentLineData->absSteps as $key => $absSteps) {
                if (isset($globalAbsSteps[$key])) {
                    $globalAbsSteps[$key] = $absSteps;
                } else {
                    $globalAbsSteps[] = $absSteps;
                }
            }
        }

        return (object) [
            'ordMinMax' => $globalOrdMinMax,
            'absSteps' => $globalAbsSteps,
            'top' => $this->graphStyle->canvas->top,
            'bottom' => $this->graphStyle->canvas->top + $this->graphStyle->canvas->height,
            'left' => $this->graphStyle->canvas->left,
            'right' => $this->graphStyle->canvas->left + $this->graphStyle->canvas->width,
            'width' => $this->graphStyle->canvas->width,
            'height' => $this->graphStyle->canvas->height,
        ];
    }

    /**
     * Compute all the data required to draw the graph
     *
     * @param $data
     * @param $graphStyle
     *
     * @return object
     */
    private function computeDrawingData($data, $graphStyle)
    {
        $drawingData = [];
        $horizontalStep = round(($graphStyle->canvas->width - $graphStyle->canvas->marginX * 2) /
            count($graphStyle->axes->abs->labels), 3)
        ;

        foreach ($data->lines as $currentLineData) {
            $pointsList = $this->computePointsList($currentLineData, $graphStyle, $horizontalStep);
            $lineData = (object) [
                'horizontalStep' => $horizontalStep,
                'data' => $currentLineData,
                'pointsList' => $pointsList->pointsList,
                'ordMinMax' => $pointsList->ordMinMax,
                'absSteps' => $pointsList->absSteps,
            ];

            $drawingData[] = $lineData;
        }

        return (object) [
            'data' => $drawingData,
            'globalData' => $this->computeGlobalDrawingData($drawingData),
        ];
    }

    /**
     * @param $data
     * @param $graphStyle
     *
     * @return SVGPath
     */
    private function createLine($data, $graphStyle)
    {
        $svgLine = [];

        foreach ($data->pointsList as $index => $point) {
            $svgLine[] = $index == 0 ? Line::lineFrom($point) : Line::lineTo($point);
        }

        $svgPath = implode(' ', $svgLine);
        $line = new SVGPath($svgPath);

        $line
            ->setStyle('stroke', $data->data->color)
            ->setStyle('stroke-width', $data->data->thickness)
            ->setStyle('stroke-linecap', $graphStyle->linecap)
            ->setStyle('stroke-linejoin', $graphStyle->linejoin)
            ->setStyle('fill', 'none')
        ;

        return $line;
    }

    /**
     * @param $drawingData
     * @param $graphStyle
     */
    private function createAllLines($drawingData, $graphStyle)
    {
        $this->linesList = [];

        foreach ($drawingData as $currentLineData) {
            $this->linesList[] = $this->createLine($currentLineData, $graphStyle);
        }
    }

    /**
     * @param SVGDocumentFragment $svgDocument
     *
     * @return string
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        $axes = new Axes($this->data, $this, $this->graphStyle);
        $axes->create($svgDocument);

        $this->createAllLines($this->drawingData->data, $this->graphStyle);

        foreach ($this->linesList as $line) {
            $svgDocument->addChild($line);
        }
    }

    public function getLegend()
    {
        $legend = new Legend($this->data, $this, $this->graphStyle);

        return $legend->create();
    }

    /**
     * Return the computed data used to create the drawing
     *
     * @return object
     */
    public function getDrawingData()
    {
        return $this->drawingData;
    }
}
