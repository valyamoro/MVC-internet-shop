<?php
session_start();

function logSessionEvent($event) {
    $logFile = '../../storage/session_log.txt';
    $timeStamp = date('Y-m-d H:i:s');
    $sessionId = session_id();
    $logEntry = "[$timeStamp] Session ID: $sessionId - $event\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}