$(document).ready(function() {
    $('.jqueryDatePicker').each(function() {
        $(this).datepicker({ dateFormat: "dd.mm.yy" });
    });
    $('.customLink').each(function() {
        if($(this).hasClass('backButton')) {
            $(this).on('click',function(event) {
                event.preventDefault();
                window.history.back();
            });
        }else if($(this).hasClass('submitButton')) {
            var _targetFormId = $(this).attr('data-targetFormId');
            $(this).on('click',function(event) {
                console.log('#'+_targetFormId)
                event.preventDefault();
                $('#'+_targetFormId).submit();
            });
        }else {
            if(!$(this).hasClass('linkWithDelegatedResolve')) {
                $(this).on('click',function(event) {
                    event.preventDefault();
                    window.location = $(this).attr('data-targetSrc');
                });
            }
        }
    });
//    $('.linkButton').each(function() {
//        $(this).on('click',function(event) {
//            event.preventDefault();
//            window.location = $(this).attr('data-targetSrc');
//        })
//    });
//    $('.textLink').each(function() {
//        if(!$(this).hasClass('linkWithDelegatedResolve')) {
//            $(this).on('click',function(event) {
//                event.preventDefault();
//                window.location = $(this).attr('data-targetSrc');
//            });
//        }
//    });
});

function FormViewController(formViewId) {
   console.log(formViewId)
    var _this = this,
        _formView = $(formViewId),
        _expandedViewSection = _formView.find('.formViewExpanded'),
        _expandViewButton = _formView.find('.expandFormButton'),
        _dexpandViewButton = _formView.find('.dexpandFormButton');
    this._isDisplayed = false;

    $(_expandViewButton).on('click', function(event) {
        event.preventDefault();
        _this._toggleExpandedSection();
    });
    $(_dexpandViewButton).on('click', function(event) {
        event.preventDefault();
        _this._toggleExpandedSection();
    });
    this._toggleExpandedSection = function() {
        if(!_this._isDisplayed) {
            $(_expandedViewSection).show();
            $(_expandViewButton).hide();
            $(_dexpandViewButton).show();
            _this._isDisplayed = true;
        }else {
            $(_expandedViewSection).hide();
            $(_expandViewButton).show();
            $(_dexpandViewButton).hide();
            _this._isDisplayed = false;
        }
    }

}

function FormMonthlyAssertionController() {
    var _this = this;

    this._shiftSelectionHolder = $('.shiftSelections');
    this._shiftSelectionIndex = null;
    this._shiftSelections = new Array();

    this.init = function() {
        _this._shiftSelectionIndex = _this._shiftSelectionHolder.find('li').length;
        _this._initExistingShiftSelections();
        _this._initListeners();
    }

    this._initExistingShiftSelections = function() {
        _this._shiftSelectionHolder.find('li').each(function() {
            var _selectionId = $(this).find('.selectionId').val();
            _this._shiftSelections.push(new ShiftSelection(_selectionId, this));
        })
    }

    this._initListeners = function() {
        $('#repeadedMonthlySelection_firstWeeklySelector input').on('change', function() {
            _this._onSelectorChange(this, 'First');
        });
    }

    this._onSelectorChange = function(element, sourceOccruency) {
        if(element.checked) {
            _this._addShiftSelection(sourceOccruency, $(element).val());
        }else {
            _this._deleteShiftSelection(sourceOccruency, $(element).val());
        }
    }

    this._deleteShiftSelection = function(selectedOccurency, selectedDay) {
        var _selectionId = _this._constructSelectionId(selectedOccurency, selectedDay);
        jQuery.each(_this._shiftSelections, function(index, selection) {
            if(selection.isElement(_selectionId)) {
                selection.removeElement();
                //remove element from array
                _this._shiftSelections.splice(index,1);
                //break
                return false;
            }
        });

    }



    this._addShiftSelection = function(selectedOccurency, selectedDay) {
        var _prototype = _this._shiftSelectionHolder.data('prototype'),
            _index = _this._shiftSelectionHolder.find('li').length,
            _newSelection = _prototype.replace(/__name__/g, _index),
            _selectionDomElement = $('<li></li>').append(_newSelection);
        _this._shiftSelectionHolder.append(_selectionDomElement);
        $('#repeadedMonthlySelection_shiftSelections_'+_index+'_selectionId').val(selectedOccurency+'_'+selectedDay);
        _this._shiftSelections.push(new ShiftSelection(_this._constructSelectionId(selectedOccurency, selectedDay), _selectionDomElement));
    }

    this._constructSelectionId = function(selectedOccurency, selectedDay) {
        return selectedOccurency+'_'+selectedDay;
    }

    function ShiftSelection(selectionId, domElement) {
        var _this = this;
        this._selectionId = selectionId;
        this._domElement = domElement;

        this.isElement = function(selectionIdentifier) {
            return _this._selectionId === selectionIdentifier;
        }

        this.removeElement = function() {
            _this._domElement.remove();
        }
    }

    _this.init();
}

