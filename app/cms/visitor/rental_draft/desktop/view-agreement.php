<?php

$orderId = base64_decode($id);

if ($verifyData = mysqli_query($connection, "SELECT enquiry_status, enquiry_message FROM " . TABLE_PREFIX . "enquiry WHERE enquiry_id='{$orderId}' AND enquiry_site_id=" . SITE_ID)) {
    if (mysqli_num_rows($verifyData) != 0) {
        extract(mysqli_fetch_assoc($verifyData));
        extract(json_decode($enquiry_message, TRUE));
        $showAgreement = true;
    }
} else {
    if (DEVELOPMENT_MODE == TRUE) {
        echo mysqli_error($connection);
    } else {
        echo "Something went wrong while fetching details";
    }
}

if (isset($showAgreement)) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <title>Rent agreement between <?php echo ucwords($landlord_name) ?> and <?php echo ucwords($tenate_name) ?> |
            <?php echo SITE_NAME ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
            rel="stylesheet">
        <style>
            /* General styles for both web and PDF */
            body {
                font-family: "Roboto", sans-serif;
                line-height: 2rem;
                font-size: 1rem;
                margin: 20px;
            }

            .watermark {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: rgba(0, 0, 0, 0.1);
                /* Light gray color with transparency */
                font-size: 5rem;
                /* Adjust the size as needed */
                transform: rotate(-45deg);
                /* Rotate the watermark */
            }

            .watermark span {
                display: block;
            }

            .content {
                position: relative;
                z-index: 1;
                padding: 20px;
            }

            /* Headers */
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                color: #333;
            }

            /* Paragraphs */
            p {
                margin-bottom: 10px;
            }

            /* Lists */
            ul,
            ol {
                margin-bottom: 10px;
                padding-left: 20px;
            }

            /* Tables */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            table,
            th,
            td {
                border: 1px solid #ddd;
            }

            th,
            td {
                padding: 10px;
                text-align: left;
            }

            /* Links */
            a {
                color: #0066cc;
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            /* Print-specific styles */
            @media print {
                body {
                    margin: 0;
                }

                a {
                    text-decoration: underline;
                    color: black;
                }

                /* Hide navigation or other web-only elements */
                .no-print {
                    display: none;
                }

                /* Page breaks for printing */
                .page-break {
                    page-break-before: always;
                }
            }

            /* Web-specific styles */
            @media screen {

                /* Add padding for better readability on screen */
                body {
                    padding: 20px;
                }

                /* Styling for buttons */
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: white;
                    background-color: #007BFF;
                    border: none;
                    border-radius: 4px;
                    text-decoration: none;
                    text-align: center;
                    cursor: pointer;
                }

                .btn:hover {
                    background-color: #0056b3;
                }

                /* Show navigation or other web-only elements */
                .no-print {
                    display: block;
                }
            }
        </style>
    </head>

    <body>
        <?php

        if ($enquiry_status != 10) { ?>
            <div class="watermark">
                <span><?php echo SITE_URL ?></span>
            </div>
        <?php
        }

        $day = date('j', strtotime($agreement_start_date));
        $month = date('F', strtotime($agreement_start_date));
        $year = date('Y', strtotime($agreement_start_date));

        // Determine the appropriate suffix for the day
        if ($day == 1 || $day == 21 || $day == 31) {
            $suffix = 'st';
        } elseif ($day == 2 || $day == 22) {
            $suffix = 'nd';
        } elseif ($day == 3 || $day == 23) {
            $suffix = 'rd';
        } else {
            $suffix = 'th';
        }

        switch ($payment_date) {
            case '1':
                $payment_date = $payment_date . 'st';
                break;
            case '2':
                $payment_date = $payment_date . 'nd';
                break;
            case '3':
                $payment_date = $payment_date . 'rd';
                break;
            default;
                $payment_date = $payment_date . 'th';
                break;
        }

        ?>

        <p>This agreement is made on this <?php echo $day . $suffix ?> day of <?php echo ucfirst($month) ?>,
            <?php echo $year ?>,
            between <?php echo $landlord_name ?>, with +91-<?php echo $landlord_mobile ?> as Mobile
            Number, residing at <?php echo $landlord_address ?>, hereinafter referred to as the 'LESSOR' of
            the One Part, AND <?php echo $tenate_name ?>, with +91-<?php echo $tenate_mobile ?> as Mobile Number, residing
            at <?php echo $tenate_address ?>, hereinafter referred to as the 'LESSEE(s)' of the other Part.</p>
        <p>WHEREAS the Lessor is the lawful owner of, and otherwise well sufficiently entitled to,
            <?php echo $property_address ?>, and comprising of <?php echo $property_bhk ?> present in
            <?php echo $property_floor ?> floor, hereinafter referred to as the 'said premises'.</p>
        <p>AND WHEREAS at the request of the Lessee, the Lessor has agreed to let the said premises to the tenant for a term
            of 11 Months commencing from <?php echo $day . $suffix . ' ' . $month . ', ' . $year ?> in the manner
            hereinafter appearing.</p>
        <p><strong>NOW THIS AGREEMENT WITNESSETH AND IT IS HEREBY AGREED BY AND BETWEEN THE PARTIES AS UNDER:</strong></p>

        <ul>
            <li>That the Lessor hereby grants to the Lessee the right to enter, use, and remain in the said premises along
                with the existing fixtures and fittings listed in Annexure 1 to this Agreement and that the Lessee shall be
                entitled to peacefully possess and enjoy possession of the said premises for residential use, and the other
                rights herein.</li>
            <li>That the lease hereby granted shall, unless canceled earlier under any provision of this Agreement, remain
                in force for a period of 11 Months.</li>
            <li>That the Lessee will have the option to terminate this lease by giving <?php echo $notice_period ?> notice
                in writing to the
                Lessor.</li>
            <li>That the Lessee shall have no right to create any sub-lease or assign or transfer in any manner the lease or
                give to anyone the possession of the said premises or any part thereof.</li>
            <li>That the Lessee shall use the said premises only for residential purposes.</li>
            <li>That the Lessor shall, before handing over the said premises, ensure the working of sanitary, electrical,
                and water supply connections and other fittings pertaining to the said premises. It is agreed that it shall
                be the responsibility of the Lessor for their return in working condition at the time of re-possession of
                the said premises, subject to normal wear and tear.</li>
            <li>That the Lessee is not authorized to make any alteration in the construction of the said premises.</li>
            <li>That the day-to-day repair jobs shall be effected by the Lessee at his own cost, and any major repairs,
                either structural or to the electrical or water connection, plumbing leaks, or water seepage shall be
                attended to by the Lessor. In the event of the Lessor failing to carry out the repairs on receiving notice
                from the Lessee, the Lessee shall undertake the necessary repairs and the Lessor will be liable to
                immediately reimburse costs incurred by the Lessee.</li>
            <li>That the Lessor or its duly authorized agent shall have the right to enter or upon the said premises or any
                part thereof at a mutually arranged convenient time for the purpose of inspection.</li>
            <li>That in consideration of the use of the said premises, the Lessee agrees that he shall pay to the Lessor
                during the period of this agreement a monthly rent at the rate of ₹ <?php echo $rent_amount ?>. The amount
                will be paid in
                advance on or before the <?php echo $payment_date ?> of every English calendar month. That the Lessee shall
                pay to the Landlord a including
                monthly maintenance charge towards the maintenance of the property.</li>
            <li>It is hereby agreed that in the event of default in payment of the rent for a consecutive period of three
                months the Lessor shall be entitled to terminate the lease.</li>
            <li>That the Lessee has paid to the Lessor a sum of ₹ <?php echo $security_amount ?> as a deposit, free of
                interest. The said deposit
                shall be returned to the Lessee simultaneously with the Lessee vacating the said premises. In the event of
                failure on the part of the Lessor to refund the said deposit amount to the Lessee as aforesaid, the Lessee
                shall be entitled to continue to use and occupy the said premises without payment of any rent until the
                Lessor refunds the said amount.</li>
            <li>That the Lessor shall be responsible for the payment of all taxes and levies pertaining to the said premises
                including but not limited to House Tax, Property Tax, other cesses, if any, and any other statutory taxes,
                levied by the Government or Governmental Departments. During the term of this Agreement, the Lessor shall
                comply with all rules, regulations, and requirements of any statutory authority, local, state, and central
                government, and governmental departments in relation to the said premises.</li>
        </ul>
        <p>IN WITNESS WHEREOF, the parties hereto have set their hands on the day and year first hereinabove mentioned.</p>
        <div style="float: left; width:50%; text-align:left">
            <p><strong>Agreed & Accepted by the Lessor</strong></p>
            <p><br /><br /><?php echo $landlord_name ?></p>
        </div>
        <div style="float: left; width:50%; text-align:right">
            <p><strong>Agreed & Accepted by the Lessee</strong></p>
            <p><br /><br /><?php echo $tenate_name ?></p>
        </div>
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <p>WITNESS</p>
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <p><strong>ANNEXURE I</strong></p>
        <p>List of fixtures and fittings provided in <?php echo $property_address ?></p>
        <p>
            <?php echo $comment ?>
        </p>
    </body>

    </html>
<?php
} else {
    http_response_code(403);
    echo "No access found";
    exit();
}
