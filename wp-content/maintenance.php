<?php

//  ATTENTION!
//
//  DO NOT MODIFY THIS FILE BECAUSE IT WAS GENERATED AUTOMATICALLY,
//  SO ALL YOUR CHANGES WILL BE LOST THE NEXT TIME THE FILE IS GENERATED.
//  IF YOU REQUIRE TO APPLY CUSTOM MODIFICATIONS, PERFORM THEM IN THE FOLLOWING FILE:
//  /var/www/vhosts/webpremiere.de/ssv-altenberg.webpremiere.de/wp-content/maintenance/template.phtml


$protocol = $_SERVER['SERVER_PROTOCOL'];
if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
    $protocol = 'HTTP/1.0';
}

header("{$protocol} 503 Service Unavailable", true, 503);
header('Content-Type: text/html; charset=utf-8');
header('Retry-After: 5184000');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="icon" href="https://ssv-altenberg.webpremiere.de/wp-content/uploads/2017/09/cropped-logo-ssv-1-32x32.png">
    <link rel="stylesheet" href="https://ssv-altenberg.webpremiere.de/wp-content/maintenance/assets/styles.css">
    <script src="https://ssv-altenberg.webpremiere.de/wp-content/maintenance/assets/timer.js"></script>
    <title>Scheduled Maintenance</title>
</head>

<body>

    <div class="container">

    <header class="header">
        <h1>The website is undergoing scheduled maintenance.</h1>
        <h2>Sorry for the inconvenience. Come back a bit later, we will be ready soon!</h2>
    </header>

    <!--START_TIMER_BLOCK-->
            <section class="timer">
            <div class="timer__item">
                <div class="timer__data" id="timerResultDays"></div>
                <div class="timer__type">Days</div>
            </div>:
            <div class="timer__item">
                <div class="timer__data" id="timerResultHours"></div>
                <div class="timer__type">Hours</div>
            </div>:
            <div class="timer__item">
                <div class="timer__data" id="timerResultMinutes"></div>
                <div class="timer__type">Minutes</div>
            </div>:
            <div class="timer__item">
                <div class="timer__data" id="timerResultSeconds"></div>
                <div class="timer__type">Seconds</div>
            </div>
        </section>
        <script type="application/javascript">
            startTimer(60, 0, 0, 1517958012, <?php echo time(); ?> - Math.floor(Date.now() / 1000));
        </script>
        <!--END_TIMER_BLOCK-->

    <!--START_SOCIAL_LINKS_BLOCK-->
    <section class="social-links">
                    <a class="social-links__link" href="https://www.facebook.com/ssvaltenberg/" target="_blank" title="Facebook">
                <span class="icon"><img src="https://ssv-altenberg.webpremiere.de/wp-content/maintenance/assets/images/facebook.svg" alt="Facebook"></span>
            </a>
            </section>
    <!--END_SOCIAL_LINKS_BLOCK-->

</div>

<footer class="footer">
    <div class="footer__content">
        Powered by <a href="https://www.plesk.com/" target="_blank"><img class="logo" src="https://ssv-altenberg.webpremiere.de/wp-content/maintenance/assets/images/plesk-logo.png" alt="Plesk"></a>
    </div>
</footer>

</body>
</html>