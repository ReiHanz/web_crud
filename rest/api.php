<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

include_once '../database.php';
include_once '../disaster.php';

$database = new Database();
$db = $database->getConnection();

$disaster = new Disaster($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        handleGetRequest($disaster);
        break;
    case 'POST':
        handlePostRequest($disaster);
        break;
    case 'PUT':
        handlePutRequest($disaster);
        break;
    case 'DELETE':
        handleDeleteRequest($disaster);
        break;
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}

function handleGetRequest($disaster) {
    if(!empty($_GET["id"])) {
        $id = intval($_GET["id"]);
        $disaster->id = $id;
        $stmt = $disaster->read();
        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);

            $disaster_arr = array(
                "id" => $id,
                "name" => $name,
                "description" => $description,
                "date" => $date,
                "location" => $location
            );

            http_response_code(200);
            echo json_encode($disaster_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Disaster not found."));
        }
    } else {
        $stmt = $disaster->read();
        $num = $stmt->rowCount();

        if($num > 0) {
            $disasters_arr = array();
            $disasters_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $disaster_item = array(
                    "id" => $id,
                    "name" => $name,
                    "description" => $description,
                    "date" => $date,
                    "location" => $location
                );

                array_push($disasters_arr["records"], $disaster_item);
            }

            http_response_code(200);
            echo json_encode($disasters_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No disasters found."));
        }
    }
}

function handlePostRequest($disaster) {
    $data = json_decode(file_get_contents("php://input"));

    if(
        !empty($data->name) &&
        !empty($data->description) &&
        !empty($data->date) &&
        !empty($data->location)
    ) {
        $disaster->name = $data->name;
        $disaster->description = $data->description;
        $disaster->date = $data->date;
        $disaster->location = $data->location;

        if($disaster->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Disaster was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create disaster."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create disaster. Data is incomplete."));
    }
}

function handlePutRequest($disaster) {
    $data = json_decode(file_get_contents("php://input"));

    if(
        !empty($data->id) &&
        !empty($data->name) &&
        !empty($data->description) &&
        !empty($data->date) &&
        !empty($data->location)
    ) {
        $disaster->id = $data->id;
        $disaster->name = $data->name;
        $disaster->description = $data->description;
        $disaster->date = $data->date;
        $disaster->location = $data->location;

        if($disaster->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Disaster was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update disaster."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to update disaster. Data is incomplete."));
    }
}

function handleDeleteRequest($disaster) {
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->id)) {
        $disaster->id = $data->id;

        if($disaster->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Disaster was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete disaster."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to delete disaster. Data is incomplete."));
    }
}
?>
