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
        _expandViewButton = _formView.find('.expandFormButton');
    this._isDisplayed = false;

    $(_expandViewButton).on('click', function(event) {
        event.preventDefault();
        _this._toggleExpandedSection();
    });

    this._toggleExpandedSection = function() {
        if(!_this._isDisplayed) {
            $(_expandedViewSection).show();
            $(_expandViewButton).html('Details verbergen');
            _this._isDisplayed = true;
        }else {
            $(_expandedViewSection).hide();
            $(_expandViewButton).html('Weitere Details');
            _this._isDisplayed = false;
        }
    }

}

