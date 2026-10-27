
<?php
$message = "";
$messageType = "";
$name = "";
$email = "";
$mobile = "";
$course = "";

$dataFolder = __DIR__ . "/data";
$csvFile = $dataFolder . "/students.csv";
$jsonFile = $dataFolder . "/students.json";

if (!is_dir($dataFolder)) {
    mkdir($dataFolder, 0755, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Read and sanitize form inputs
    $name = trim(strip_tags($_POST["name"] ?? ""));
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim(strip_tags($_POST["course"] ?? ""));

    $errors = [];

    // Server-side validation
    if ($name === "" || strlen($name) < 2) {
        $errors[] = "Name must contain at least 2 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must contain exactly 10 digits.";
    }

    $allowedCourses = ["Computer Engineering", "Information Technology",
                       "AI and Data Science"];

    if (!in_array($course, $allowedCourses, true)) {
        $errors[] = "Please select a valid course.";
    }

    if (empty($errors)) {
        $student = [
            "name" => $name,
            "email" => $email,
            "mobile" => $mobile,
            "course" => $course,
            "date" => date("Y-m-d H:i:s")
        ];

        // Save the record in CSV
        $csvHandle = fopen($csvFile, "a");

        if ($csvHandle !== false) {
            if (flock($csvHandle, LOCK_EX)) {
                if (filesize($csvFile) === 0) {
                    fputcsv($csvHandle,
                        ["Name", "Email", "Mobile", "Course", "Date"]);
                }

                $csvSaved = fputcsv($csvHandle, array_values($student));
                flock($csvHandle, LOCK_UN);
            } else {
                $csvSaved = false;
            }

            fclose($csvHandle);
        } else {
            $csvSaved = false;
        }

        // Save the record in JSON
        $students = [];

        if (file_exists($jsonFile)) {
            $oldData = file_get_contents($jsonFile);
            $students = json_decode($oldData, true);

            if (!is_array($students)) {
                $students = [];
            }
        }

        $students[] = $student;

        $jsonData = json_encode(
            $students,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        $jsonSaved = false;

        if ($jsonData !== false) {
            $jsonSaved = file_put_contents(
                $jsonFile,
                $jsonData . PHP_EOL,
                LOCK_EX
            ) !== false;
        }

        if ($csvSaved && $jsonSaved) {
            $message = "Registration successful! Data saved in CSV and JSON.";
            $messageType = "success";

            $name = "";
            $email = "";
            $mobile = "";
            $course = "";
        } else {
            $message = "Storage error. Check folder permissions and saved files.";
            $messageType = "error";
        }
    } else {
        $message = implode(" ", $errors);
        $messageType = "error";
    }
}

// Read records for display
$students = [];

if (file_exists($jsonFile)) {
    $savedData = json_decode(file_get_contents($jsonFile), true);

    if (is_array($savedData)) {
        $students = $savedData;
    }
}

// Escape output before displaying it in HTML
function safeOutput($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Practical 7</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }

        header, main {
            max-width: 850px;
            margin: 20px auto;
        }

        header {
            background: #173b70;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }

        section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 5px;
        }

        button {
            background: #173b70;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 18px;
            cursor: pointer;
            border-radius: 5px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #dbeafe;
        }
    </style>
</head>

<body>
<header>
    <h1>StudentHub Portal</h1>
    <p>Practical 7: PHP Form Processing</p>
</header>

<main>
    <section>
        <h2>Student Registration Form</h2>

        <?php if ($message !== ""): ?>
            <p class="<?php echo safeOutput($messageType); ?>"
               role="status">
                <?php echo safeOutput($message); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name"
                   value="<?php echo safeOutput($name); ?>"
                   minlength="2" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   value="<?php echo safeOutput($email); ?>"
                   required>

            <label for="mobile">Mobile Number</label>
            <input type="text" id="mobile" name="mobile"
                   value="<?php echo safeOutput($mobile); ?>"
                   inputmode="numeric" pattern="[0-9]{10}"
                   maxlength="10" required>

            <label for="course">Course</label>
            <select id="course" name="course" required>
                <option value="">Select Course</option>

                <option value="Computer Engineering"
                    <?php if ($course === "Computer Engineering")
                        echo "selected"; ?>>
                    Computer Engineering
                </option>

                <option value="Information Technology"
                    <?php if ($course === "Information Technology")
                        echo "selected"; ?>>
                    Information Technology
                </option>

                <option value="AI and Data Science"
                    <?php if ($course === "AI and Data Science")
                        echo "selected"; ?>>
                    AI and Data Science
                </option>
            </select>

            <button type="submit">Register Student</button>
        </form>
    </section>

    <section>
        <h2>Registered Students</h2>

        <?php if (count($students) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Course</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo safeOutput($student["name"] ?? ""); ?></td>
                        <td><?php echo safeOutput($student["email"] ?? ""); ?></td>
                        <td><?php echo safeOutput($student["mobile"] ?? ""); ?></td>
                        <td><?php echo safeOutput($student["course"] ?? ""); ?></td>
                        <td><?php echo safeOutput($student["date"] ?? ""); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p>No students registered yet.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>


