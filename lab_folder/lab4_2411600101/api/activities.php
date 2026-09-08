<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dataFile = __DIR__ . '/data.json';

function readActivities($file) {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function writeActivities($file, $activities) {
    file_put_contents($file, json_encode(array_values($activities), JSON_PRETTY_PRINT));
}

function sendJson($payload, $status = 200) {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$activities = readActivities($dataFile);
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

switch ($method) {

    case 'GET':
        if ($id !== null) {
            foreach ($activities as $activity) {
                if ($activity['id'] === $id) {
                    sendJson($activity);
                }
            }
            sendJson(['error' => 'Activity not found'], 404);
        }
        sendJson($activities);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['name'], $input['type'])) {
            sendJson(['error' => 'Invalid payload. "name" and "type" are required.'], 400);
        }

        $nextId = 1;
        foreach ($activities as $activity) {
            if ($activity['id'] >= $nextId) $nextId = $activity['id'] + 1;
        }

        $newActivity = [
            'id'             => $nextId,
            'name'           => $input['name'],
            'type'           => $input['type'],
            'date'           => $input['date'] ?? date('Y-m-d H:i'),
            'duration'       => $input['duration'] ?? 0,
            'caloriesBurned' => $input['caloriesBurned'] ?? 0,
            'avgHeartRate'   => $input['avgHeartRate'] ?? 0,
            'status'         => $input['status'] ?? 'completed',
        ];

       array_unshift($activities, $newActivity);

       writeActivities($dataFile, $activities);
        sendJson($newActivity, 201);
        break;

    case 'PUT':
        if ($id === null) {
            sendJson(['error' => 'An "id" query parameter is required for updates.'], 400);
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $found = false;

        foreach ($activities as &$activity) {
            if ($activity['id'] === $id) {
                $activity = array_merge($activity, $input ?? []);
                $found = true;
                $updated = $activity;
                break;
            }
        }
        unset($activity);

        if (!$found) {
            sendJson(['error' => 'Activity not found'], 404);
        }

        writeActivities($dataFile, $activities);
        sendJson($updated);
        break;

    case 'DELETE':
        if ($id === null) {
            sendJson(['error' => 'An "id" query parameter is required for deletion.'], 400);
        }
        $remaining = array_filter($activities, fn($a) => $a['id'] !== $id);

        if (count($remaining) === count($activities)) {
            sendJson(['error' => 'Activity not found'], 404);
        }

        writeActivities($dataFile, $remaining);
        sendJson(['success' => true, 'id' => $id]);
        break;

    default:
        sendJson(['error' => 'Method not allowed'], 405);
}
