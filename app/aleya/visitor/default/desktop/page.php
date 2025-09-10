<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $page_title ?></title>
    <?php require_once DEFAULT_META ?>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
            color: #333;
        }

        h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
        }

        h3 {
            font-size: 1.2rem;
            margin-top: 15px;
        }

        p {
            margin: 10px 0;
            color: #555;
            font-size: 0.95rem;
        }

        ul {
            padding-left: 20px;
            color: #555;
        }

        ul li {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        a {
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #333;
        }

        a:hover {
            color: #555;
            border-bottom: 1px solid #555;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            font-size: 0.85rem;
            color: #777;
        }

        footer p {
            margin: 0;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8rem;
            }

            h2 {
                font-size: 1.3rem;
            }

            h3 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header -->
        <header>
            <h1><?php echo $page_title ?></h1>
        </header>

        <!-- Terms and Conditions Section -->
        <section id="terms" style="margin-top: 10px;">
            <?php

            echo $page_description;

            ?>
        </section>

        <!-- Footer -->
        <!-- <footer>
            <p>&copy; 2024 <?php echo SITE_NAME ?>. All rights reserved.</p>
            <p>Phone: +91-1234567890 | Email: <a href="mailto:info@yourcompany.com">info@yourcompany.com</a></p>
        </footer> -->
    </div>

</body>

</html>