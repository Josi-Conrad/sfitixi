function AddressLookahead() {
    var _this = this;

    this.MANUALADDSTATE = 1;
    this.LOOKAHEADSTATE = 2;

    this.SEARCHREQUESTSTATE = 'search_state';
    this.USERADDRESSREQUESTSTATE = 'user_state';

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
    this._saveManualAddLink = null;
    this._editManuallyLink = null;

    this.init = function(lookaheadId, passengerId) {
        _this._initElements(lookaheadId);
        _this._initDataSrcUrl();
        _this._initListeners();
        _this._initPassengerHomeAddressLink(passengerId);
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
            _saveManualAddLink = $(_addressContainerWrapper).find('.saveManualAdd'),
            _inputField = $(_wrapper).find('.lookaheadInput'),
            _editManuallyLink = $(_lookaheadWrapper).find('.editManually');

        _this._wrapper = _wrapper;
        _this._lookaheadWrapper = _lookaheadWrapper;
        _this._manualAddWrapper = _manualAddWrapper;
        _this._addressSelectionsWrapper = _addressSelectionsWrapper;
        _this._addressSelectionsContainer = _addressSelectionsContainer;
        _this._addressContainerWrapper = _addressContainerWrapper;
        _this._addressContainer = _addressContainer;
        _this._cancelManualAddLink = _cancelManualAddLink;
        _this._saveManualAddLink = _saveManualAddLink;
        _this._inputField = _inputField;
        _this._editManuallyLink = _editManuallyLink;
    };

    this._initDataSrcUrl = function() {
        _this._dataUrl = $(_this._wrapper).data('datasrc');
    };

    this._initListeners = function() {
        $(_this._inputField).on('keydown', function(event) {
            _this._onInputKeyDown(event);
        });
        $(_this._inputField).on('focusin', _this._onInputIn);
        $(_this._inputField).on('focusout', _this._onInputOut);
        $(_this._cancelManualAddLink).on('click', _this._onCancelManualAddClicked);
        $(_this._saveManualAddLink).on('click', _this._onSaveManualAddClicked);
        $(_this._editManuallyLink).on('click', _this._onEditManuallyClicked);
    };

    this._initPassengerHomeAddressLink = function(passengerId) {
        var _passengerId = (undefined !== passengerId) ? passengerId : null,
            _userHomeLink;
        if(_passengerId !== null) {
            _userHomeLink = $(_this._wrapper).find('.userHomeLinkWrapper');
            $(_userHomeLink).on('click', function() {
                _this._onUserHomeLinkClick(passengerId);
            });
            $(_userHomeLink).show();
        }
    };

    this._initGoogleMapWrapper = function(mapCanvasId) {
        var _wrapper =  new GoogleMapWrapper(),
            _googleMapCanvasWrapper = $(_this._wrapper).find('.googleMapCanvasWrapper'),
            _googleMapCanvas = $(_googleMapCanvasWrapper).find('.googleMapCanvas').get(0);
        _wrapper.init(_googleMapCanvasWrapper, _googleMapCanvas);
        _this._googleMapWrapper = _wrapper;
    };

    this._initSelectedAddress = function() {
        if(_this._getAddressFieldValue('id') !== '') {
            _this._setSelectedAddress(_this._createAddressFromAddressContainerValues());
        }
    };

    this.getAddress = function() {
        return _this._selectedAddress;
    };

    this.getAddressCity = function() {
        var _city = null;
        if(_this._selectedAddress) {
            _city = _this._getAddressFieldValue('city');
        }
        return _city;
    };


    this._switchToManualAddState = function(edit) {
        var _edit = (undefined !== edit);
        if(!_edit) {
            _this._resetAddressContainer();
            _this._resetInputField();
        }
        $(_this._lookaheadWrapper).hide();
        $(_this._addressContainerWrapper).show();
        if(edit) {
            _this._googleMapWrapper.displayEditableAddress(
                _this._selectedAddress.fields.lat, _this._selectedAddress.fields.lng, _this._onGoogleMapMarkerDrop);
        }else {
            _this._googleMapWrapper.displayGeocodeEditableCanvas(_this._onGoogleMapMarkerDrop);
        }
        _this._state = _this.MANUALADDSTATE;
    };

    this._switchToLookaheadState = function() {
        $(_this._addressContainerWrapper).hide();
        $(_this._lookaheadWrapper).show();
        _this._state = _this.LOOKAHEADSTATE;
    };

    this._showAddressSelections = function() {
        $(_this._editManuallyLink).hide();
        $(_this._addressSelectionsWrapper).show();
    };

    this._hideAddressSelections = function() {
        $(_this._addressSelectionsWrapper).hide();
    };

    this._updateAddressSelections = function() {
        var _model,
            _domSelectionDisplay;
        if($(_this._inputField).val().length>0) {
            _this._pollDataFromSource(_this._constructParamsForSearch()).done(function(data) {
                _this._resetLookahead();
                $.each(data.models, function(index, jsonModel) {
                    _model = new Address(jsonModel, index);
                    _domSelectionDisplay =  _this._constructAddressSelections(_model);
                    $(_this._addressSelectionsContainer).append(_domSelectionDisplay);
                });
                $(_this._addressSelectionsContainer).append(_this._constructAddManuallyLink());
                _this._showAddressSelections();
            }).fail(function() {
                //fail silently
            })
        }else {
            _this._hideAddressSelections();
        }
    };

    this._updateAddressWithUserHomeAddress = function(passengerId) {
        var _model;
        _this._pollDataFromSource(_this._constructParamsForUserHomeAddress(passengerId)).done(function(data) {
            if(data.models[0]) {
                _model = new Address(data.models[0], 0);
                _this._onAddressSelectionClick(_model);
            }
        }).fail(function() {
            //fail silently
        });
    };

    this._constructParamsForSearch = function() {
        var _jsonToReturn = {};
        _jsonToReturn['requeststate'] = _this.SEARCHREQUESTSTATE;
        _jsonToReturn['searchstr'] = $(_this._inputField).val();
        return _jsonToReturn;
    };

    this._constructParamsForUserHomeAddress = function(passengerId) {
        var _jsonToReturn = {};
        _jsonToReturn['requeststate'] = _this.USERADDRESSREQUESTSTATE;
        _jsonToReturn['passengerid'] = passengerId;
        return _jsonToReturn;
    };

    this._pollDataFromSource = function (params) {
        return $.ajax({
            type: 'GET',
            url: _this._dataUrl + '?' + $.param(params, true),
            dataType: 'json'
        });
    };

    this._updateAddressContainer = function(model) {
        for(var _field in model.fields) {
            _this._setAddressFieldValue(_field, model.fields[_field]);
        }
    };

    this._resetAddressContainer = function() {
        var _dummyAddress = new Address();
        _this._setSelectedAddress(null);
        for(var _field in _dummyAddress.fields) {
            _this._setAddressFieldValue(_field, '');
        }
    };

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
    };

    this._constructAddManuallyLink = function() {
        var _selection = $('<li style="padding-top: 12px"></li>'),
            _link = $('<a href="#">Addresse manuell hinzufügen</a>');
        _link.on('click', function(event) {
            _this._onAddManuallyClicked(event)
        });
        _selection.append(_link);
        return _selection;
    };

    this._createAddressFromAddressContainerValues = function() {
        var _dummyAddress = new Address(),
            _address = {},
            _field;
        for(_field in _dummyAddress.fields) {
            _address[_field] = _this._getAddressFieldValue(_field);
        }
        _address['displayName'] = $(_this._inputField).val();
        return new Address(_address, 0);
    };

    this._fillLatLng = function(lat, lng) {
        _this._setAddressFieldValue('lat', lat);
        _this._setAddressFieldValue('lng', lng);
    };

    this._setAddressFieldValue = function(fieldName, value) {
        $(_this._addressContainer).find('[id*='+fieldName+']').val(value);
    };

    this._getAddressFieldValue = function(fieldName) {
        return $(_this._addressContainer).find('[id*='+fieldName+']').val();
    };

    this._setSelectedAddress = function(model) {
        _this._selectedAddress = model;
        $('body').trigger('addressChanged',[_this]);
    };

    this._onAddressSelectionClick = function(model) {
        $(_this._inputField).val(model.getDisplayName());
        _this._updateAddressContainer(model);
        _this._setSelectedAddress(model);
        _this._hideAddressSelections();
    };

    this._onUserHomeLinkClick = function(passengerId) {
        _this._updateAddressWithUserHomeAddress(passengerId);
    };

    this._onEditManuallyClicked = function(event) {
        event.preventDefault();
        _this._switchToManualAddState(true);
    };

    this._onAddManuallyClicked = function(event) {
        event.preventDefault();
        _this._switchToManualAddState();
    };

    this._onCancelManualAddClicked = function(event) {
        event.preventDefault();
        if(_this._selectedAddress !== null) {
            _this._updateAddressContainer(_this._selectedAddress);
        }
        _this._googleMapWrapper.hideCanvas();
        _this._switchToLookaheadState();
    };

    this._onSaveManualAddClicked = function(event) {
        event.preventDefault();
        _this._setSelectedAddress(_this._createAddressFromAddressContainerValues());
        $(_this._inputField).val(_this._selectedAddress.getDisplayName());
        _this._googleMapWrapper.hideCanvas();
        _this._switchToLookaheadState();
    };

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
    };

    this._onInputIn = function() {
        if(_this._selectedAddress) {
            _this._googleMapWrapper.displayAddress(
                _this._selectedAddress.fields.lat, _this._selectedAddress.fields.lng, true);
            $(_this._editManuallyLink).show();
        }
    };

    this._onInputOut = function() {
        setTimeout(function() {
            if($(_this._inputField).val()==='' || _this._selectedAddress === null) {
                $(_this._inputField).val('');
                _this._setSelectedAddress(null);
            }else {
                $(_this._inputField).val(_this._selectedAddress.getDisplayName());
            }
            _this._hideAddressSelections();
            $(_this._editManuallyLink).hide();
        },100);
        _this._googleMapWrapper.hideCanvas();
    };

    this._onSelectionDisplayMouseOver = function(model) {
        _this._googleMapWrapper.displayAddress(model.fields.lat, model.fields.lng);
    };

    this._onGoogleMapMarkerDrop = function(lat, lng) {
        _this._fillLatLng(lat, lng);
    };

    this._resetLookahead = function() {
        $(_this._addressSelectionsContainer).empty();
    };
}

function Address(address, index) {
    var _this = this;

    _this.index = (undefined !== index) ? index : 0;
    _this.fields = {
        id : (undefined !== address) ? address.id : '',
        displayName : (undefined !== address) ? address.displayName : '',
        street : (undefined !== address) ? address.street : '',
        postalCode : (undefined !== address) ? address.postalCode : '',
        city : (undefined !== address) ? address.city : '',
        country : (undefined !== address) ? address.country : '',
        lat : (undefined !== address) ? address.lat : '',
        lng : (undefined !== address) ? address.lng : '',
        source : (undefined !== address) ? address.source : ''
    };

    _this.getDisplayName = function() {
        return _this.fields.displayName ? _this.fields.displayName :  _this._constructAlternativeDisplayName();
    };

    _this.getCoordinates = function() {
        var _jsonToReturn = {};
        _jsonToReturn['lat'] = _this.fields.lat;
        _jsonToReturn['lng'] = _this.fields.lng;
        return _jsonToReturn;
    };

    _this._constructAlternativeDisplayName = function() {
        return _this.fields.street+', '+_this.fields.postalCode+' '+_this.fields.city+', '+_this.fields.country;
    };
}