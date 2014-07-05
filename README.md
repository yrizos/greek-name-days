GreekNameDays
=============

Client for the [GreekNameDays API](http://www.greeknamedays.gr/). The API requires a subscription, for more information see:

- http://www.greeknamedays.gr/greeknamedaystools.php
- http://www.greeknamedays.gr/docs/webservice.pdf

## Install

[Composer](https://getcomposer.org/):

    require: "yrizos/greek-name-days": "dev-master"

## Usage

    $username = "<your username>";
    $password = "<your password>";
    $language = "gr";

    $gnd = new GreekNameDays($username, $password, $language);

    $resultsByDate    = $gnd->getByDate(2014, 7, 1);
    $resultsByMonth   = $gnd->getByMonth(2014, 7);
    $resultsByInitial = $gnd->getByInitial("i");
    $resultsByName    = $gnd->getByName("ioannis");





