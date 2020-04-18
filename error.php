<?php
    $message = isset($_GET["message"]) ? $_GET["message"] : "No Message Supplied";
    $referer = isset($_GET["referer"]) ? $_GET["referer"] : "/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>We're sorry</title>

    <style>
        html, body {
            margin: 0;
            padding: 0;

            background-color: black;

            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            color: white;
        }

        .container-wrapper {
            overflow-x: auto;
            height: 100vh;

            display: flex;

            flex-direction: column;

            justify-content: center;
            align-items: center;
        }

        .error-message {
            color: gray;
        }

        a {
            color: #00CDCD;
        }
    </style>
</head>
<body>

    <div class="container-wrapper">
        <div>
            <h1>
                :(
            </h1>

            <h2>
                Error happened and we we're unable to recover from it<br />
                You can read the details below.
            </h2>

            <p class="error-message">
                <?php echo $message; ?>
            </p>

            <p>
                <?php echo "<a href='$referer'>You can try again by clicking here</a>"; ?>
            </p>

            <p>
                We apologize for the inconvenience.<br />
                &copy; 2020
            </p>
        </div>
    </div>

</body>
</html>