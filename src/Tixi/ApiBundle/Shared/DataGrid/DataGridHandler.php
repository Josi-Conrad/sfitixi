<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.03.14
 * Time: 00:53
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


use Doctrine\Common\Annotations\FileCacheReader;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Tixi\ApiBundle\Interfaces\AssemblerInterface;
use Tixi\ApiBundle\Shared\DataGrid\DataGridInputState;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridEmbeddedTile;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridRowTableTile;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridTile;
use Tixi\ApiBundle\Shared\Paginator;
use Tixi\ApiBundle\Tile\Core\PanelTile;
use Tixi\ApiBundle\Tile\Core\RootPanel;
use Tixi\CoreDomain\Shared\FastGenericEntityAccessorRepository;
use Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties\OrderBy;
use Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties\Search;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityProperty;

class DataGridHandler {

    public static $dataGirdReplaceIdentifier = '__replaceId__';

    protected $rowIdAnnotationClass = 'Tixi\ApiBundle\Shared\DataGrid\Annotations\GridRowId';
    protected $fieldAnnotationClass = 'Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField';
    protected $headerClass = 'Tixi\ApiBundle\Shared\DataGrid\DataGridHeader';
    protected $fieldClass = 'Tixi\ApiBundle\Shared\DataGrid\DataGridField';

    protected $annotationReader;
    protected $router;

    public function createDataGridTileByRequest(Request $request, DataGridAbstractController $gridControl) {
        $dataGridInputState = $this->initStateByRequest($request, $gridControl->getReferenceDTO());
        $fgeaFilter = $this->createFgeaFilter($dataGridInputState);
        $sourceDTOs = $gridControl->constructDtosFromFgeaFilter($fgeaFilter);
        $totalAmountOfRows = $gridControl->getTotalAmountOfRowsByFgeaFilter($fgeaFilter);
        return $this->createDataGridTile($dataGridInputState, $sourceDTOs, $totalAmountOfRows, $gridControl);
    }

    public function createEmbeddedDataGridTile(DataGridAbstractController $gridController) {
        $headers = $this->createHeaderArray($gridController->getReferenceDTO());
        $outputState = DataGridOutputState::createEmbeddedOutputState($gridController->getGridIdentifier(), $headers, $gridController->getDataSrcUrl());
        $embeddedTile = new DataGridEmbeddedTile($outputState, $gridController->createDataGridJsConf());
        $embeddedTile->add($gridController->createCustomControlTile());
        return $embeddedTile;
    }

    protected function initStateByRequest(Request $request, DataGridSourceClass $referenceDTO) {
        $page = $request->get('page');
        $limit = $request->get('limit');
        $orderByField = $request->get('orderbyfield');
        $orderByDirection = $request->get('orderbydirection');
        $filterstr = $request->get('filterstr');
        $correctedPage = Paginator::adjustPageForPagination($page);
        $partial = $request->get('partial');
        return new DataGridInputState($referenceDTO, $orderByField, $orderByDirection, $correctedPage, $limit, $filterstr, $partial);
    }

    protected function createDataGridTile(
        DataGridInputState $state, array $sourceDtos, $totalAmountOfRows, DataGridAbstractController $gridController) {
        $rows = $this->createRowsArray($sourceDtos);
        $returnTile = null;
        if(!$state->isPartial()) {
            $headers = $this->createHeaderArray($gridController->getReferenceDTO());
            $outputState = DataGridOutputState::createOutputState($gridController->getGridIdentifier(), $headers, $rows, $totalAmountOfRows);
            $dataGridTile = null;
            if(!$gridController->isInEmbeddedState()) {
                $returnTile = new RootPanel($gridController->getGridDisplayTitel());
                $dataGridTile = $returnTile->add(new DataGridTile($outputState, $gridController->createDataGridJsConf()));
            }else {
                $returnTile = new PanelTile($gridController->getGridDisplayTitel());
                $dataGridTile = $returnTile->add(new DataGridEmbeddedTile($outputState, $gridController->createDataGridJsConf()));
            }
            $dataGridTile->add($gridController->createCustomControlTile());
            $dataGridTile->add(new DataGridRowTableTile($outputState));
        }else {
            $outputState = DataGridOutputState::createPartialOutputState($gridController->getGridIdentifier(), $rows, $totalAmountOfRows);
            $returnTile = new DataGridRowTableTile($outputState);
        }
        return $returnTile;
    }

    public function createFgeaFilter(DataGridInputState $state) {
        $filter = new GenericEntityFilter($state->getSourceDTO()->getAccessQuery());
        $properties = $this->createEntityPropertiesArray($state->getSourceDTO());
        $restrictiveProperties = DataGridEntityProperty::getRestrictiveProperties($properties);
        $headerProperties = DataGridEntityProperty::getHeaderProperties($properties);
        if(count($restrictiveProperties)>0) {
            $filter->setRestrictiveProperties($restrictiveProperties);
        }
        if(null !== $state->getOrderByDirection() && null !== $state->getOrderByField()) {
            $orderFieldExploded = explode('.',$state->getOrderByField());
            if(count($orderFieldExploded)<2) {throw new \Exception('misformatted propertyId found on field '.$state->getOrderByField());}
            $filter->setOrderedBy(new OrderBy(new GenericEntityProperty($orderFieldExploded[0],$orderFieldExploded[1]), $state->getOrderByDirection()));
        }
        if(null !== $state->getFilterStr()) {
            $filter->setSearch(new Search($state->getFilterStr(), $headerProperties));
        }
        if(null !== $state->getPage() && null !== $state->getLimit()) {
            $filter->setOffset($state->getPage()*$state->getLimit());
        }
        if(null !== $state->getLimit()) {
            $filter->setLimit($state->getLimit());
        }
        return $filter;
    }

    protected function createEntityPropertiesArray(DataGridSourceClass $sourceClassInstance) {
        $sourceReflectionObject = new \ReflectionClass($sourceClassInstance);
        $properties = array();

        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->annotationReader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(!empty($dataGridFieldAnnotation->propertyId)) {
                $explodedPropertyId = explode('.', $dataGridFieldAnnotation->propertyId);
                if(count($explodedPropertyId)<2) {throw new \Exception('misformatted propertyId found on field '.$reflProperty->getName());}
                $entityName = $explodedPropertyId[0];
                $propertyName = $explodedPropertyId[1];
                $isRestrictive = $dataGridFieldAnnotation->restrictive;
                $isHeader = !empty($dataGridFieldAnnotation->headerName);
                $propertyValue = $reflProperty->getValue($sourceClassInstance);
                $properties[] = new DataGridEntityProperty($entityName, $propertyName, $propertyValue, $isRestrictive, $isHeader);
            }
        }
        return $properties;
    }

    protected function createHeaderArray(DataGridSourceClass $sourceClassInstance) {
        $sourceReflectionObject = new \ReflectionClass($sourceClassInstance);
        $headers = array();

        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->annotationReader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(null !== $dataGridFieldAnnotation) {
                if(!empty($dataGridFieldAnnotation->headerName)) {
                    $rowId = $dataGridFieldAnnotation->propertyId;
                    if(null === $rowId) {throw new \Exception('no propertyId found on field '.$reflProperty->getName());}
                    $headers[] = new DataGridHeader($rowId, $dataGridFieldAnnotation->headerName, $dataGridFieldAnnotation->order);
                }
            }
        }
        usort($headers, $this->headerClass . '::compare');
        return $headers;
    }

    protected function createRowsArray(array $sourceArray) {
        $rowsArray = array();
        foreach($sourceArray as $rowSource) {
            $row = $this->createRow($rowSource);
            $rowsArray[] = array('rowId'=>$row->getRowId(),'fieldValues'=>$row->getFieldValues());
        }
        return $rowsArray;
    }

    protected function createRow(DataGridSourceClass $source) {
        $sourceReflectionObject = new \ReflectionClass($source);
        $rowId = null;
        $fields = array();
        foreach($sourceReflectionObject->getProperties() as $reflProperty) {
            $dataGridFieldAnnotation = $this->annotationReader->getPropertyAnnotation($reflProperty, $this->fieldAnnotationClass);
            if(null !== $dataGridFieldAnnotation) {
                if($dataGridFieldAnnotation->rowIdentifier) {
                    if(null !== $rowId) {throw new \Exception('found duplicated row id');}
                    $rowId = $reflProperty->getValue($source);
                }
                if(!empty($dataGridFieldAnnotation->headerName)) {
                    $fields[] = new DataGridField($reflProperty->getName(), $reflProperty->getValue($source), $dataGridFieldAnnotation->order);
                }
            }
        }
        if(null === $rowId) {throw new \Exception('no row id found');}
        $row = new DataGridRow($rowId);
        $row->appendAndSortFields($fields, $this->fieldClass . '::compare');
        return $row;
    }

    public function setReader(FileCacheReader $reader) {
        $this->annotationReader = $reader;
    }

    public function setRouter(Router $router) {
        $this->router = $router;
    }

} 