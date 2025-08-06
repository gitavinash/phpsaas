Email configuration value saved in database

email_enable

This parameter is used for sending a email

          ( Send email 1 is true and 0 is false  )

email_smtp_default ( Use default app 1 is SMTP configutaion o )
email_smtp_host
email_smtp_ssl
email_smtp_port
email_smtp_username
email_smtp_password
email_smtp_email
email_smtp_name
email_smtp_reply_name

SMS configuration value saved in database

sms_auth_key
sms_sender
sms_route
sms_dlt_sms_id
sms_gateway

Transaction status

0 : Initiate
1 : Success
2 : Proceed to payment gateway
3 : Failed

Quotation status

0 : Pending
1 : Approved by client
2 : Previous quotes

Lead status

0 : Initiate
1 : Success
2 : Pending because it's assign to some agent
3 : follow up
4 : Failed
5 : Duplicate
6 : payment initiate
7 : Sent for sales request
8 : Upay done

Cart Status

0 : Initiate
1 : Success
2 : Procced to next step of cart
3 : Cancel
4 : Remove from cart

Website status

0 : Initiate or Not verified
1 : Website is live
2 : Website is suspend
3 : Website is expired
4 : Payment pending

Pangat

Availability status
0 : Request received but booking not confirmed
1 : Slot not available
2 : Slot available
3 : Slot available ( but booking is cancelled )
4 : Slot blocked by admin
5 : Slot cancel automatically

Availablity status type

0 : Booked or blocked by date
1 : Booked or blocked by dynamic range
2 : Booked or blocked by week ( in Booking table : booking_details = block by week )
4 : Booked or blocked by month ( in Booking table : booking_details = block by month )

Marble or IMS Inventory

0 : Intial
1 : live
2 : Sold
3 : Sold out
4 : Return
5 : Delete

Tour package status

0: Pending
1: Live
2: Draft
3: Delete
