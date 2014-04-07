function RepeatedDrivingAssertion() {
    var _this = this;

    this._weeklyAssertionController = null;
    this._monthlyAssertionController = null;

    this._weeklyView = null;
    this._monthlyView = null;


    this.init = function() {
        _this._weeklyView = $('.weeklyPart');
        _this._monthlyView = $('.monthlyPart');

        _this._weeklyAssertionController = new ShiftSelectionController('weeklyShiftSelections');
        _this._weeklyAssertionController.init();
        _this._monthlyAssertionController = new ShiftSelectionController('monthlyShiftSelections');
        _this._monthlyAssertionController.init();

        _this._initListeners();
    }

    this._initListeners = function() {
        $('.repeatedDrivingAssertionFrequencyControl').on('change', function() {
            _this._toggleFrequency($(this).val());
        });
        _this._initWeeklyListeners();
        _this._initMonthlyListeners();
    }

    this._initWeeklyListeners = function() {
        var _labelText;
        $('.weeklyDaySelection input').on('change', function() {
            _labelText = $(this).parent().find('label').text()+' (jeden)';
            _this._weeklyAssertionController.onSelectorChange(this, 'Day', _labelText);
        });
    }

    this._initMonthlyListeners = function() {
        $('#repeatedDrivingAssertion_monthlyFirstWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'First');
        });
        $('#repeatedDrivingAssertion_monthlySecondWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'Second');
        });
        $('#repeatedDrivingAssertion_monthlyThirdWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'Third');
        });
        $('#repeatedDrivingAssertion_monthlyFourthWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'Fourth');
        });
        $('#repeatedDrivingAssertion_monthlyLastWeeklySelector input').on('change', function() {
            _this._monthlyAssertionController.onSelectorChange(this, 'Last');
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

function ShiftSelectionController(selectionIdentifier) {
    var _this = this;

    this._shiftSelectionHolder = null;
    this._shiftSelections = null;

    this.init = function() {
        _this._shiftSelectionHolder = $('.'+selectionIdentifier);
        _this._shiftSelectionIndex = _this._shiftSelectionHolder.find('li').length;
        _this._shiftSelections = new Array();
        _this._initExistingShiftSelections();
    }

    this._initExistingShiftSelections = function() {
        _this._shiftSelectionHolder.find('li').each(function() {
            var _selectionId = $(this).find('.selectionId').val();
            _this._shiftSelections.push(new ShiftSelection(_selectionId, this));
        })
    }

    this.onSelectorChange = function(element, sourceOccruency, selectionText) {
        if(element.checked) {
            _this._addShiftSelection(sourceOccruency, $(element).val(), selectionText);
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
            _newSelection = _prototype.replace(/__label__/g, selectionText),
            _selectionDomElement = $('<li></li>').append(_newSelection);
        _this._shiftSelectionHolder.append(_selectionDomElement);
        $('#repeatedDrivingAssertion_'+selectionIdentifier+'_'+_index+'_selectionId').val(_this._constructSelectionId(selectedOccurency,selectedDay));
        _this._shiftSelections.push(new ShiftSelection(_this._constructSelectionId(selectedOccurency, selectedDay), _selectionDomElement));
    }

    this._constructSelectionId = function(selectedOccurency, selectedDay) {
        return selectedOccurency+'_'+selectedDay;
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