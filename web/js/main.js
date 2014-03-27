$(document).ready(function() {
    $('.jqueryDatePicker').each(function() {
        $(this).datepicker({ dateFormat: "dd.mm.yy" });
    })
    $('.actionButton').each(function() {
        $(this).on('click',function() {
            window.location = $(this).attr('data-targetSrc');
        })
    })
});

