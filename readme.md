# Configuration & Status Documentation

## Email Configuration

Email configuration values are saved in the database.

| Parameter               | Description                                                     |
| ----------------------- | --------------------------------------------------------------- |
| `email_enable`          | Enable or disable email sending. `1` = Enabled, `0` = Disabled  |
| `email_smtp_default`    | Use default application SMTP configuration. `1` = Yes, `0` = No |
| `email_smtp_host`       | SMTP host                                                       |
| `email_smtp_ssl`        | Enable/disable SMTP SSL                                         |
| `email_smtp_port`       | SMTP port                                                       |
| `email_smtp_username`   | SMTP username                                                   |
| `email_smtp_password`   | SMTP password                                                   |
| `email_smtp_email`      | SMTP email address                                              |
| `email_smtp_name`       | SMTP sender name                                                |
| `email_smtp_reply_name` | Reply-to name                                                   |

---

## SMS Configuration

SMS configuration values are saved in the database.

| Parameter        | Description                    |
| ---------------- | ------------------------------ |
| `sms_auth_key`   | SMS gateway authentication key |
| `sms_sender`     | SMS sender ID                  |
| `sms_route`      | SMS route                      |
| `sms_dlt_sms_id` | DLT SMS template ID            |
| `sms_gateway`    | SMS gateway                    |

---

## Transaction Status

| Status | Description                |
| -----: | -------------------------- |
|    `0` | Initiate                   |
|    `1` | Success                    |
|    `2` | Proceed to payment gateway |
|    `3` | Failed                     |

---

## Quotation Status

| Status | Description        |
| -----: | ------------------ |
|    `0` | Pending            |
|    `1` | Approved by client |
|    `2` | Previous quotes    |

---

## Lead Status

| Status | Description                                |
| -----: | ------------------------------------------ |
|    `0` | Initiate                                   |
|    `1` | Success                                    |
|    `2` | Pending because it is assigned to an agent |
|    `3` | Follow-up                                  |
|    `4` | Failed                                     |
|    `5` | Duplicate                                  |
|    `6` | Payment initiated                          |
|    `7` | Sent for sales request                     |
|    `8` | Upay done                                  |
|    `9` | Itinerary sent                             |
|   `10` | Quotation approved                         |
|   `11` | Voucher issued                             |
|   `12` | Payment received                           |
|   `13` | Trip in progress                           |
|   `14` | Trip completed                             |
|   `15` | Closed                                     |
|   `16` | Cancelled                                  |
|   `17` | Lost                                       |
|   `18` | Payment refund                             |

### Aleya Lead Lifecycle Flow

Each status has a unique identifier without overriding or combining existing statuses.

```text
Initiate (`0`)
    ↓  (Assigned to Agent)
Pending because it is assigned to an agent (`2`)
    ↓  (Itinerary created and dispatched)
Itinerary sent (`9`)
    ↓  (Negotiation, date/destination adjustments)
Follow-up (`3`)
    ↓  (Quotation approved by traveler)
Quotation approved (`10`)
    ↓  (Payment link generated & sent)
Payment initiated (`6`)
    ↓  (Payment completed / verified)
Payment received (`12`) / Upay done (`8`) / Success (`1`)
    ↓  (Hotel, transport, and flight vouchers generated)
Voucher issued (`11`)
    ↓  (Travel commences)
Trip in progress (`13`)
    ↓  (Traveler returns safely)
Trip completed (`14`)
    ↓  (Final feedback & archiving)
Closed (`15`)
```

### Exception & Drop-Off Flows

- **Failed (`4`)**: Ingestion failure or uncontactable / invalid lead inquiry.
- **Duplicate (`5`)**: Repeated inquiry submitted by the same client.
- **Sent for Sales Request (`7`)**: Custom/complex inquiry forwarded to the specialized sales team.
- **Cancelled (`16`)**: Booking or trip cancelled by traveler or agency.
- **Lost (`17`)**: Traveler dropped out due to budget or selected a competitor.
- **Payment Refund (`18`)**: Payment refunded back to traveler after cancellation.

---

## Cart Status

| Status | Description                  |
| -----: | ---------------------------- |
|    `0` | Initiate                     |
|    `1` | Success                      |
|    `2` | Proceed to next step of cart |
|    `3` | Cancel                       |
|    `4` | Remove from cart             |

---

## Website Status

| Status | Description             |
| -----: | ----------------------- |
|    `0` | Initiate / Not verified |
|    `1` | Website is live         |
|    `2` | Website is suspended    |
|    `3` | Website is expired      |
|    `4` | Payment pending         |

---

# Pangat

## Availability Status

| Status | Description                                |
| -----: | ------------------------------------------ |
|    `0` | Request received but booking not confirmed |
|    `1` | Slot not available                         |
|    `2` | Slot available                             |
|    `3` | Slot available, but booking is cancelled   |
|    `4` | Slot blocked by admin                      |
|    `5` | Slot cancelled automatically               |

## Availability Status Type

| Status | Description                        |
| -----: | ---------------------------------- |
|    `0` | Booked or blocked by date          |
|    `1` | Booked or blocked by dynamic range |
|    `2` | Booked or blocked by week          |
|    `4` | Booked or blocked by month         |

> **Note:** In the Booking table, `booking_details` is used to identify week/month blocking.

---

## Marble / IMS Inventory Status

| Status | Description |
| -----: | ----------- |
|    `0` | Initial     |
|    `1` | Live        |
|    `2` | Reserved    |
|    `3` | Sold out    |
|    `4` | Returned    |
|    `5` | Deleted     |

---

## Tour Package Status

| Status | Description |
| -----: | ----------- |
|    `0` | Pending     |
|    `1` | Live        |
|    `2` | Draft       |
|    `3` | Deleted     |

---

# Order Status

Order status follows the standard logistics lifecycle from order creation to final delivery.

| Status | Description        |
| -----: | ------------------ |
|    `0` | Pending            |
|    `1` | Accepted           |
|    `2` | Assigned to Vendor |
|    `3` | Processing         |
|    `4` | Shipped            |
|    `5` | Out for Delivery   |
|    `6` | Delivered          |
|    `7` | Cancelled          |
|    `8` | Returned           |
|    `9` | Rescheduled        |
|    `10` | Not in Service Area        |
|    `11` | Delivery Failed        |

### Normal Order Flow

```text
Pending
   ↓
Accepted
   ↓
Processing
   ↓
Shipped
   ↓
Out for Delivery
   ↓
Delivered
```

### Reschedule Flow

An order can be rescheduled during delivery:

```text
Out for Delivery
       ↓
  Rescheduled
       ↓
Out for Delivery / Delivered
```

### Cancellation Flow

An order can be cancelled before delivery, depending on the business rules:

```text
Pending / Accepted / Processing / Shipped / Rescheduled
                    ↓
                Cancelled
```

### Return Flow

A delivered order can be returned:

```text
Delivered
    ↓
Returned
```
