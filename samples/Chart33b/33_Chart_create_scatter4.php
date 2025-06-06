<?php

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray(
    [
        ['', 2010, 2011, 2012],
        ['Q1', 12, 15, 21],
        ['Q2', 56, 73, 86],
        ['Q3', 52, 61, 69],
        ['Q4', 30, 32, 0],
    ]
);

// Set the Labels for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 2010
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // 2011
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), // 2012
];
// Set the X-Axis Labels
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
];
// Set the Data values for each data series we want to plot
//     Datatype
//     Cell reference for data
//     Format Code
//     Number of datapoints in series
//     Data values
//     Data Marker
$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', null, 4),
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_SCATTERCHART, // plotType
    null, // plotGrouping (Scatter charts don't have any grouping)
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues, // plotValues
    null, // plotDirection
    false, // smooth line
    DataSeries::STYLE_LINEMARKER  // plotStyle
);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);

$pos1 = 0; // pos = 0% (extreme low side or lower left corner)
$brightness1 = 0; // 0%
$gsColor1 = new ChartColor();
$gsColor1->setColorProperties('FF0000', 75, 'srgbClr', $brightness1); // red
$gradientStop1 = [$pos1, $gsColor1];

$pos2 = 0.5; // pos = 50% (middle)
$brightness2 = 0.5; // 50%
$gsColor2 = new ChartColor();
$gsColor2->setColorProperties('FFFF00', 50, 'srgbClr', $brightness2); // yellow
$gradientStop2 = [$pos2, $gsColor2];

$pos3 = 1.0; // pos = 100% (extreme high side or upper right corner)
$brightness3 = 0.5; // 50%
$gsColor3 = new ChartColor();
$gsColor3->setColorProperties('00B050', 50, 'srgbClr', $brightness3); // green
$gradientStop3 = [$pos3, $gsColor3];

$gradientFillStops = [
    $gradientStop1,
    $gradientStop2,
    $gradientStop3,
];
$gradientFillAngle = 315.0; // 45deg above horiz

$plotArea->setGradientFillProperties($gradientFillStops, $gradientFillAngle);

// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

$title = new Title('Test Scatter Chart');
$yAxisLabel = new Title('Value ($k)');

// Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A7');
$chart->setBottomRightPosition('H20');

// Add the chart to the worksheet
$worksheet->addChart($chart);

$helper->renderChart($chart, __FILE__);

// Save Excel 2007 file
$helper->write($spreadsheet, __FILE__, ['Xlsx'], true);
