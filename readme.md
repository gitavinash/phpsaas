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

### Cancellation Flow

An order can be cancelled before delivery, depending on the business rules:

```text
Pending / Accepted / Processing / Shipped
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
