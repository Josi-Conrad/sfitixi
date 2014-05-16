function AddressLookahead() {
    var _this = this;

    this._wrapper = null;
    this._inputField = null;

    this._lookaheadWrapper = null;
    this._manualAddWrapper = null;

    this._selectionsWrapper = null;
    this._selectionsModelContainer = null;
    this._selectionsDisplayContainer = null;
    this._selectionsModelPrototype = null;
    this._selectionsIdContainer = null;

    this._manualAddFieldContainer = null;

    this._modelPrototype = null;
    this._modelIdPrefix = null;

    this._updateTimeout = null;
    this._selectedIndex = 1;

    this._dataUrl = null;

    this._googleMapWrapper = null;
    this._mapCanvasIsVisible = false;

    this.init = function(lookaheadId, modelIdPrefix, modelPrototype, mapCanvasId) {
        _this._modelIdPrefix = modelIdPrefix;
        _this._modelPrototype = modelPrototype;
        _this._initElements(lookaheadId);
        _this._initDataSrcUrl();
        _this._initListeners();
        _this._initGoogleMapWrapper(mapCanvasId);
        _this._switchToLookaheadState();
    }

    this._initElements = function(lookaheadId) {
        var _wrapper = $('.'+lookaheadId),
            _lookaheadWrapper = $(_wrapper).find('.lookaheadWrapper'),
            _manualAddWrapper = $(_wrapper).find('.manualAddWrapper'),
            _selectionsWrapper = $(_wrapper).find('.selectionsWrapper'),
            _selectionsModelContainer = $(_selectionsWrapper).find('.selectionsModelContainer'),
            _selectionsModelPrototype = $(_selectionsModelContainer).data('prototype'),
            _selectionsDisplay = $(_selectionsWrapper).find('.selectionsDisplayContainer'),
            _inputField = $(_wrapper).find('.lookaheadInput'),
            _selectionsIdContainer = $(_wrapper).find('.lookaheadSelectionId'),
            _manualAddFieldContainer = $(_manualAddWrapper).find('.manualAddFieldContainer');

        _this._wrapper = _wrapper;
        _this._lookaheadWrapper = _lookaheadWrapper;
        _this._manualAddWrapper = _manualAddWrapper;
        _this._selectionsWrapper = _selectionsWrapper;
        _this._selectionsModelContainer = _selectionsModelContainer;
        _this._selectionsModelPrototype = _selectionsModelPrototype;
        _this._selectionsDisplayContainer = _selectionsDisplay;
        _this._inputField = _inputField;
        _this._selectionsIdContainer = _selectionsIdContainer;
        _this._manualAddFieldContainer = _manualAddFieldContainer;
    }

    this._initDataSrcUrl = function() {
        _this._dataUrl = $(_this._wrapper).data('datasrc');
    }

    this._initListeners = function() {
        $(_this._inputField).on('keydown', function(event) {
            _this._onInputKeyDown(event);
        });
        $(_this._inputField).on('focusout', _this._onInputOut);
    }

    this._initGoogleMapWrapper = function(mapCanvasId) {
        var _wrapper =  new GoogleMapWrapper();
        _wrapper.init(mapCanvasId)
        _this._googleMapWrapper = _wrapper;
    }

    this._switchToManualAddState = function() {
        _this._resetLookahead();
        _this._constructManualAddContainer();
        $(_this._lookaheadWrapper).hide();
        $(_this._manualAddWrapper).show();
    }

    this._switchToLookaheadState = function() {
        _this._removeManualAddContainer();
        $(_this._manualAddWrapper).hide();
        $(_this._lookaheadWrapper).show();
    }


    this._updateSelections = function() {
        var _model,
            _domSelectionModel,
            _domSelectionDisplay;
        if($(_this._inputField).val().length>0) {
            _this._pollDataFromSource(_this._constructParams()).done(function(data) {
                _this._resetLookahead();
                $.each(data.models, function(index, jsonModel) {
                    _model = _this._constructNewModel(jsonModel, index);
                    _domSelectionModel = _this._constructDomSelectionModel(_model);
                    $(_this._selectionsModelContainer).append(_domSelectionModel);
                    _domSelectionDisplay =  _this._constructDomSelectionDisplay(_model);
                    $(_this._selectionsDisplayContainer).append(_domSelectionDisplay);
                });
                $(_this._selectionsDisplayContainer).append(_this._constructAddManuallyLink());
                _this._showSelectionDisplay();
            }).fail(function() {
                //ToDo Exception handling
            })
        }else {
            _this._hideSelectionDisplay();
        }
    }

    this._constructNewModel = function(jsonModel, index) {
        return new _this._modelPrototype.constructor(jsonModel, index);
    }

    this._constructDomSelectionModel = function(model) {
        var _selectionPrototype = _this._selectionsModelPrototype,
            _domSelection = $('<li></li>');
        _selectionPrototype = _selectionPrototype.replace(/__name__/g, model.index);
        _domSelection.append(_selectionPrototype);
        for(var _field in model.fields) {
            _domSelection.find('[id*='+_this._modelIdPrefix+'_'+model.index+'_'+_field+']').val(model.fields[_field]);
        }
        return _domSelection;
    }

    this._constructDomSelectionDisplay = function(model) {
        var _selectionDisplay = $('<li></li>').append(model.getDisplayName());
        _selectionDisplay.on('mousedown', function() {
            _this._selectedIndex = model.index;
            $(_this._inputField).val(model.getDisplayName());
            $(_this._selectionsIdContainer).val(model.index);
            _this._hideSelectionDisplay();
        });
        _selectionDisplay.on('mouseover', function() {
            _this._onSelectionDisplayMouseOver(model);
        })
        return _selectionDisplay;
    }

    this._constructAddManuallyLink = function() {
        var _selection = $('<li style="padding-top: 12px"></li>'),
            _link = $('<a href="#">Addresse manuell hinzufügen</a>');
        _link.on('click', function(event) {
            _this._onAddManuallyClicked(event)
        });
        _selection.append(_link);
        return _selection;
    }

    this._constructGoBackToLookaheadLink = function() {
        var _link = $('<a href="#">Zurück zur unterstützen Eingabe</a>');
        _link.on('click', function(event) {
            _this._onGoBackToLookahead(event);
        });
        return _link;
    }

    this._constructFieldsForManualAdd = function() {
        var _prototype = _this._selectionsModelPrototype;
        _prototype = _prototype.replace(/__name__/g, 0);
        return _prototype;
    }

    this._onAddManuallyClicked = function(event) {
        event.preventDefault();
        _this._switchToManualAddState();
    }

    this._onGoBackToLookahead = function(event) {
        event.preventDefault();
        _this._switchToLookaheadState();
    }

    this._onInputKeyDown = function(event) {
        //do not trigger on tab key
        if(event.keyCode!==9) {
            _this._selectedIndex = null;
            if(_this._updateTimeout) {
                clearTimeout(_this._updateTimeout);
                _this._updateTimeout = null;
            }
            _this._updateTimeout = setTimeout(function() {
                _this._updateSelections();
            },300);
        }
    }

    this._onInputOut = function() {
        setTimeout(function() {
            if(null === _this._selectedIndex) {
                $(_this._inputField).val('');
                _this._hideSelectionDisplay();
            }
        },100)
        _this._googleMapWrapper.hideCanvas();
    }

    this._onSelectionDisplayMouseOver = function(model) {
        _this._googleMapWrapper.showAddress(model.fields.lat, model.fields.lng);
    }

    this._resetLookahead = function() {
        $(_this._selectionsModelContainer).empty();
        $(_this._selectionsDisplayContainer).empty();
        _this._selectedIndex = null;
    }

    this._constructManualAddContainer = function() {
        var _fields = _this._constructFieldsForManualAdd(),
            _backLink = $('<div class="row"></div>').append(_this._constructGoBackToLookaheadLink());
        _this._removeManualAddContainer();
        _this._manualAddFieldContainer.append(_fields);
        _this._manualAddFieldContainer.append(_backLink);
    }

    this._removeManualAddContainer = function() {
        $(_this._manualAddFieldContainer).empty();
    }

    this._constructParams = function() {
        var _jsonToReturn = {};
        _jsonToReturn['searchstr'] = $(_this._inputField).val();
        return _jsonToReturn;
    }

    this._pollDataFromSource = function (params) {
        return $.ajax({
            type: 'GET',
            url: _this._dataUrl + '?' + $.param(params, true),
            dataType: 'json'
        });
    }

    this._showSelectionDisplay = function() {
        $(_this._selectionsDisplayContainer).show();
    }

    this._hideSelectionDisplay = function() {
        $(_this._selectionsDisplayContainer).hide();
    }
}

function LookaheadModel(modelJson, index) {
    var _this = this;

    _this.index = index;
    _this.fields = {};
    _this.getDisplayName = function() {};

}

function Address(address, index) {
    var _this = this;

    LookaheadModel.apply(this,arguments);

    _this.fields = {
        id : address.id,
        name : address.name,
        street : address.street,
        postalCode : address.postalCode,
        city : address.city,
        country : address.country,
        lat : address.lat,
        lng : address.lng,
        source : address.source
    };

    _this.getDisplayName = function() {
        return _this.fields.name ? _this.fields.name :  _this._constructAlternativeDisplayName();
    }

    _this._constructAlternativeDisplayName = function() {
        return _this.fields.street+', '+_this.fields.postalCode+' '+_this.fields.city+', '+_this.fields.country;

    }
}