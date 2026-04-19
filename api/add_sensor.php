<?php
/**
 * API Endpoint: Add Sensor
 * POST /api/add_sensor.php
 * 
 * Expected JSON payload:
 * {
 *     "id": "SEN-001",
 *     "type": "AQI",
 *     "status": "monitoring",
 *     "area": "Mumbai",
 *     "location": "Sector 1",
 *     "health": "good",
 *     "images": []
 * }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST'); //in cross origin it will make sure browser will only accept post request.

try {
    // Get JSON payload
    $input = file_get_contents('php://input'); //this will read the entire json file, the application/json file in headers.
    $data = json_decode($input, true); //Convert JSON → PHP array

    /* Now we can use data from json like this
    $data['id'] or $data['sensorname'] can be anything we can get from this data
    */
    
    // Validate
    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid request - sensor_id required'
        ]);
        exit;
    }
    
    // In production, validate and insert into database
    // For now, return success to indicate API structure
    
    http_response_code(501);
    echo json_encode([
        'success' => false,
        'error' => 'API not implemented - using mock data'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
