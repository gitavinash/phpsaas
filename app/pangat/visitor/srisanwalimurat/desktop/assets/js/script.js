var previousmonth = "";
var nexttmonth = "";
var dates = new Date();
var month = dates.getMonth();
var year = dates.getFullYear();
var onGoingMonth = dates.getMonth();
var onGoingYear = dates.getFullYear();
var isPrevAllowed = 0;
var isNextAllowed = $('#calenderMonthLimit').val();
var dataIndexer = 0;
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

function popUpMessage(msgPostion, msgTxt, msgType) {
    if (msgType == 200) {
        msgType = "success";
    }

    Swal.fire({
        position: msgPostion,
        type: msgType,
        title: msgTxt,
        showConfirmButton: false,
        timer: 1500,
    });
}

/** Check box guest */

$("#guest").click(function (e) {
    if ($("#guest").prop("checked") == true) {
        $("#checkBox").show();
    } else {
        $("#checkBox").hide();
    }
});

var getprevdate = function (y, m, d) {
    var prevdate = new Date(y, m, -d).getDate();
    return prevdate; // return the last day of month
};
// check for prev month calendar dates //
var premonth = function (y, m) {
    var premonth = 1;
    var preyear = 0;
    if (m == 0) {
        premonth = 11;
        preyear = y - 1;
    } else {
        premonth = parseInt(m) - 1;
        preyear = parseInt(y);
    }
    return preyear + ", " + premonth;
};
var nextmonth = function (y, m) {
    var nexmonth = 0;
    var nexyear = 0;
    if (m == 11) {
        nexmonth = 0;
        nexyear = parseInt(y) + 1;
    } else {
        nexmonth = parseInt(m) + 1;
        nexyear = y;
    }
    return nexyear + ", " + nexmonth;
};
var calendar = function (y, m) {
    //———–Calendar function start ———-//
    $("#calendardates").html(""); // writes current month calendar in html table with id calendar

    m = parseInt(m);
    y = parseInt(y);
    // console.log(m, y, 'Passed Values')
    var selectedFirstDate = new Date(y, m, 1).getDate(); // get first date of month
    var selectedLastDate = new Date(y, m + 1, 0).getDate(); // get last date of month
    var selectedFirstDay = new Date(y, m, 1).getDay(); // get first day of the month
    var selectedLastDay = new Date(y, m + 1, 0).getDay(); // get last day of the month
    var calendardates = "";
    var number = 0;
    var next = 1;
    var prev = "";

    for (var i = 0; i < 6; i++) {
        // run for 6 weeks to get full month

        // creates a table row

        for (var j = 0; j < 7; j++) {
            // run for 7 days of month

            if (i === 0 && j < selectedFirstDay) {
                prev = getprevdate(y, m, selectedFirstDay - j - 1);
                calendardates += "<li class='weekday prevmonth'> <p class='cal-day old-day'>" + prev + "</p></li>";
            } else if (number >= selectedLastDate) {
                calendardates += "<li class='weekday nextmonth'> <p class='cal-day nex-days'>" + next + "</p></li>";
                next++;
            } else if (number + 1 == dates.getDate() && isPrevAllowed <= 0) {
                number++;
                calendardates +=
                    "<li class='weekday clkAlw' bookingAllowed='" +
                    dummydata["availablity"][number - 1]["bookingAllowed"] +
                    "' date='" +
                    number +
                    "'><p class='cal-day'>" +
                    number +
                    " </p><div class='flex-it'><span class='breakfast' data='" +
                    dummydata["availablity"][number - 1]["breakfast"] +
                    "'></span><span class='lunch' data='" +
                    dummydata["availablity"][number - 1]["lunch"] +
                    "'></span><span class='dinner' data='" +
                    dummydata["availablity"][number - 1]["dinner"] +
                    "'></span></div></li > ";
            } else {
                if (number + 1 < dates.getDate() && isPrevAllowed <= 0) {
                    // console.log(number, 'Number')
                    number++;
                    calendardates += " <li class='weekday prev-dates'> <p class='cal-day' id='" + number + "'>" + number + " </p></li > ";
                } else {
                    number++;
                    //alert('not in inarray');
                    calendardates +=
                        " <li class='weekday clkAlw upcoming-dates' bookingAllowed='" +
                        dummydata["availablity"][number - 1]["bookingAllowed"] +
                        "' date='" +
                        number +
                        "'> <p class='cal-day'>" +
                        number +
                        " </p><div class='flex-it'><span class='breakfast' data='" +
                        dummydata["availablity"][number - 1]["breakfast"] +
                        "'></span><span class='lunch' data='" +
                        dummydata["availablity"][number - 1]["lunch"] +
                        "'></span><span class='dinner' data='" +
                        dummydata["availablity"][number - 1]["dinner"] +
                        "'></span></div></li > ";
                }
            }
        }

        if (number >= selectedLastDate) {
            break;
        }
    }
    $(".calendar-body-wraper").removeClass("d-none");

    // premonth(y,m);
    // console.log('Consoled the data', calendardates)
    if (isPrevAllowed <= 0) {
        $("#prearrow").attr("disabled", true);
    }
    if (isNextAllowed <= 0) {
        $("#nexarrow").attr("disabled", true);
    }

    previousmonth = premonth(y, m); // store value of previous month and year
    nexttmonth = nextmonth(y, m); // store value of previous month and year

    $("#month").html(months[m] + " " + y); //write current month and year in div
    $("#calendardates").html(calendardates); // writes current month calendar in html table with id calendar
};

var callingData = { type: "booking", action: "availability" };

function callTheBackednData() {
    $.ajax({
        type: "POST",
        dataType: "json",
        data: callingData,
        // url: 'https://pangat.srisanwalimurat.com/',
        success: function (ret) {
            dummydata = ret;
            // console.log(dummydata);
            calendar(onGoingYear, onGoingMonth);
            // console.log('success');
            $(".retryButton").removeClass("working");
            $(".calendar-body-wraper").removeClass("d-none");
        },
        error: function (err) {
            console.log(err);
            // console.log('In Error')
            $(".retryButton").addClass("failed");
            $(".calendar-body-wraper").addClass("d-none");
        },
    });
}

function markTheDate(m, y) {
    $(".calendar-body-wraper").addClass("d-none");
    $(".retryButton").addClass("working");

    onGoingMonth = m;
    onGoingYear = y;
    m = parseInt(m) + 1;
    let d = "" + y + "-" + m + "-1";
    callingData["date"] = d;
    // console.log(callingData);
    // calendar(onGoingYear, onGoingMonth);
    callTheBackednData();
}

/*—-Call calendar funtion —–*/
$(document).ready(function (argument) {
    callTheBackednData();
    // calendar(onGoingYear, onGoingMonth);

    /**
     *
     * Availability status
     *
     * 0 -> Request received but booking not confirmed
     * 1 -> Slot not available
     * 2 -> Slot available
     */

    $(".retryButton").click(function (argument) {
        callTheBackednData();
        $(".retryButton").addClass("working");
    });
});
var bookingData = {
    bookingDate: "",
    bookingTime: "",
    guest: 1,
};

$("body").delegate('.clkAlw[bookingAllowed="1"]', "click", function (argument) {
    // console.log('Not Allowed')
    $(".no-booking").addClass("active");
    $(".clkAlw").removeClass("highlight");
    $(".clkAlw").removeClass("today");
    $(this).addClass("highlight");
    // let y = nexttmonth.split(', ')
    // $(this).attr(date)
    $(".no-booking span").html($(this).attr("date") + " " + months[onGoingMonth] + " " + onGoingYear);
    // console.log(onGoingYear)
    $(".bookingSlots").removeClass("active");
});

$("body").delegate('.clkAlw[bookingAllowed="0"]', "click", function (argument) {
    // console.log('Not Allowed')
    $(".clkAlw").removeClass("today");
    $(".clkAlw").removeClass("highlight");
    $(this).addClass("highlight");
    $(".no-booking").addClass("active");
    $(".no-booking span").html($(this).attr("date") + " " + months[onGoingMonth] + " " + onGoingYear);
    // $('.bookingSlots').removeClass('active')
});

$("body").delegate('.clkAlw[bookingAllowed="2"]', "click", function (argument) {
    $(".bookedForText div").removeClass("active");
    $(".no-booking").removeClass("active");
    $(".clkAlw").removeClass("today");
    $(".clkAlw").removeClass("highlight");
    bookingData["bookingTime"] = "";
    $(this).addClass("highlight");
    // $('.bookingSlots').addClass('active')
    checkAndShow($(this).children(".flex-it").children(".breakfast"), "breakfast-btn");
    checkAndShow($(this).children(".flex-it").children(".lunch"), "lunch-btn");
    checkAndShow($(this).children(".flex-it").children(".dinner"), "dinner-btn");
    // console.log('Worked', $(this).children('.flex-it').children('.breakfast-btn-btn').attr('data'))
    bookingData["bookingDate"] = onGoingYear + "-" + (parseInt(onGoingMonth) + 1) + "-" + $(this).attr("date");
});

$("body").delegate('.clkAlw[bookingAllowed="1"]', "click", function (argument) {
    if (!$(this).hasClass("isAvl")) {
        return;
    }
    // var bookFor = 'You had booked for ';
    $(".bookedForText div").removeClass("active");
    // console.log('Consoled')
    $(".com-sch").removeClass("booked");
    $(this).addClass("booked");
    var textBook = "";
    textBook = $(this).attr("booking-for");
    let markClass = ".for" + textBook;
    $(markClass).addClass("active");
    $(".bookedForText .time-sch").html($(".highlight").attr("date") + " " + months[onGoingMonth] + " " + onGoingYear);
    // $('.bookedForText').html(bookFor + textBook)
    bookingData["bookingTime"] = textBook;
    // console.log(bookingData)
});

$("body").delegate("#prearrow", "click", function (argument) {
    if (isPrevAllowed <= 0) {
        $("#prearrow").attr("disabled", true);
        return;
    }
    isPrevAllowed--;
    isNextAllowed++;
    // $('.bookingSlots').removeClass('active')
    // console.log(isNextAllowed)
    // console.log(isPrevAllowed)
    $("#nexarrow").removeAttr("disabled");
    var x = previousmonth.split(", ");
    markTheDate(x[1], x[0]);
    // console.log('1', x[0]);
    // calendar('1', (x[0]+1), x[1]);
});
$("body").delegate("#nexarrow", "click", function (argument) {
    if (isNextAllowed <= 0) {
        $("#nexarrow").attr("disabled", true);
        return;
    }
    isNextAllowed--;
    isPrevAllowed++;
    // $('.bookingSlots').removeClass('active')

    // console.log(isNextAllowed)
    // console.log(isPrevAllowed)
    $("#prearrow").removeAttr("disabled");
    var y = nexttmonth.split(", ");
    markTheDate(y[1], y[0]);
    // calendar(y[0], y[1]);
});
$("body").delegate(".com-sch", "click", function (argument) {
    if (!$(this).hasClass("isAvl")) {
        return;
    }
    // var bookFor = 'You had booked for ';
    $(".bookedForText div").removeClass("active");
    $(".booking-select").addClass("d-none");

    // console.log('Consoled')
    $(".com-sch").removeClass("booked");
    $(this).addClass("booked");
    var textBook = "";
    textBook = $(this).attr("booking-for");
    let markClass = ".for" + textBook;
    $(markClass).addClass("active");
    $(".bookedForText .time-sch").html($(".highlight").attr("date") + " " + months[onGoingMonth] + " " + onGoingYear);
    // $('.bookedForText').html(bookFor + textBook)
    bookingData["bookingTime"] = textBook;
    // console.log(bookingData)
});

$(".phoneVerify").keypress(function (e) {
    // console.log('I am in');
    var length = jQuery(this).val().length;
    if (length > 9) {
        return false;
    } else if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    } else if (length == 0 && e.which == 48) {
        return false;
    }
});

$("body").delegate(".submit-data", "click", function (argument) {

    let sendingData = {
        type: "booking",
        action: "request",
        acceptPayment: $('#acceptPayment').val(),
        amount: $('#pangatAmount').val()
    };

    let errFlag = false;

    if (!bookingData["bookingTime"]) {
        $(".booking-select").removeClass("d-none");
        return;
    }

    if (!$(".firstName").val() || $(".firstName").val() == "") {
        $(".firstName").parent().children("p").addClass("warning");
        errFlag = true;
        return;
    }

    if (!$(".emailName").val() || $(".emailName").val() == "") {
        $(".emailName").parent().children("p").addClass("warning");
        errFlag = true;
        return;
    }

    if (!$(".mobName").val() || $(".mobName").val() == "") {
        $(".mobName").parent().children("p").addClass("warning");
        errFlag = true;
        return;
    }

    if (errFlag) {
        return;
    }
    
    $(".serverError").addClass("d-none");

    /** Show booking confirmation screen */

    $('.bookingFormScreen').hide();
    $('.bookingConfirmationScreen').show();

    var selfComing='No';

    if(bookingData.guest==1) {

        selfComing='Yes';

    }

    if(bookingData.bookingTime=='breakfast') {

        bookingAmount = 1500;
        bookingSlotAmount = '₹ ' + bookingAmount;

    } else {

        bookingAmount = 3100;
        bookingSlotAmount = '₹ ' + bookingAmount;

    }

    $('#bookingAmount').html(bookingSlotAmount);
    $('#nameConfirmation').html($(".firstName").val() + ' ' +$(".lastName").val());
    $('#emailConfirmation').html($(".emailName").val());
    $('#mobileConfirmation').html($(".mobName").val());
    $('#comingConfirmation').html(selfComing);

    if(bookingData.guest==1) {

        $('.guestComing').show();
        $('#guestConfirmation').html($(".guestVal").val());

    }

    $('#bookingDateConfirmation').html(bookingData.bookingDate);
    $('#bookingTimeConfirmation').html(bookingData.bookingTime);

    $('#submitForm').val('Pay ₹ ' + bookingAmount);

});

$('#donationAmount').on('change', function(e) {

    var donationAmount = $('#donationAmount').val();

    if(bookingData.bookingTime=='breakfast') {

        bookingAmount = 1500;

    } else {

        bookingAmount = 3100;

    }

    var totalAmount = parseInt(bookingAmount) + parseInt(donationAmount);

    $('#submitForm').val('Pay ₹ ' + totalAmount);

})

$('#editForm').click(function(e) {

    /** Show booking confirmation screen */

    $('.bookingConfirmationScreen').hide();
    $('.bookingFormScreen').show();
    $('#submitForm').show();

});

$("body").delegate(".reqInput", "click", function (argument) {
    $(this).parent().children("p").removeClass("warning");
});

$("body").delegate(".inputBox", "click", function (argument) {
    if ($(this).prop("checked") == true) {
        bookingData["guest"] = 1;
    } else if ($(this).prop("checked") == false) {
        bookingData["guest"] = 0;
    }
});

function formSubmission(sendingData) {

    $('#editForm').hide();
    $('#submitForm').hide();

    if (bookingData["guest"] == 1) {
        if (bookingData["noOfGuest"] != "") {
            var sendDataToServer = true;
        } else {
            popUpMessage("center", "Please enter number of guest", "warning");
        }
    } else {
        var sendDataToServer = true;
    }

    if (sendDataToServer == true) {

        $.ajax({
            type: "POST",
            dataType: "json",
            data: sendingData,
            success: function (ret) {

                console.log(ret);
                
                if (ret.status == 200) {

                    if(ret.url!='#') {
                        
                        $("#submitForm").hide();
                        window.location.href=ret.url;
                        
                    } else {

                        popUpMessage("center", ret.msg, ret.status);
                        
                        $("#submitForm").show();
                        $('#editForm').show();
                        
                    }

                } else {

                    popUpMessage("center", ret.msg, ret.status);

                    $("#submitForm").show();
                    $('#editForm').show();

                }
                
            },
            error: function (err) {
                $("#submitForm").show();
                $('#editForm').show();
                console.log(err);
                resetInputs();
            },
        });
    }
}

function resetInputs() {
    $(".inputClass").each(function (argument) {
        $(this).val("");
    });
    $(".inputBox").prop("checked", false);
    $(".serverError").removeClass("d-none");
}

function checkAndShow(elem, classNameSent) {
    $(".bookingSlots ." + classNameSent).removeClass("isAvl");
    $(".bookingSlots ." + classNameSent).removeClass("notAvl");
    $(".bookingSlots ." + classNameSent).removeClass("notConf");

    if (elem.attr("data") == 0) {
        $(".bookingSlots ." + classNameSent).addClass("isAvl");
    }
    if (elem.attr("data") == 1) {
        $(".bookingSlots ." + classNameSent).addClass("notAvl");
    }
    if (elem.attr("data") == 2) {
        $(".bookingSlots ." + classNameSent).addClass("isAvl");
    }
}


/** Submit data to server */

$("body").delegate("#submitForm", "click", function (e) {

    let sendingData = {
        type: "booking",
        action: "request",
        acceptPayment: $('#acceptPayment').val(),
        amount: $('#pangatAmount').val()
    };

    e.preventDefault();

    if($("#donationAmount").val()=='') {

        donationAmt = 0;

    } else {

        donationAmt = $("#donationAmount").val();

    }

    bookingData["first_name"] = $(".firstName").val();
    bookingData["last_name"] = $(".lastName").val();
    bookingData["mobile"] = $(".mobName").val();
    bookingData["email"] = $(".emailName").val();
    bookingData["noOfGuest"] = $(".guestVal").val();
    bookingData["donation"] = donationAmt;
    sendingData = { ...bookingData, ...sendingData };
    
    // console.log('Final Data is Here to send it backend remove comment from line 585', sendingData)

    $('#submitForm').hide();
    formSubmission(sendingData);

});