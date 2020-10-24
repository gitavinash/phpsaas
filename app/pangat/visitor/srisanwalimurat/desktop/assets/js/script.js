var previousmonth = '';
var nexttmonth = '';
var dates = new Date();
var month = dates.getMonth();
var year = dates.getFullYear();
var onGoingMonth = '';
var onGoingYear = '';
var isPrevAllowed = 0;
var isNextAllowed = 2;
var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var dataIndexer = 0;
var dummydata = {
    "status": 200,
    "msg": "Data fetch successfully",
    "current_month": "October 2020",
    "previous_month": "",
    "next_month": "2020-11-01",
    "availablity": [
      {
        "full_date": "01-10-2020",
        "short_date": "Thu, 01",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "02-10-2020",
        "short_date": "Fri, 02",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "03-10-2020",
        "short_date": "Sat, 03",
        "bookingAllowed": 1,
        "breakfast": 1900,
        "lunch": 1222,
        "dinner": 1333
      },
      {
        "full_date": "04-10-2020",
        "short_date": "Sun, 04",
        "bookingAllowed": 1,
        "breakfast": 1011,
        "lunch": 1711,
        "dinner": 1522
      },
      {
        "full_date": "05-10-2020",
        "short_date": "Mon, 05",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "06-10-2020",
        "short_date": "Tue, 06",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "07-10-2020",
        "short_date": "Wed, 07",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "08-10-2020",
        "short_date": "Thu, 08",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "09-10-2020",
        "short_date": "Fri, 09",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "10-10-2020",
        "short_date": "Sat, 10",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "11-10-2020",
        "short_date": "Sun, 11",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "12-10-2020",
        "short_date": "Mon, 12",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "13-10-2020",
        "short_date": "Tue, 13",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "14-10-2020",
        "short_date": "Wed, 14",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "15-10-2020",
        "short_date": "Thu, 15",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "16-10-2020",
        "short_date": "Fri, 16",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "17-10-2020",
        "short_date": "Sat, 17",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "18-10-2020",
        "short_date": "Sun, 18",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "19-10-2020",
        "short_date": "Mon, 19",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "20-10-2020",
        "short_date": "Tue, 20",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "21-10-2020",
        "short_date": "Wed, 21",
        "bookingAllowed": 1,
        "breakfast": 1,
        "lunch": 1,
        "dinner": 1
      },
      {
        "full_date": "22-10-2020",
        "short_date": "Thu, 22",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "23-10-2020",
        "short_date": "Fri, 23",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "24-10-2020",
        "short_date": "Sat, 24",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "25-10-2020",
        "short_date": "Sun, 25",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "26-10-2020",
        "short_date": "Mon, 26",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "27-10-2020",
        "short_date": "Tue, 27",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "28-10-2020",
        "short_date": "Wed, 28",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 1,
        "dinner": 2
      },
      {
        "full_date": "29-10-2020",
        "short_date": "Thu, 29",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "30-10-2020",
        "short_date": "Fri, 30",
        "bookingAllowed": 1,
        "breakfast": 2,
        "lunch": 2,
        "dinner": 2
      },
      {
        "full_date": "31-10-2020",
        "short_date": "Sat, 31",
        "bookingAllowed": 2,
        "breakfast": 2,
        "lunch": 0,
        "dinner": 2
      }
    ]
}

var getprevdate = function(y, m, d) {
  var prevdate = new Date(y, (m), -d).getDate();
  return prevdate; // return the last day of month
};
// check for prev month calendar dates //
var premonth = function(y, m) {
  var premonth = 1;
  var preyear = 0;
  if (m == 0) {
    premonth = 11;
    preyear = y - 1;
  } else {
    premonth = parseInt(m) - 1;
    preyear = parseInt(y);
  }
  return preyear + ', '+premonth;
}
var nextmonth = function(y, m) {
  var nexmonth = 0;
  var nexyear = 0;
  if (m == 11) {
    nexmonth = 0;
    nexyear = parseInt(y) + 1;
  } else {
    nexmonth = parseInt(m) + 1;
    nexyear = y;
  }
  return nexyear + ', '+nexmonth;
}
var calendar = function(y, m) { //———–Calendar function start ———-//
  onGoingMonth = m;
  onGoingYear = y;
  var selectedFirstDate = new Date(y, (m), 1).getDate(); // get first date of month
  var selectedLastDate = new Date(y, (m + 1), 0).getDate(); // get last date of month
  var selectedFirstDay = new Date(y, (m), 1).getDay(); // get first day of the month
  var selectedLastDay = new Date(y, (m + 1), 0).getDay(); // get last day of the month

  var calendardates = '';
  var number = 0;
  var next = 1;
  var prev = '';

  for (var i = 0; i < 6; i++) { // run for 6 weeks to get full month

    // creates a table row

    for (var j = 0; j < 7; j++) { // run for 7 days of month

      if (i === 0 && j < selectedFirstDay) {
        prev = getprevdate(y, m, selectedFirstDay - j - 1);
        calendardates += "<li class='weekday prevmonth'> <p class='cal-day old-day'>"+prev +"</p></li>";
      } else if (number >= selectedLastDate) {
        calendardates += "<li class='weekday nextmonth'> <p class='cal-day nex-days'>"+next +"</p></li>";
        next++;
      } else if (number + 1 == dates.getDate() && isPrevAllowed <= 0) {
        number++;
        calendardates += "<li class='weekday today clkAlw' bookingAllowed='"+dummydata['availablity'][number-1]['bookingAllowed']+"' date='"+number+"'><p class='cal-day'>"+number+" </p><div class='flex-it'><span class='breakfast' data='"+dummydata['availablity'][number-1]['breakfast']+"'></span><span class='lunch' data='"+dummydata['availablity'][number-1]['lunch']+"'></span><span class='dinner' data='"+dummydata['availablity'][number-1]['dinner']+"'></span></div></li > ";
      } else {
        if(number + 1 < dates.getDate() && isPrevAllowed <= 0){
          number++;
          calendardates += " <li class='weekday prev-dates'> <p class='cal-day' id='"+number+"'>"+number+" </p></li > ";
        }
        else{
          number++;
          //alert('not in inarray');
          calendardates += " <li class='weekday clkAlw' bookingAllowed='"+dummydata['availablity'][number-1]['bookingAllowed']+"' date='"+number+"'> <p class='cal-day upcoming-dates'>"+number+" </p><div class='flex-it'><span class='breakfast' data='"+dummydata['availablity'][number-1]['breakfast']+"'></span><span class='lunch' data='"+dummydata['availablity'][number-1]['lunch']+"'></span><span class='dinner' data='"+dummydata['availablity'][number-1]['dinner']+"'></span></div></li > ";
        }
      }
    }

    if (number >= selectedLastDate) {
      break;
    }

  }

  //premonth(y,m);
  // console.log('Consoled the data', calendardates)
  if (isPrevAllowed <= 0) {
    $('#prearrow').attr('disabled', true)
  }
  if (isNextAllowed <= 0) {
    $('#nexarrow').attr('disabled', true)
  }

  previousmonth = premonth(y, m); // store value of previous month and year
  nexttmonth = nextmonth(y, m); // store value of previous month and year

  $('#month').html(months[m] + ' '+y); //write current month and year in div
  $('#calendardates').html(calendardates); // writes current month calendar in html table with id calendar
}

var callingData = {type: 'booking',action: 'availability'}

/*—-Call calendar funtion —–*/
$(document).ready(function (argument) {

    $.ajax({
      type: 'POST',
      dataType: 'json',
      data: {
          'type' : 'booking',
          'action' : 'availability'
      },
      success: function (ret) {
        console.log(ret)
        console.log('success')
      },
      error: function (err) {
        console.log(err)
        console.log('In Error')
      }
    });



  calendar(year, month);
  $('#prearrow').click(function() {
    if (isPrevAllowed <= 0) {
      $('#prearrow').attr('disabled', true)
      return
    }
    isPrevAllowed--;
    isNextAllowed++;
    console.log(isNextAllowed)
    console.log(isPrevAllowed)
    $('#nexarrow').removeAttr('disabled')
    var x = previousmonth.split(', ');
    calendar(x[0], x[1]);

  });

  $('#nexarrow').click(function() {
    if (isNextAllowed <= 0) {
      $('#nexarrow').attr('disabled', true)
      return
    }
    isNextAllowed--;
    isPrevAllowed++;
    console.log(isNextAllowed)
    console.log(isPrevAllowed)
    $('#prearrow').removeAttr('disabled')
    var y = nexttmonth.split(', ');
    calendar(y[0], y[1]);

  });

  /**
   * 
   * Availability status
   * 
   * 0 -> Request received but booking not confirmed
   * 1 -> Slot not available
   * 2 -> Slot available
  */
  $('.clkAlw[bookingAllowed="1"]').click(function (argument) {
    $('.no-booking').addClass('active');
    $('.clkAlw').removeClass('highlight');
    $(this).addClass('highlight')
    // let y = nexttmonth.split(', ')
    // $(this).attr(date)
    $('.no-booking span').html($(this).attr('date') + ' ' + months[onGoingMonth] + ' ' + onGoingYear )
    console.log(onGoingYear)
    $('.bookingSlots').removeClass('active')
  })
  $('.clkAlw[bookingAllowed="0"]').click(function (argument) {
    $('.clkAlw').removeClass('highlight');
    $(this).addClass('highlight')
    $('.no-booking').addClass('active')
    $('.no-booking span').html($(this).attr('date') + ' ' + months[onGoingMonth] + ' ' + onGoingYear )
    $('.bookingSlots').removeClass('active')
  })

  $('.clkAlw[bookingAllowed="2"]').click(function (argument) {
    $('.bookedForText div').removeClass('active')
    $('.no-booking').removeClass('active')
    $('.clkAlw').removeClass('today');
    $('.clkAlw').removeClass('highlight');
    $(this).addClass('highlight')
    $('.bookingSlots').addClass('active')
    checkAndShow($(this).children('.flex-it').children('.breakfast'), 'breakfast')
    checkAndShow($(this).children('.flex-it').children('.lunch'), 'lunch')
    checkAndShow($(this).children('.flex-it').children('.dinner'), 'dinner')
    // console.log('Worked', $(this).children('.flex-it').children('.breakfast').attr('data'))
    bookingData['bookingDate'] = $(this).attr('date') + ' ' +  months[onGoingMonth] + ' ' + onGoingYear
  })
  var bookingData ={
    'bookingDate': '',
    'bookedFor': []
  }
  $('.com-sch').click(function (argument) {
    if (!$(this).hasClass('isAvl')) {
      return
    }
    // var bookFor = 'You had booked for ';
    $('.bookedForText div').removeClass('active')
    // console.log('Consoled')
    $('.com-sch').removeClass('booked')
    $(this).addClass('booked')
    var textBook = '';
    textBook = $(this).attr('booking-for')
    let markClass = '.for' + textBook;
    $(markClass).addClass('active');
    $('.bookedForText .time-sch').html($('.highlight').attr('date') + ' ' + months[onGoingMonth] + ' ' + onGoingYear)
    // $('.bookedForText').html(bookFor + textBook)
    bookingData['bookedFor'] = textBook;
    console.log(bookingData)
  })
})



function checkAndShow(elem, classNameSent){
  $(".bookingSlots ."+ classNameSent).removeClass('isAvl')
  $(".bookingSlots ."+ classNameSent).removeClass('notAvl')
  $(".bookingSlots ."+ classNameSent).removeClass('notConf')

  if (elem.attr('data') == 0) {
    $(".bookingSlots ."+ classNameSent).addClass('notConf')
  }
  if (elem.attr('data') == 1) {
    $(".bookingSlots ."+ classNameSent).addClass('notAvl')
  }
  if (elem.attr('data') == 2) {
    $(".bookingSlots ."+ classNameSent).addClass('isAvl')
  }
}




