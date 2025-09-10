<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo SITE_NAME ?> - <?php echo $tour_package_title ?></title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f8fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        header img {
            max-height: 60px;
        }

        header .company-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4CAF50;
        }

        h1,
        h2,
        h3 {
            color: #4CAF50;
            font-weight: 600;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
        }

        h2 {
            margin-top: 20px;
            font-size: 1.5rem;
        }

        h3 {
            margin-top: 20px;
            font-size: 1.3rem;
        }

        p {
            margin: 10px 0;
            color: #555;
        }

        .highlight {
            color: #4CAF50;
            font-weight: 600;
        }

        .section {
            margin-bottom: 20px;
        }

        .package-details,
        .policy-section {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
            font-size: 0.9rem;
            color: #555;
            margin-top: 40px;
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                margin: 0;
                padding: 10px;
                border: none;
                box-shadow: none;
            }

            header,
            footer {
                position: static;
                width: 100%;
            }

            footer {
                margin-top: 20px;
            }

            h1,
            h2,
            h3,
            p,
            ul,
            li {
                color: black;
            }

            .page-break {
                page-break-before: always;
            }

            .package-details,
            .policy-section {
                page-break-inside: avoid;
            }

            table,
            th,
            td {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header with Logo and Company Name -->
        <header>
            <!-- <img src="https://musafirindiatrip.com/img/general/logo.png" alt="Company Logo"> -->
            <div class="company-name"><?php echo SITE_NAME ?></div>
        </header>

        <h1><?php echo $tour_package_title ?></h1>
        <p>Dear Mr. Customer,</p>
        <p>Greetings from <strong><?php echo SITE_NAME ?></strong>!</p>
        <p><?php echo $tour_package_overview ?></p>

        <!-- Package Overview -->
        <div class="package-details">
            <h2>Tour Package Overview</h2>
            <p><span class="highlight">Duration:</span> <?php echo $tour_package_duration ?> Nights / <?php echo $tour_package_duration + 1 ?> Days</p>
            <p><span class="highlight">Destination:</span> <?php echo $tour_package_city ?>, <?php echo $tour_package_state ?>, <?php echo $tour_package_country ?></p>
        </div>

        <!-- Simplified Itinerary -->
        <div class="section">
            <h2>Itinerary</h2>
            <!-- 
            <h3>Day 01: Delhi - Manali</h3>
            <p>Catch the Volvo bus from Delhi to Manali in the evening (6:00 PM). Overnight journey in the Volvo bus.</p>

            <h3>Day 02: Manali Arrival - Local Sightseeing</h3>
            <p>Arrive in Manali in the morning. Check-in at the hotel and proceed for local sightseeing, including Hadimba Devi Temple, Tibetan Monasteries, and Mall Road. Dinner and overnight stay at the hotel.</p>

            <h3>Day 03: Return to Delhi</h3>
            <p>After your stay in Manali, catch the Volvo bus in the evening and return to Delhi.</p> -->

            <?php

            $itineraryData = json_decode($tour_package_itinerary, TRUE);

            for ($i = 0; $i < count($itineraryData); $i++) {
                echo "<h3>{$itineraryData[$i]['day']}</h3>";
                echo "<p>{$itineraryData[$i]['overview']}</p>";
            }

            ?>

        </div>

        <!-- Package Inclusions -->
        <div class="package-details">
            <h2>Package Inclusions</h2>
            <ul>
                <?php

                $inclusionsList = explode(', ', $tour_package_inclusions);

                foreach ($inclusionsList as $inclusions) {
                    echo "<li>" . ucfirst(strtolower($inclusions)) . "</li>";
                }

                ?>
            </ul>
        </div>

        <!-- Package Exclusions -->
        <div class="package-details">
            <h2>Package Exclusions</h2>
            <ul>
                <?php

                $exclusionsList = explode(', ', $tour_package_exclusions);

                foreach ($exclusionsList as $exclusions) {
                    echo "<li>" . ucfirst(strtolower($exclusions)) . "</li>";
                }

                ?>
            </ul>
        </div>

        <!-- Hotels Details and Package Costs -->
        <div class="package-details">
            <h2>Hotel Details and Package Costs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Package Type</th>
                        <th>Hotel Name</th>
                        <th>Total Cost (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Deluxe</td>
                        <td>Hotel Ocean Inn / Hotel Elite Cottage / Silvermoon / Similar</td>
                        <td>11,000</td>
                    </tr>
                    <tr>
                        <td>Semi Super Deluxe</td>
                        <td>Mountain Green Villa / Manali Heaven / Hotel Khushboo Resort / Similar</td>
                        <td>12,500</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Notes and Cancellation Policy -->
        <div class="policy-section">
            <h2>Notes</h2>
            <ul>
                <li>Hotels are subject to availability at the time of reservation.</li>
                <li>We are not responsible for any natural calamity (landslides, bad weather, etc.) during the tour.</li>
                <li>Vehicle AC will be used only on flat land surfaces, not on hilly areas.</li>
                <li>Local sightseeing will be covered only if time and situation permit.</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>Cancellation Policy</h2>
            <ul>
                <li>20% cancellation of the package cost after confirmation/issuing the hotel voucher.</li>
                <li>30% cancellation of the package cost before 10 days of the travel date.</li>
                <li>50% cancellation of the package cost before 7 days of the travel date.</li>
                <li>100% cancellation of the package cost before 3 days of the travel date.</li>
            </ul>
        </div>

        <!-- Footer with Contact Details -->
        <!-- <footer>
            <p>Contact us at:</p>
            <p>Phone: +91-1234567890 | Email: info@musafirindiatrip.com</p>
            <p>Website: www.musafirindiatrip.com</p>
        </footer> -->
    </div>

</body>

</html>