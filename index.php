<?php
declare(strict_types=1);

use Pearson\ExamScoresManager;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'Pearson/ExamScoresManager.php';

$scoresObj = new ExamScoresManager("scores.csv");

echo $scoresObj->ConvertToJSON();



