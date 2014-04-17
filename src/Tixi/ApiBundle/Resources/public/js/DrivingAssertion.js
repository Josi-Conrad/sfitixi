function RepeatedDrivingAssertion() {
    var _this = this;

    this._weeklyAssertionController = null;
    this._monthlyAssertionController = null;

    this._weeklyView = null;
    this._monthlyView = null;

    this.init = function(trans, frequency, formId) {
        _this._weeklyView = $('.weeklyPart');
        _this._monthlyView = $('.monthlyPart');

        _this._weeklyAssertionController = new ShiftSelectionController();
        _this._weeklyAssertionController.init('weeklyShiftSelections', trans, formId);
        _this._monthlyAssertionController = new ShiftSelectionController();
        _this._monthlyAssertionController.init('monthlyShiftSelections', trans, formId);
        _this._initListeners();
        if(frequency) {_this._toggleFrequency(frequency);}
    }

    this._initListeners = function() {
        $('.repeatedDrivingAssertionFrequencyControl').on('change', function() {
            _this._toggleFrequency($(this).val());
        });
        _this._initWeeklyListeners();
        _this._initMonthlyListeners();
    }

    this._initWeeklyListeners = function() {
        $('.weeklyDaySelection input').on('change', function() {
            _this._weeklyAssertionController.onSelectorChange(this, 'day');
        });
    }

    this._initMonthlyListeners = function() {
        $('#fpw_driver_repeatedassertions_monthlyFirstWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'first');
        });
        $('#fpw_driver_repeatedassertions_monthlySecondWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'second');
        });
        $('#fpw_driver_repeatedassertions_monthlyThirdWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'third');
        });
        $('#fpw_driver_repeatedassertions_monthlyFourthWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'fourth');
        });
        $('#fpw_driver_repeatedassertions_monthlyLastWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'last');
        });
    }

    this._toggleFrequency = function(frequency) {
        if(frequency === 'weekly') {
            _this._monthlyView.hide();
            _this._weeklyView.show();
        }else {
            _this._weeklyView.hide();
            _this._monthlyView.show();
        }
    }

}

function ShiftSelectionController() {
    var _this = this;

    this._shiftSelectionHolder = null;
    this._shiftSelections = null;

    this._selectionIdentifier = null;
    this._trans = null;

    this._formId = null;

    this.init = function(selectionIdentifier, trans, formId) {
        _this._trans = trans;
        _this._selectionIdentifier = selectionIdentifier;
        _this._formId = formId;
        _this._shiftSelectionHolder = $('.'+selectionIdentifier);
        _this._shiftSelectionIndex = _this._shiftSelectionHolder.find('li').length;
        _this._shiftSelections = new Array();
        _this._initExistingShiftSelections();
    }

    this._initExistingShiftSelections = function() {
        _this._shiftSelectionHolder.find('li').each(function() {
            var _selectionId = $(this).find('.selectionId').val(),
                _explodedSelectionId = _this._deconstructSelectionId(_selectionId),
                _labelText = _this._constructSelectionText(
                    _explodedSelectionId.selectedOccurency,
                    _explodedSelectionId.selectedDay
                );
            $(this).find('label').first().html(_labelText);
            _this._shiftSelections.push(new ShiftSelection(_selectionId, this));
        })
    }

    this.onSelectorChange = function(element, sourceOccruency) {
        var _selectionText = _this._constructSelectionText(sourceOccruency, $(element).val());
        if(element.checked) {
            _this._addShiftSelection(sourceOccruency, $(element).val(), _selectionText);
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

    this._addShiftSelection = function(selectedOccurency, selectedDay, selectionText) {
        var _prototype = _this._shiftSelectionHolder.data('prototype'),
            _index = _this._shiftSelectionHolder.find('li').length,
            _newSelection = _prototype.replace(/__name__/g, _index),
            _newSelection = _newSelection.replace(/__label__/g, selectionText),
            _selectionDomElement = $('<li></li>').append(_newSelection);
        _this._shiftSelectionHolder.append(_selectionDomElement);
        $('#'+_this._formId+'_'+_this._selectionIdentifier+'_'+_index+'_selectionId').val(_this._constructSelectionId(selectedOccurency,selectedDay));
        _this._shiftSelections.push(new ShiftSelection(_this._constructSelectionId(selectedOccurency, selectedDay), _selectionDomElement));
    }

    this._constructSelectionId = function(selectedOccurency, selectedDay) {
        return selectedOccurency+'_'+selectedDay;
    }

    this._deconstructSelectionId = function(selectionId) {
        var _exploded = selectionId.split('_');
        return {
            'selectedOccurency':_exploded[0],
            'selectedDay': _exploded[1]
        }
    }

    this._constructSelectionText = function(selectedOccurency, selectedDay) {
        var _labelText;
        if(selectedOccurency==='day') {
            _labelText = _this._trans[selectedDay]+' ('+_this._trans['repeateddrivingmission.everyweek.text']+')';
        }else if(selectedOccurency==='first') {
            _labelText = _this._trans[selectedDay]+'<br/> ('+_this._trans['firstweek.name']+')';
        }else if(selectedOccurency==='second') {
            _labelText = _this._trans[selectedDay]+'<br/> ('+_this._trans['secondweek.name']+')';
        }else if(selectedOccurency==='third') {
            _labelText = _this._trans[selectedDay]+'<br/> ('+_this._trans['thirdweek.name']+')';
        }else if(selectedOccurency==='fourth') {
            _labelText = _this._trans[selectedDay]+'<br/> ('+_this._trans['fourthweek.name']+')';
        }else if(selectedOccurency==='last') {
            _labelText = _this._trans[selectedDay]+'<br/> ('+_this._trans['lastweek.name']+')';
        }
        return _labelText;
    }
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