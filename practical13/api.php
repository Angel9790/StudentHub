
<?php
header("Content-Type: application/json; charset=UTF-8");

require __DIR__ . "/db.php";

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function getInput() {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        respond([
            "success" => false,
            "message" => "Invalid JSON data."
        ], 400);
    }

    return $input;
}

function validId($value) {
    $id = filter_var($value, FILTER_VALIDATE_INT);
    return ($id !== false && $id > 0) ? $id : false;
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $result = $conn->query(
        "SELECT id, name, email, course
         FROM students
         ORDER BY id DESC"
    );

    if (!$result) {
        respond(["success" => false, "message" => "Could not load students."], 500);
    }

    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    respond([
        "success" => true,
        "data" => $students
    ]);
}

/* CREATE: Add student */
if ($method === "POST") {
    $data = getInput();

    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $course = trim($data["course"] ?? "");

    if ($name === "" || strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150 ||
        $course === "" || strlen($course) > 100) {
        respond([
            "success" => false,
            "message" => "Enter a valid name, email, and course."
        ], 400);
    }

    $stmt = $conn->prepare(
        "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $name, $email, $course);

    if (!$stmt->execute()) {
        $stmt->close();
        respond(["success" => false, "message" => "Could not add student."], 500);
    }

    $stmt->close();

    respond([
        "success" => true,
        "message" => "Student added successfully."
    ], 201);
}

if ($method === "PUT") {
    $data = getInput();

    $id = validId($data["id"] ?? null);
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $course = trim($data["course"] ?? "");

    if (!$id || $name === "" || strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150 ||
        $course === "" || strlen($course) > 100) {
        respond([
            "success" => false,
            "message" => "Enter a valid ID, name, email, and course."
        ], 400);
    }

    $check = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();

    if (!$exists) {
        respond(["success" => false, "message" => "Student not found."], 404);
    }

    $stmt = $conn->prepare(
        "UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?"
    );

    $stmt->bind_param("sssi", $name, $email, $course, $id);

    if (!$stmt->execute()) {
        $stmt->close();
        respond(["success" => false, "message" => "Could not update student."], 500);
    }

    $stmt->close();

    respond([
        "success" => true,
        "message" => "Student updated successfully."
    ]);
}

if ($method === "DELETE") {
    $data = getInput();
    $id = validId($data["id"] ?? null);

    if (!$id) {
        respond([
            "success" => false,
            "message" => "Enter a valid student ID."
        ], 400);
    }

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $deleted = $stmt->affected_rows;
    $stmt->close();

    if ($deleted === 0) {
        respond(["success" => false, "message" => "Student not found."], 404);
    }

    respond([
        "success" => true,
        "message" => "Student deleted successfully."
    ]);
}

header("Allow: GET, POST, PUT, DELETE");

respond([
    "success" => false,
    "message" => "Request method not allowed."
], 405);
?>
