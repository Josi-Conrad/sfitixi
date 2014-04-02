<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.03.14
 * Time: 01:51
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


class DataGridOutputState {

    protected $gridIdentifier;
    protected $dataSrcUrl;
    protected $totalAmountOfRows;
    protected $headers;
    protected $rows;

    protected function __construct() {

    }

    public static function createOutputState($gridIdentifier, array $gridHeaders, array $gridRows, $totalAmountOfRows, $dataSrcUrl=null) {
        $outputState = new DataGridOutputState();
        $outputState->setGridIdentifier($gridIdentifier);
        $outputState->setHeaders($gridHeaders);
        $outputState->setRows($gridRows);
        $outputState->setTotalAmountOfRows($totalAmountOfRows);
        $outputState->setDataSrcUrl($dataSrcUrl);
        return $outputState;
    }

    public static function createEmbeddedOutputState($gridIdentifier, array $gridHeaders, $dataSrcUrl) {
        $outputState = new DataGridOutputState();
        $outputState->setGridIdentifier($gridIdentifier);
        $outputState->setHeaders($gridHeaders);
        $outputState->setDataSrcUrl($dataSrcUrl);
        return $outputState;
    }

    public static function createPartialOutputState($gridIdentifier, array $gridRows, $totalAmountOfRows) {
        $outputState = new DataGridOutputState();
        $outputState->setGridIdentifier($gridIdentifier);
        $outputState->setRows($gridRows);
        $outputState->setTotalAmountOfRows($totalAmountOfRows);
        return $outputState;
    }

    /**
     * @param null $dataSrcUrl
     */
    public function setDataSrcUrl($dataSrcUrl)
    {
        $this->dataSrcUrl = $dataSrcUrl;
    }

    /**
     * @return null
     */
    public function getDataSrcUrl()
    {
        return $this->dataSrcUrl;
    }

    /**
     * @param mixed $gridIdentifier
     */
    public function setGridIdentifier($gridIdentifier)
    {
        $this->gridIdentifier = $gridIdentifier;
    }

    /**
     * @return mixed
     */
    public function getGridIdentifier()
    {
        return $this->gridIdentifier;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param null $totalAmountOfRows
     */
    public function setTotalAmountOfRows($totalAmountOfRows)
    {
        $this->totalAmountOfRows = $totalAmountOfRows;
    }

    /**
     * @return null
     */
    public function getTotalAmountOfRows()
    {
        return $this->totalAmountOfRows;
    }






}