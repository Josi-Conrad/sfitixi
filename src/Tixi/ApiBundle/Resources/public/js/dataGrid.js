/**
 * Created by faustos on 07.03.14.
 */
var global = this;

function DataGridManager() {
    if(this == global) {return new DataGridManager(arguments);}

    var _this = this,
        _dataGridClass = '.dataGrid',
        _dataGridIdDataAttribut = 'data-gridid',
        _gridId,
        _callback;

    this._dataGrids = [];

    this.initDataGrids = function(conf) {
        $(_dataGridClass).each(function(index, outline) {
            _callback = function(){};
            _gridId = $(outline).attr(_dataGridIdDataAttribut);
            if(conf && conf[_gridId] && conf[_gridId].dblClickCallback) {
                _callback = conf[_gridId].dblClickCallback;
            }
            _this._dataGrids.push(new DataGrid(outline,_gridId,_callback));
        });
    }
}

function DataGrid(outline, gridId, dblClickCallback) {
    if(this == global) {return new DataGrid(arguments);}

    var _this = this,
        _defaultLimit = 15;

    this._outline = null;
    this._gridId = null;
    this._headers = null;
    this._paginator = null;

    this._dblClickCallback = null;
    this._customActionWithSelectionButton = null;

    this._activeRow = null;
    this._filterstr = null;
    this._orderedByHeader = null;

    this._init = function(outline, gridId, dblClickCallback) {
        _this._outline = $(outline);
        _this._dblClickCallback = dblClickCallback;
        _this._gridId = gridId;
        _this._initHeaders();
        _this._initDataSrcUrl();
        _this._initControls();
        _this._initCustomControlsWithSelection();
        _this._initRowListener();
        _this._paginator.refresh(_this._getTotalAmountOfRows());
    }

    this._initHeaders = function() {
        _this._headers = [];
        _this._outline.find('.header').each(function(index, header) {
            _this._headers.push(new DataGridHeader($(header), $(header).attr('data-fieldid'),_this._onHeaderClick));
        });
    }

    this._initDataSrcUrl = function() {
        _this._dataSrcUrl = _this._outline.find('.tableWrapper table').attr('data-srcurl');
    }

    this._initControls = function() {
        _this._initFilterControl();
        _this._initPaginationControl();
        _this._initCustomControls();
    }

    this._initFilterControl = function() {
        var _filterControlOutline = $('.filterControl'),
            _filterTextInput = _filterControlOutline.find('.gridFilterInput'),
            _filterCommitButton = _filterControlOutline.find('.gridFilterCommit');
        _filterCommitButton.on('click', function() {
            _this._onFilterControlActivation(_filterTextInput.val());
        });
    }

    this._initPaginationControl = function() {
        _this._paginator = new DataGridPaginator();
        _this._paginator.init(_this._outline, _defaultLimit, _this._onPaginationActivity);
    }

    this._initCustomControlsWithSelection = function() {
        var _url;
        _this._customActionWithSelectionButton = $('.actionControl .customActionWithSelectionButton');
        $(_this._customActionWithSelectionButton).prop("disabled",true);
        $('.actionControl .customActionWithSelection').each(function() {
            $(this).on('click', function(event) {
                event.preventDefault();
                if(_this._activeRow) {
                    _url = $(this).attr('data-targetSrc').replace('--selectionid--', _this._getIdFromRow(_this._activeRow));
                    window.location = _url;
                }
            });
        });
    }

    this._initCustomControls = function() {
        var _url;
        $('.actionControl .customAction').each(function() {
            $(this).on('click', function(event) {
                event.preventDefault();
                _url = $(this).attr('data-targetSrc')
                window.location = _url;
            });
        });
    }

    this._updateData = function(resetPagination) {
        _this._pollDataFromSource(_this._constructDataParams()).done(function(data) {
            _this._getTableBody().replaceWith($(data));
            _this._initRowListener();
            _this._activeRow = null;
            _this._updateVisibilityOfCustomActionButton();
            if(resetPagination) {
                _this._paginator.refresh(_this._getTotalAmountOfRows());
            }
        }).fail(function() {
            //ToDo Exception handling
        });
    }

    this._getTableBody = function() {
        return $(_this._outline.find('.tableWrapper tbody'));
    }

    this._getTotalAmountOfRows = function() {
        return _this._getTableBody().attr('data-totalamountofrows');
    }

    this._initRowListener = function() {
        var _rowId;
        _this._outline.find('.tableWrapper tr').each(function() {
            $(this).on('click', function() {
                _this._onRowClick(this);
            });
            $(this).on('dblclick',function() {
                _this._dblClickCallback(_this._getIdFromRow(this));
            });
        });
    }

    this._pollDataFromSource = function(params) {
        return $.ajax({
            type: 'GET',
            url: _this._dataSrcUrl+'?'+$.param(params, true),
            dataType: 'html'
        });
    }

    this._constructDataParams = function() {
        var _jsonToReturn = {};
        _jsonToReturn['partial'] = true;
        if(_this._orderedByHeader && _this._orderedByHeader.isOrdered()) {
            _jsonToReturn['orderbyfield'] = _this._orderedByHeader._getFieldId();
            _jsonToReturn['orderbydirection'] = _this._orderedByHeader.getOrderingState();
        }
        if(_this._filterstr && _this._filterstr!=='') {
            _jsonToReturn['filterstr'] = _this._filterstr;
        }
        _jsonToReturn['page'] = _this._paginator.getPage();
        _jsonToReturn['limit'] = _this._paginator.getLimit();
        return _jsonToReturn;
    }

    this._onHeaderClick = function(sourceHeader) {
        _this._orderedByHeader = sourceHeader;
        _this._headers.forEach(function(header) {
            if(sourceHeader !== header) {
                header.resetOrdering();
            }
        });
        _this._updateData(true);
    }

    this._onCustomButtonClick = function(button) {
        var _target = $(button).attr('data-targetSrc');
        if(_target) {
            window.location = _target;
        }
    }

    this._onRowClick = function(row) {
        if(_this._activeRow && _this._activeRow === row) {
            $(_this._activeRow).removeClass('selected');
            _this._activeRow = null;
        }else {
            if(_this._activeRow) {
                $(_this._activeRow).removeClass('selected');
            }
            $(row).addClass('selected');
            _this._activeRow = row;
        }
        _this._updateVisibilityOfCustomActionButton();
    }

    this._updateVisibilityOfCustomActionButton = function() {
        if(_this._activeRow) {
            $(_this._customActionWithSelectionButton).prop("disabled",false);
        }else {
            $(_this._customActionWithSelectionButton).prop("disabled",true);
        }
    }

    this._onFilterControlActivation = function(filterStr) {
        _this._filterstr = filterStr;
        _this._paginator.reset();
        _this._updateData(true);
    }

    this._onPaginationActivity = function() {
        _this._updateData(false);
    }

    this._getIdFromRow = function(row) {
        return _this._removeRowPrefix($(row).attr('data-rowid'));
    }

    this._removeRowPrefix = function(prefixedId) {
        var _prefixLength = (_this._gridId+'_').length;
        return prefixedId.substr(_prefixLength, prefixedId.length);
    }

    _this._init(outline, gridId, dblClickCallback);
}

function DataGridHeader(uiElement, fieldId, callback) {
    if(this == global) {return new DataGrid(arguments);}

    var _this = this,
        _orderingStates = {
            asc : 'ASC',
            desc : 'DESC'
        };

    this._fieldId = null;
    this._callback = null;
    this._uiElement = null;

    this._init = function() {
        this._fieldId = fieldId;
        this._callback = callback;
        uiElement.on('click',_this._onHeaderClick);
        this._uiElement = uiElement;
    }

    this._orderingState = null;

    this.resetOrdering = function() {
        _this._orderingState = null;
    }

    this._getFieldId = function() {
        return _this._fieldId;
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
        if(!_this._orderingState) {
            _this._orderingState = _orderingStates.asc;
        }else if(_this._orderingState === _orderingStates.asc) {
            _this._orderingState = _orderingStates.desc;
        }else {
            _this._orderingState = null;
        }
    }

    this._init();
}

function DataGridPaginator() {
    if(this == global) {return new DataGridPagination(arguments);}
    var _this = this;

    this._page = null;
    this._limit = null;

    this._callback = null;
    this._totalAmountOfPages = null;

    this._controlOutline = null;

    this._firstButton = null;
    this._previousButton = null;
    this._nextButton = null;
    this._lastButton = null;
    this._pageIndicator = null;


    this.init = function(outline, defaultLimit, notifyCallback) {
        _this._page = 1;
        _this._limit = defaultLimit;
        _this._callback = notifyCallback;
        _this._initControls(outline);
    }

    this.getPage = function() {
        return _this._page;
    }

    this.getLimit = function() {
        return _this._limit;
    }

    this._initControls = function(outline) {
        var _controlOutline = outline.find('.paginationControl'),
            _firstButton = _controlOutline.find('.first'),
            _previousButton = _controlOutline.find('.previous'),
            _nextButton = _controlOutline.find('.next'),
            _lastButton = _controlOutline.find('.last'),
            _pageIndicator = _controlOutline.find('.pageIndication');

        _firstButton.on('click',_this._onFirstButtonClick);
        _previousButton.on('click',_this._onPreviousButtonClick);
        _nextButton.on('click',_this._onNextButtonClick);
        _lastButton.on('click',_this._onLastButtonClick);

        _this._controlOutline = _controlOutline;
        _this._firstButton = _firstButton;
        _this._previousButton = _previousButton;
        _this._nextButton = _nextButton;
        _this._lastButton = _lastButton;
        _this._pageIndicator = _pageIndicator;
    }

    this.refresh = function(totalAmountOfRows, limit) {
        if(typeof limit !== 'undefined') {
            _this._limit = limit;
        }
        _this._totalAmountOfPages = Math.ceil(totalAmountOfRows/_this._limit);
        _this._page = 1;
        _this._updatePageIndication();
        _this._toggleControlVisiability();
    }

    this.reset = function() {
        _this._page = 1;
    }

    _this._updatePageIndication = function() {
        _this._pageIndicator.html(_this._page+'/'+_this._totalAmountOfPages);
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
        _this._controlOutline.hide();
    }

    this._showControl = function() {
        _this._controlOutline.show();
    }
}