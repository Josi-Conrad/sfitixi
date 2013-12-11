/* iTixi project: http://sourceforge.net/projects/itixi/files/
 * jQuery : localized (de-CH) date, birthday, datetime, time and daterange pickers 
 * credits: jQuery.com, trentrichardson.com and all guys and girls at stackoverflow.com 
 * 09.12.2013 martin jonasse initial file 
 * 10.12.2013 martin jonasse integrate with symfony/sfitixi
 */
    $(function() {
        $( ".jq_datepicker" )
            .datepicker( {changeMonth: true, changeYear: true }
        );
        $( ".jq_birthdaypicker")
            .datepicker( {changeMonth: true, changeYear: true, yearRange: '-125:+0'}
        );
        $('.jq_datetimepicker')
            .datetimepicker({
                timeText: 'Zeit',
                hourText: 'Stunde',
                minuteText: 'Minute',
                secondText: 'Sekunde',
                currentText: 'Jetzt',
                closeText: 'Fertig'
        });
        $('.jq_timepicker')
            .timepicker({
                timeOnlyTitle: 'Tageszeit',
                timeText: 'Zeit',
                hourText: 'Stunde',
                minuteText: 'Minute',
                secondText: 'Sekunde',
                currentText: 'Jetzt',
                closeText: 'Fertig'
        });
        $(".jq_daterangepicker")
            .datepicker({
				minDate: 0,
				numberOfMonths: [3,4],
				beforeShowDay: function(date) {
					var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#fpw_von").val());
					var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#fpw_bis").val());
					return [true, date1 && ((date.getTime() == date1.getTime()) || (date2 && date >= date1 && date <= date2)) ? "dp-highlight" : ""];
				},
				onSelect: function(dateText, inst) {
                    var date1 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#fpw_von").val());
					var date2 = $.datepicker.parseDate($.datepicker._defaults.dateFormat, $("#fpw_bis").val());
                    var selectedDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, dateText);
					if (!date1 || date2) {
						$("#fpw_von").val(dateText);
						$("#fpw_bis").val("");
						$(this).datepicker();
					} else if( selectedDate < date1 ) {
						$("#fpw_bis").val( $("#fpw_von").val() );
						$("#fpw_von").val( dateText );
						$(this).datepicker();
					} else {
						$("#fpw_bis").val(dateText);
						$(this).datepicker();
					}
				}
			});
        }
	);
