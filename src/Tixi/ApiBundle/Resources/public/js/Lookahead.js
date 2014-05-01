function Lookahead() {
    var _this = this;

    this._wrapper = null;
    this._inputField = null;
    this._selectionsWrapper = null;
    this._selectionsModelContainer = null;
    this._selectionsDisplayContainer = null;
    this._selectionsModelPrototype = null;
    this._selectionsIdContainer = null;

    this._modelPrototype = null;
    this._modelIdPrefix = null;

    this._dataUrl = null;

    this.init = function(lookaheadId, modelIdPrefix, modelPrototype) {
        _this._modelIdPrefix = modelIdPrefix;
        _this._modelPrototype = modelPrototype;
        _this._initElements(lookaheadId);
        _this._initDataSrcUrl();
        _this._initListeners();
    }

    this._initElements = function(lookaheadId) {
        var _wrapper = $('.'+lookaheadId),
            _selectionsWrapper = $(_wrapper).find('.selectionsWrapper'),
            _selectionsModelContainer = $(_selectionsWrapper).find('.selectionsModelContainer'),
            _selectionsModelPrototype = $(_selectionsModelContainer).data('prototype'),
            _selectionsDisplay = $(_selectionsWrapper).find('.selectionsDisplayContainer'),
            _inputField = $(_wrapper).find('.lookaheadInput'),
            _selectionsIdContainer = $(_wrapper).find('.lookaheadSelectionId');

        _this._wrapper = _wrapper;
        _this._selectionsWrapper = _selectionsWrapper;
        _this._selectionsModelContainer = _selectionsModelContainer;
        _this._selectionsModelPrototype = _selectionsModelPrototype;
        _this._selectionsDisplayContainer = _selectionsDisplay;
        _this._inputField = _inputField;
        _this._selectionsIdContainer = _selectionsIdContainer;
    }

    this._initDataSrcUrl = function() {
        _this._dataUrl = $(_this._wrapper).data('datasrc');
    }

    this._initListeners = function() {
        var _updateTimeout = null;
        $(_this._inputField).on('keyup', function() {
            if(_updateTimeout) {
                clearTimeout(_updateTimeout);
            }
            _updateTimeout = setTimeout(_this._updateSelections, 300);
        })
    }

    this._updateSelections = function() {
        var _model,
            _domSelectionModel,
            _domSelectionDisplay;
        _this._pollDataFromSource(_this._constructParams()).done(function(data) {
            $(_this._selectionsModelContainer).empty();
            $(_this._selectionsDisplayContainer).empty();
            if(data.models.length>0) {
                if($(_this._inputField).val().length>0) {
                    $.each(data.models, function(index, jsonModel) {
                        _model = _this._constructNewModel(jsonModel, index);
                        console.log(_model.getDisplayName());
                        _domSelectionModel = _this._constructDomSelectionModel(_model);
                        $(_this._selectionsModelContainer).append(_domSelectionModel);
                        _domSelectionDisplay = _this._constructDomSelectionDisplay(_model);
                        console.log(_domSelectionDisplay);
                        $(_this._selectionsDisplayContainer).append(_domSelectionDisplay);
                    });
                    _this._showSelectionDisplay();
                }else {
                    _this._hideSelectionDisplay();
                }
            }

        }).fail(function() {
            //ToDo Exception handling
        })
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
        _selectionDisplay.on('click', function() {
            $(_this._inputField).val(model.getDisplayName());
            $(_this._selectionsIdContainer).val(model.index);
            _this._hideSelectionDisplay();
        });
        return _selectionDisplay;
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