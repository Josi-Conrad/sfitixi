/* iTixi project: http://sourceforge.net/projects/itixi/files/
 * jQuery : localized (de-CH) date, birthday, datetime, time and daterange pickers 
 * credits: jQuery.com, trentrichardson.com and all guys and girls at stackoverflow.com 
 * 09.12.2013 martin jonasse initial file 
 * 10.12.2013 martin jonasse integrate with symfony/sfitixi
 * 09.01.2014 martin jonasse added upgraded dateplanner (one year into future)
 */
$(function()
{
    /* general purpose datepicker ..................... */
    $( ".jq_datepicker" )
        .datepicker({
            changeMonth: true,
            changeYear: true
        })
    ;

    /* datepicker for tasks ..................... */
    $( ".jq_taskdatepicker" )
        .datepicker(
            {
                onSelect: function(date)
                {/* date selected event */
                    var shortd = date.replace(/\./g, ''); // use this as the id
                    var taskid = document.getElementById("task" + shortd);
                    if (taskid == null)
                    { /* object doesn't exist: make an object */
                        var trash = $("#trash").html().replace(/\?/g, shortd );
                        var prefix =
                            "<p id=\"task" + shortd + "\">" + trash +
                            "<input name= \"date" + shortd + "\" type=\"text\" value=\"";
                        var shifts = $("#shifts").html().replace(/\?/g, shortd );
                        var postfix = "\" />" + shifts + "</p>";
                        $("#tasks").append(prefix + date + postfix);
                    }
                    else
                    {/* object already exists: remove the object */
                        taskid.parentNode.removeChild(taskid);
                    }
                },
                numberOfMonths: 2,
                minDate: 0
            }
        )
    ;

    /* datepicker for birthdays in the past ..................... */
    $( ".jq_birthdaypicker")
        .datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: '-125:+0'
        })
    ;

    /* datepicker combined with time: hours & minutes ..................... */
    $('.jq_datetimepicker')
        .datetimepicker({
            timeText: 'Zeit',
            hourText: 'Stunde',
            minuteText: 'Minute',
            secondText: 'Sekunde',
            currentText: 'Jetzt',
            closeText: 'Fertig'
        })
    ;

    /* general purpose timepicker ..................... */
    $('.jq_timepicker')
        .timepicker({
            timeOnlyTitle: 'Tageszeit',
            timeText: 'Zeit',
            hourText: 'Stunde',
            minuteText: 'Minute',
            secondText: 'Sekunde',
            currentText: 'Jetzt',
            closeText: 'Fertig'
        })
    ;

    /* datepicker for a range of dates from .. to ..................... */
    $(".jq_daterangepicker")
        .datepicker({
	        minDate: 0,
			numberOfMonths: [2,4],
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
		})
    ;

});


