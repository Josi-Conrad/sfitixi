function AddressLookahead() {
    var _this = this;

    this.MANUALADDSTATE = 1;
    this.LOOKAHEADSTATE = 2;

    this._state = null;

    this._wrapper = null;
    this._inputField = null;

    this._lookaheadWrapper = null;

    this._addressSelectionsWrapper = null;
    this._addressSelectionsContainer = null;
    this._addressContainerWrapper = null;
    this._addressContainer = null;

    this._selectedAddress = null;

    this._dataUrl = null;

    this._googleMapWrapper = null;

    this._updateTimeout = null;
    this._cancelManualAddLink = null;

    this.init = function(lookaheadId) {
        _this._initElements(lookaheadId);
        _this._initDataSrcUrl();
        _this._initListeners();
        _this._initGoogleMapWrapper();
        _this._initSelectedAddress();
        _this._switchToLookaheadState();
    }

    this._initElements = function(lookaheadId) {
        var _wrapper = $('.'+lookaheadId),
            _lookaheadWrapper = $(_wrapper).find('.lookaheadWrapper'),
            _manualAddWrapper = $(_wrapper).find('.manualAddWrapper'),
            _addressSelectionsWrapper = $(_wrapper).find('.addressSelectionsWrapper'),
            _addressSelectionsContainer = $(_addressSelectionsWrapper).find('.addressSelectionsContainer'),
            _addressContainerWrapper = $(_wrapper).find('.addressContainerWrapper'),
            _addressContainer = $(_addressContainerWrapper).find('.addressContainer'),
            _cancelManualAddLink = $(_addressContainerWrapper).find('.cancelManualAdd'),
            _inputField = $(_wrapper).find('.lookaheadInput');

        _this._wrapper = _wrapper;
        _this._lookaheadWrapper = _lookaheadWrapper;
        _this._manualAddWrapper = _manualAddWrapper;
        _this._addressSelectionsWrapper = _addressSelectionsWrapper;
        _this._addressSelectionsContainer = _addressSelectionsContainer;
        _this._addressContainerWrapper = _addressContainerWrapper;
        _this._addressContainer = _addressContainer;
        _this._cancelManualAddLink = _cancelManualAddLink;
        _this._inputField = _inputField;
    }

    this._initDataSrcUrl = function() {
        _this._dataUrl = $(_this._wrapper).data('datasrc');
    }

    this._initListeners = function() {
        $(_this._inputField).on('keydown', function(event) {
            _this._onInputKeyDown(event);
        });
        $(_this._inputField).on('focusout', _this._onInputOut);
        $(_this._cancelManualAddLink).on('click', _this._onGoBackToLookahead);
    }

    this._initGoogleMapWrapper = function(mapCanvasId) {
        var _wrapper =  new GoogleMapWrapper(),
            _googleMapCanvasWrapper = $(_this._wrapper).find('.googleMapCanvasWrapper'),
            _googleMapCanvas = $(_googleMapCanvasWrapper).find('.googleMapCanvas').get(0);
        _wrapper.init(_googleMapCanvasWrapper, _googleMapCanvas);
        _this._googleMapWrapper = _wrapper;
    }

    this._initSelectedAddress = function() {
        if(_this._getAddressFieldValue('id') !== '') {
            _this._selectedAddress = _this._createAddressFromAddressContainerValues();
        }
    }

    this._switchToManualAddState = function() {
        _this._resetAddressContainer();
        _this._resetInputField();
        $(_this._lookaheadWrapper).hide();
        $(_this._addressContainerWrapper).show();
        _this._googleMapWrapper.displayGeocodeEditableCanvas(_this._onGoogleMapMarkerDrop);
        _this._state = _this.MANUALADDSTATE;
    }

    this._switchToLookaheadState = function() {
        $(_this._addressContainerWrapper).hide();
        $(_this._lookaheadWrapper).show();
        _this._state = _this.LOOKAHEADSTATE;
    }

    this._showAddressSelections = function() {
        $(_this._addressSelectionsWrapper).show();
    }

    this._hideAddressSelections = function() {
        $(_this._addressSelectionsWrapper).hide();
    }

    this._updateAddressSelections = function() {
        var _model,
            _domSelectionDisplay;
        if($(_this._inputField).val().length>0) {
            _this._pollDataFromSource(_this._constructParams()).done(function(data) {
                _this._resetLookahead();
                $.each(data.models, function(index, jsonModel) {
                    _model = new Address(jsonModel, index);
                    _domSelectionDisplay =  _this._constructAddressSelections(_model);
                    $(_this._addressSelectionsContainer).append(_domSelectionDisplay);
                });
                $(_this._addressSelectionsContainer).append(_this._constructAddManuallyLink());
                _this._showAddressSelections();
            }).fail(function() {
                //ToDo Exception handling
            })
        }else {
            _this._hideAddressSelections();
        }
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

    this._updateAddressContainer = function(model) {
        console.log('container updated')
        for(var _field in model.fields) {
            _this._setAddressFieldValue(_field, model.fields[_field]);
        }
    }

    this._resetAddressContainer = function() {
        var _dummyAddress = new Address();
        _this._selectedAddress = null;
        for(var _field in _dummyAddress.fields) {
            console.log(_field)
            _this._setAddressFieldValue(_field, '');
        }
    }

    this._resetInputField = function() {
        $(_this._inputField).val('');
    }

    this._constructAddressSelections = function(model) {
        var _selectionDisplay = $('<li class="lookaheadSelection"></li>').append(model.getDisplayName());
        _selectionDisplay.on('mousedown', function() {
            _this._onAddressSelectionClick(model);
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

    this._createAddressFromAddressContainerValues = function() {
        var _dummyAddress = new Address(),
            _address = {},
            _field;
        for(_field in _dummyAddress.fields) {
            _address[_field] = _this._getAddressFieldValue(_field);
        }
        return new Address(_address, 0);
    }

    this._fillLatLng = function(lat, lng) {
        _this._setAddressFieldValue('lat', lat);
        _this._setAddressFieldValue('lng', lng);
    }

    this._setAddressFieldValue = function(fieldName, value) {
        $(_this._addressContainer).find('[id*='+fieldName+']').val(value);
    }

    this._getAddressFieldValue = function(fieldName) {
        return $(_this._addressContainer).find('[id*='+fieldName+']').val();
    }

    this._onAddressSelectionClick = function(model) {
        _this._selectedAddress = model;
        $(_this._inputField).val(model.getDisplayName());
        _this._updateAddressContainer(model);
        _this._hideAddressSelections();
    }

    this._onAddManuallyClicked = function(event) {
        event.preventDefault();
        _this._switchToManualAddState();
    }

    this._onGoBackToLookahead = function(event) {
        event.preventDefault();
        _this._googleMapWrapper.hideCanvas();
        _this._switchToLookaheadState();
    }

    this._onInputKeyDown = function(event) {
        //do not trigger on tab key
        if(event.keyCode!==9) {
            _this._selectedIndex = null;
            if(_this._updateTimeout) {
                clearTimeout(_this._updateTimeout);
            }
            _this._updateTimeout = setTimeout(function() {
                _this._updateAddressSelections();
            },300);
        }
    }

    this._onInputOut = function() {
        if($(_this._inputField).val()==='' || _this._selectedAddress === null) {
            _this._selectedAddress = null;
        }else {
            $(_this._inputField).val(_this._selectedAddress.getDisplayName());
        }
        setTimeout(function() {
            _this._hideAddressSelections();
        },200);

        _this._googleMapWrapper.hideCanvas();
    }

    this._onSelectionDisplayMouseOver = function(model) {
        _this._googleMapWrapper.displayAddress(model.fields.lat, model.fields.lng);
    }

    this._onGoogleMapMarkerDrop = function(lat, lng) {
        _this._fillLatLng(lat, lng);
    }

    this._resetLookahead = function() {
        $(_this._addressSelectionsContainer).empty();
    }

}



function Address(address, index) {
    var _this = this;

    _this.index = (undefined !== index) ? index : 0;
    _this.fields = {
        id : (undefined !== address) ? address.id : '',
        name : (undefined !== address) ? address.name : '',
        street : (undefined !== address) ? address.street : '',
        postalCode : (undefined !== address) ? address.postalCode : '',
        city : (undefined !== address) ? address.city : '',
        country : (undefined !== address) ? address.country : '',
        lat : (undefined !== address) ? address.lat : '',
        lng : (undefined !== address) ? address.lng : '',
        source : (undefined !== address) ? address.source : ''
    };

    _this.getDisplayName = function() {
        return _this.fields.name ? _this.fields.name :  _this._constructAlternativeDisplayName();
    }

    _this._constructAlternativeDisplayName = function() {
        return _this.fields.street+', '+_this.fields.postalCode+' '+_this.fields.city+', '+_this.fields.country;
    }
}