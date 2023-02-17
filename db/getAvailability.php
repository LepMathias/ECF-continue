<?php
require '../public/src/models/SchedulesManager.php';
require '../public/src/models/SettingManager.php';
require '../public/src/models/ReservationManager.php';
include 'confDB.php';

$pdo = new PDO("mysql:host=$HOST;dbname=$DB", $USER, $PWD);
$schedulesManager = new SchedulesManager($pdo);
$settingManager = new SettingManager($pdo);
$reservationManager = new ReservationManager($pdo);

//$s = date(mktime(0,0,0,3,20,2023));
//$ss = date('Y-m-d', $s);
$q = $_GET['q'];
$date = date('l', strtotime($q));
    switch ($date) {
        case 'Monday':
            $day = 'Lundi';
            break;
        case 'Thuesday':
            $day = 'Mardi';
            break;
        case 'Wednesday':
            $day = 'Mercredi';
            break;
        case 'Thursday':
            $day = 'Jeudi';
            break;
        case 'Friday':
            $day = 'Vendredi';
            break;
        case 'Saturday':
            $day = 'Samedi';
            break;
        case 'Sunday':
            $day = 'Dimanche';
            break;
    }
$maxOfGuest = $settingManager->getSettings('maxOfGuest');
$schedulesOfDay = $schedulesManager->getSchedulesDay($day);
$nbrOnLunch = $reservationManager->getCountLunch($q, $schedulesManager->getSchedulesDay($day)->startDej, $schedulesManager->getSchedulesDay($day)->endDej);
$nbrOnDiner = $reservationManager->getCountDiner($q, $schedulesManager->getSchedulesDay($day)->startDin, $schedulesManager->getSchedulesDay($day)->endDin);
$availability = $schedulesManager->getAvailableHours($day, $nbrOnLunch[0], $nbrOnDiner[0], $maxOfGuest->content);

if($availability['lunch'] === "close"){
    echo "<option value='' selected disabled>Fermé au déjeuner</option>";
}
if($availability['lunch'] === "full"){
    echo "<option value='' selected disabled>Complet au déjeuner</option>";
}
foreach ($availability as $service) {
    foreach ($service as $i) {
        echo "<option value=" . $i . ">" . $i . "</option>";
    }
}
if($availability['diner'] === "close"){
    echo "<option value='' disabled>Fermé au dîner</option>";
}
if($availability['diner'] === "full"){
    echo "<option value='' disabled>Complet au dîner</option>";
}