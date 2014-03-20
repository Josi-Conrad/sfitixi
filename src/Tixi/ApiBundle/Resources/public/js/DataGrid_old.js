/**
 * Created by faustos on 07.03.14.
 */
var global = this;

function DataGrid() {
    if(this == global) {return new DataGrid(arguments);}

    var _this = this,
        _dataGridIdString = 'data-gridId',
        _rowIdPrefix='row_',
        _totalAmountIdentifier = 'totalAmount';

    this._dataUrl = null;
    this._dataIdentifier = null;
    this._dataFieldId = null;
    this._gridId = null;
    this._gridOutline = null;
    this._headers = [];
    this._rows = [];
    this._activeRow = null;

    this._paginationModule = null;

    this._amountPerPage = 20;

    this._tableBody = null;

    this._topControlWrapper = $('<div class="topControlWrapper" />');
    this._bottomControlWrapper = $('<div class="bottomControlWrapper" />');
    this._tableWrapper = $('<div class="tableWrapper" />');


    this._orderedByHeader = null;
    this._filterstr = null;


    this.init = function(conf) {
        var _tempGridOutline;
        if(conf && conf.gridId && conf.dataIdentifier && conf.dataFieldId && conf.fields && conf.dataURL) {
            _this._dataIdentifier = conf.dataIdentifier;
            _this._gridId = conf.gridId;
            _tempGridOutline = this._findGridOutline(conf.gridId);
            if(_tempGridOutline) {
                _this._gridOutline = $(_tempGridOutline);
                _this._dataFieldId = conf.dataFieldId;
                _this._dataUrl = conf.dataURL;
                _this._headers = _this._constructHeaders(conf.fields);
                _this._paginationModule = new DataGridPagination(_this._amountPerPage);
                _this._initGrid();
                _this.updateData(true);
            }
        }
    }

    this._findGridOutline = function(gridId) {
        var _toReturn;
        $('.dataGrid').each(function(index, element) {
            if($(element).attr(_dataGridIdString)===gridId) {
                _toReturn = element;
            }
        });
        return _toReturn || null;
    }

    this._constructHeaders = function(headers) {
        var _headersToReturn = [];
        headers.forEach(function(header,index) {
            _headersToReturn.push(new DataGridHeader(header.fieldId,header.fieldDisplayName,index));
        });
        return _headersToReturn;
    }

    this._initGrid = function() {
        _this._gridOutline.append(_this._topControlWrapper).append(_this._tableWrapper).append(_this._bottomControlWrapper);
        _this._initControls();
        _this._initTable();
    }



    this._initControls = function() {
        var _findControl = _this._constructFindControl(),
            _actionControl = _this._constructActionControl(),
            _paginationControl = _this._paginationModule.constructUi(_this.onPagination)

        _this._topControlWrapper.append(_findControl).append(_actionControl);
        _this._bottomControlWrapper.append(_paginationControl);
    }

    this._initTable = function() {
        var _table = _this._constructTable(),
            _tableHeader = _this._constructTableHeaders(),
            _tableBody = _this._constructTableBody();

        _this._tableBody = _tableBody;

        _this._tableWrapper.append(_table.append(_tableHeader).append(_tableBody));
    }

    this.updateData = function(resetPagination) {
        _this._pollDataFromSource(_this._constructDataParams()).done(function(data) {
            if(resetPagination) {
                _this._updatePagination(data[_totalAmountIdentifier]);
            }
            _this._updateTableRows(data[_this._dataIdentifier]);
        }).fail(_this._onDataUpdateWithFailure);
    }

    this._constructDataParams = function() {
        var _jsonToReturn = {};
        if(_this._orderedByHeader && _this._orderedByHeader.isOrdered()) {
            _jsonToReturn['orderbyfield'] = _this._orderedByHeader.getFieldName();
            _jsonToReturn['orderbydirection'] = _this._orderedByHeader.getOrderingState();
        }
        if(_this._filterstr && _this._filterstr!=='') {
            _jsonToReturn['filterstr'] = _this._filterstr;
        }
        _jsonToReturn['page'] = _this._paginationModule.getPage();
        _jsonToReturn['limit'] = _this._paginationModule.getLimit();
        return _jsonToReturn;
    }

    this._onDataUpdateWithFailure = function() {

    }

    this._updatePagination = function(totalAmount) {
        _this._paginationModule.update(totalAmount, _this._amountPerPage);
    }

    this._updateTableRows = function(fieldData) {
        var _row,
            _rowModel;
        _this._tableBody.empty();
        _this._rows = [];
        console.log(fieldData.length)

        fieldData.forEach(function(field) {
            _rowModel = new DataGridRow(field[_this._dataFieldId]);
            _row = _this._constructTableRow(field);
            _rowModel.registerUi(_row,_this.onRowClick, _this.onRowDoubleClick);
            _this._rows.push(_rowModel);
            _this._tableBody.append(_row);
        });
    }

    this._constructFindControl = function() {
        var _controlOutline = $('<div class="findControl"></div>'),
            _searchTextField = $('<input type="search" class="gridSearch">'),
            _searchSubmitButton = $('<input type="button" class="search" value="Suchen">');
        _searchSubmitButton.on('click',function() {
            _this._onSearchControlButtonClick(_searchTextField.val());
        });
        _controlOutline.append(_searchTextField);
        _controlOutline.append(_searchSubmitButton);
        return _controlOutline;
    }

    this._constructActionControl = function() {
        var _controlOutline = $('<div class="actionControl"></div>'),
            _deleteButton = $('<input type="button" class="delete" value="Löschen">'),
            _editButton = $('<input type="button" class="edit" value="Editieren">'),
            _newButton = $('<input type="button" class="new" value="Neu">');
        _deleteButton.on('click',_this._onDeleteButtonClick);
        _editButton.on('click',_this._onEditButtonClick);
        _newButton.on('click',_this._onNewButtonClick);
//        _controlOutline.append(_deleteButton).append(_editButton).append(_newButton);
        _controlOutline.append(_newButton);
        return _controlOutline;
    }

    this._constructTable = function() {
        var _table = $('<table/>')
        return _table;
    }

    this._constructTableHeaders = function() {
        var _tableHeader = $('<thead/>'),
            _row = _tableHeader.append($('<tr/>')),
            _ui;
        _this._headers.forEach(function(header) {
            _ui = $('<td>'+header.getDisplayName()+'</td>');
            header.registerUi(_ui, _this.onTableHeaderClick);
            _row.append(_ui);
        });
        return _tableHeader;

    }

    this._constructTableBody = function() {
        var _tableBody = $('<tbody/>');
        return _tableBody;
    }

    this._constructTableRow = function(dataRow) {
        var _tableRow = $('<tr id='+_this._constructTabelRowPrefix()+dataRow[_this._dataFieldId]+'/>');
        _this._headers.forEach(function(header) {
            _tableRow.append('<td>'+dataRow[header.getFieldName()]+'</td>');
        });
        return _tableRow;
    }

    this._constructTabelRowPrefix = function() {
        return _rowIdPrefix+_this._gridId+'_';
    }

    this.onTableHeaderClick = function(sourceHeader) {
        _this._headers.forEach(function(header, index) {
            if(header===sourceHeader) {
                _this._orderedByHeader = header;
            }else {
                header.resetOrdering();
            }
        });
        _this.updateData(true);
    }

    this.onRowClick = function(rowModel) {
        if(_this._activeRow) {
            _this._activeRow.reset();
        }
        _this._activeRow = rowModel;
    }

    this.onRowDoubleClick = function(rowModel) {
        window.location = _this._dataUrl+'/'+rowModel.getId()+'/edit';
    }

    this._onSearchControlButtonClick = function(searchText) {
        console.log('check'+searchText)
        _this._filterstr=searchText;
        _this._paginationModule.reset();
        _this.updateData(true);
    }

    this._onDeleteButtonClick = function() {

    }

    this._onEditButtonClick = function() {

    }

    this._onNewButtonClick = function() {
        window.location = _this._dataUrl+'/new';
    }

    this.onPagination = function() {
        _this.updateData(false);
    }

    this._pollDataFromSource = function(params) {
        return $.ajax({
            type: 'GET',
            url: _this._dataUrl+'.json?'+ $.param(params, true),
            dataType: 'json'
        });
    }
}

function DataGridPagination(defaultLimit) {
    if(this == global) {return new DataGridPagination(arguments);}

    var _this = this;

    this._defaultLimit = defaultLimit;
    this._uiElement = null;

    this._firstButton = null;
    this._previousButton = null;
    this._nextButton = null;
    this._lastButton = null;

    this._pageIndication = null;

    this._page = 1;
    this._limit = defaultLimit;

    this._totalAmountOfPages = null;

    this._callback = null;

    this.constructUi = function(callback) {
        if(!_this._uiElement) {
            var _controlButtonWrapper = $('<div class="paginationControl"/>'),
                _firstButton = $('<input type="button" class="first" value="<<">'),
                _previousButton = $('<input type="button" class="previous" value="<">'),
                _nextButton = $('<input type="button" class="next" value =">">'),
                _lastButton = $('<input type="button" class="last" value=">>">'),
                _pageIndication = $('<div class="pageIndication" />');

            _firstButton.on('click',_this._onFirstButtonClick);
            _previousButton.on('click',_this._onPreviousButtonClick);
            _nextButton.on('click',_this._onNextButtonClick);
            _lastButton.on('click',_this._onLastButtonClick);

            _this._firstButton = _firstButton;
            _this._previousButton = _previousButton;
            _this._nextButton = _nextButton;
            _this._lastButton = _lastButton;
            _this._pageIndication = _pageIndication;

            _controlButtonWrapper.append(_firstButton).append(_previousButton).append(_pageIndication).append(_nextButton).append(_lastButton);
            _this._uiElement = _controlButtonWrapper;
            _this._hideControl();
            _this._callback = callback;
        }
        return _this._uiElement;
    }

    this.update = function(totalAmount, limit) {
        _this._totalAmountOfPages = Math.ceil(totalAmount/limit);
        _this._page = 1;
        if(typeof limit !== 'undefined') {
            _this._limit = limit;
        }
        _this._updatePageIndication();
        _this._toggleControlVisiability();
    }

    this.reset = function() {
        _this._page = 1;
    }

    this.getPage = function() {
        return _this._page;
    }

    this.getLimit = function() {
        return _this._limit;
    }

    this._onFirstButtonClick = function() {
        _this._page = 1;
        _this._notifyDataGrid();
    }

    this._onPreviousButtonClick = function() {
        if(_this._page>1) {
            _this._page--;
            _this._notifyDataGrid();
        }
    }

    this._onNextButtonClick = function() {
        if(_this._page<_this._totalAmountOfPages) {
            _this._page++;
            _this._notifyDataGrid();
        }
    }

    this._onLastButtonClick = function() {
        _this._page = _this._totalAmountOfPages;
        _this._notifyDataGrid();
    }

    this._updatePageIndication = function() {
        _this._pageIndication.html(_this._page+'/'+_this._totalAmountOfPages);
    }

    this._notifyDataGrid = function() {
        _this._updatePageIndication();
        _this._callback();
    }

    this._toggleControlVisiability = function() {
        if(_this._totalAmountOfPages<=1) {
            _this._hideControl();
        }else {
            _this._showControl();
        }
    }

    this._hideControl = function() {
        _this._uiElement.hide();
    }

    this._showControl = function() {
        _this._uiElement.show();
    }

}


function DataGridRow(id) {
    if(this == global) {return new DataGridRow(arguments);}

    var _this = this;

    this._id = id;
    this._uiElement = null;
    this._clickCallback = null;
    this._doubleClickCallback = null;

    this.registerUi = function(ui, clickCallback, doubleClickCallback) {
        _this._uiElement = ui;
        _this._clickCallback = clickCallback;
        _this._doubleClickCallback = doubleClickCallback;
        _this._uiElement.on('click', _this._onRowClick);
        _this._uiElement.on('dblclick', _this._onRowDblClick);
    }

    this.reset = function() {
        _this._uiElement.removeClass('activeRow');
    }

    this._onRowClick = function() {
        _this._uiElement.addClass('activeRow');
        _this._clickCallback(_this);
    }

    this._onRowDblClick = function() {
        _this._doubleClickCallback(_this);
    }

    this.getId = function() {
        return _this._id;
    }
}

function DataGridHeader(fieldName, displayName, index) {
    if(this == global) {return new DataGrid(arguments);}

    var _this = this,
        _orderingStates = {
            asc : 'ASC',
            desc : 'DESC'
        };

    this._index = index;
    this._fieldName = fieldName;
    this._displayName = displayName;

    this._callback = null;
    this._uiElement = null;

    this._orderingState = null;

    this.registerUi = function(ui, callback) {
        _this._uiElement = ui;
        _this._callback = callback;
        _this._uiElement.on('click',_this._onHeaderClick);
    }

    this.resetOrdering = function() {
        _this._orderingState = null;
    }


    this.getDisplayName = function() {
        return _this._displayName;
    }

    this.getFieldName = function() {
        return _this._fieldName;
    }

    this.getIndex = function() {
        return _this._index;
    }

    this.getOrderingState = function() {
        return _this._orderingState;
    }

    this.isOrdered = function() {
        return (_this._orderingState !== null);
    }

    this._onHeaderClick = function() {
        _this._toggleOrderingState();
        _this._callback(_this);
    }


    this._toggleOrderingState = function() {
        if(!_this._orderingState || _this._orderingState === _orderingStates.desc) {
            _this._orderingState = _orderingStates.asc;
        }else {
            _this._orderingState = _orderingStates.desc;
        }
    }
}