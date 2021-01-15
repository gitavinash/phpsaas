<h2>Manage calender</h2>
<div class="form-box">
    <form id="manageCalender">
        <div class="form-group">
            <label>Select date for disable booking</label>
            <div class="calender-box">
            	<div id="myCalendar" class="vanilla-calendar">
				  	<div class="vanilla-calendar-header">
				      	<button class="vanilla-calendar-btn" id="prearrow" disabled="">
				      		<i class="fa fa-chevron-left"></i>
				      	</button>
				      	<div class="vanilla-calendar-header__label" id="month"></div>
				      	<button class="vanilla-calendar-btn" id="nexarrow">
				      		<i class="fa fa-chevron-right"></i>
				      	</button>
					</div>
					<div class="vanilla-calendar-week">
				    	<span>S</span>
				      	<span>M</span>
				      	<span>T</span>
				      	<span>W</span>
				      	<span>T</span>
				      	<span>F</span>
				      	<span>S</span>
					</div>
					<div class="calendar-body-wraper d-none">
					  	<ul class="calendar-dates vanilla-calendar-body m-0 p-0" id="calendardates">
						</ul>
					</div>
					<button id="blockDate" class="submit-data">Block calender date</button>
				</div>
            </div>
        </div>
    </form>
</div>