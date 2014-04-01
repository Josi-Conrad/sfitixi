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

