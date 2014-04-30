function Lookahead() {
    var _this = this;

    this._wrapper = null;
    this._inputField = null;
    this._selectionsWrapper = null;
    this._selectionsModelContainer = null;
    this._selectionsDisplayContainer = null;
    this._selectionsModelPrototype = null;

    this._modelIdPrefix = null;

    this._dataUrl = null;

    this.init = function(lookaheadId, modelIdPrefix) {
        _this._modelIdPrefix = modelIdPrefix;
        _this._initElements(lookaheadId);
        _this._initDataSrcUrl();
        _this._initListeners();
        console.log(modelIdPrefix);
    }

    this._initElements = function(lookaheadId) {
        var _wrapper = $('.'+lookaheadId),
            _selectionsWrapper = $(_wrapper).find('.selectionsWrapper'),
            _selectionsModelContainer = $(_selectionsWrapper).find('.selectionsModelContainer'),
            _selectionsModelPrototype = $(_selectionsModelContainer).data('prototype'),
            _selectionsDisplay = $(_selectionsWrapper).find('.selectionsDisplayContainer'),
            _inputField = $(_wrapper).find('.lookaheadInput');

        _this._wrapper = _wrapper;
        _this._selectionsWrapper = _selectionsWrapper;
        _this._selectionsModelContainer = _selectionsModelContainer;
        _this._selectionsModelPrototype = _selectionsModelPrototype;
        _this._selectionsDisplayContainer = _selectionsDisplay;
        _this._inputField = _inputField;
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
            _domSelection;
        _this._pollDataFromSource(_this._constructParams()).done(function(data) {
            $(_this._selectionsModelContainer).empty();
            console.log(data.models);
            if(data.models.length>0) {
                $.each(data.models, function(index, jsonModel) {
                    _model = _this._constructNewModel(jsonModel, index);
                    _domSelection = _this._constructDomSelectionModel(_model);
                    $(_this._selectionsModelContainer).append(_domSelection);
                });
            }

        }).fail(function() {
            //ToDo Exception handling
        })
    }

    this._constructNewModel = function(jsonModel, index) {
        return new Address(jsonModel, index);
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
}

function Address(address, index) {
    var _this = this;

    this.index = index;
    this.fields = {
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


}