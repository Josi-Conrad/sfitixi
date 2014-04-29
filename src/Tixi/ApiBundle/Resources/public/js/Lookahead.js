function Lookahead() {
    var _this = this;

    this._wrapper = null;
    this._inputField = null;
    this._selectionsWrapper = null;
    this._selections = null;

    this._dataUrl = null;

    this.init = function(lookaheadId) {
        _this._initElements(lookaheadId);
        _this._initDataSrcUrl();
        _this._initListeners();
    }

    this._initElements = function(lookaheadId) {
        var _wrapper = $('.'+lookaheadId),
            _selectionsWrapper = $(_wrapper).find('.selectionsWrapper'),
            _selections = $(_selectionsWrapper).find('.selections'),
            _inputField = $(_wrapper).find('.lookaheadInput');
        _this._wrapper = _wrapper;
        _this._selectionsWrapper = _selectionsWrapper;
        _this._selections = _selections;
        _this._inputField = _inputField;
    }

    this._initDataSrcUrl = function() {
        console.log($(_this._wrapper).data('datasrc'));
        _this._dataUrl = $(_this._wrapper).data('datasrc');
    }

    this._initListeners = function() {
        $(_this._inputField).on('keyup', function() {
            if($(this).val().length>0) {
                _this._updateSelections()
            }
        })
    }

    this._updateSelections = function() {
        _this._pollDataFromSource(_this._constructParams()).done(function(data) {
            console.log(data);
        }).fail(function() {
            //ToDo Exception handling
        })
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