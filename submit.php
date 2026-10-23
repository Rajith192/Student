<?php
include 'db_connect.php';

function calculateGrade($total) {
    if ($total >= 90) return 'A+';
    elseif ($total >= 80) return 'A';
    elseif ($total >= 70) return 'B';
    elseif ($total >= 60) return 'C';
    elseif ($total >= 50) return 'D';
    else return 'F';
}

function formatDate($date) {
    return !empty($date) ? date('d-m-Y', strtotime($date)) : 'N/A';
}

$showGrades = isset($_POST['calculate_grade']);
$updateDetails = isset($_POST['update_details']);
$saveDetails = isset($_POST['save_details']);
$error_message = "";

// Get form data
$student_id = $_POST['student_id'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$class = $_POST['class'] ?? '';
$section = $_POST['section'] ?? '';
$dob = $_POST['dob'] ?? '';
$parent_phone = $_POST['parent_phone'] ?? '';
$address = $_POST['address'] ?? '';

$course_ids = $_POST['course_id'] ?? [];
$subject_names = $_POST['subject_name'] ?? [];
$internal_marks = $_POST['internal_mark'] ?? [];
$final_marks = $_POST['final_mark'] ?? [];

// 1. Insert student and courses
$studentExists = false;
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$showGrades && !$updateDetails && !$saveDetails) {
    // Check if student already exists
    $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $check_stmt->bind_param("s", $student_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error_message = "Error: Student ID already exists!";
        $studentExists = true;
    } else {
        $sql_student = "INSERT INTO students (student_id, first_name, last_name, class, section, dob, parent_phone, address)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_student);
        $stmt->bind_param("ssssssss", $student_id, $first_name, $last_name, $class, $section, $dob, $parent_phone, $address);
        $stmt->execute();
        $stmt->close();

        $sql_course = "INSERT INTO courses (student_id, course_id, subject_name, internal_mark, final_mark)
                       VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_course);
        for ($i = 0; $i < count($course_ids); $i++) {
            $stmt->bind_param("sssii", $student_id, $course_ids[$i], $subject_names[$i], $internal_marks[$i], $final_marks[$i]);
            $stmt->execute();
        }
        $stmt->close();
    }
    $check_stmt->close();
}

// 2. Update marks
if ($saveDetails) {
    $update_stmt = $conn->prepare("UPDATE courses SET internal_mark = ?, final_mark = ? WHERE student_id = ? AND course_id = ?");
    for ($i = 0; $i < count($course_ids); $i++) {
        $internal = (int)$internal_marks[$i];
        $final = (int)$final_marks[$i];
        $cid = $course_ids[$i];
        $update_stmt->bind_param("iiss", $internal, $final, $student_id, $cid);
        $update_stmt->execute();
    }
    $update_stmt->close();
}

// 3. Calculate and save final grade
if ($showGrades && !$updateDetails) {
    $totalMarks = 0;
    $maxMarks = count($course_ids) * 100;

    for ($i = 0; $i < count($course_ids); $i++) {
        $internal = (float)$internal_marks[$i];
        $final = (float)$final_marks[$i];
        $totalMarks += ($internal + $final);
    }

    $percentage = ($totalMarks / $maxMarks) * 100;
    $averageMark = $totalMarks / count($course_ids);
    $grade = calculateGrade($averageMark);
    $result_status = ($percentage >= 85) ? "Distinction" :
                     (($percentage >= 75) ? "First Class" :
                     (($percentage >= 60) ? "Second Class" :
                     (($percentage >= 50) ? "Third Class" : "Fail")));

    $check_result = $conn->prepare("SELECT student_id FROM student_results WHERE student_id = ?");
    $check_result->bind_param("s", $student_id);
    $check_result->execute();
    $check_result->store_result();

    if ($check_result->num_rows > 0) {
        $update_result = $conn->prepare("UPDATE student_results SET total_marks = ?, percentage = ?, grade = ?, result_status = ? WHERE student_id = ?");
        $update_result->bind_param("idsss", $totalMarks, $percentage, $grade, $result_status, $student_id);
        $update_result->execute();
        $update_result->close();
    } else {
        $insert_result = $conn->prepare("INSERT INTO student_results (student_id, total_marks, percentage, grade, result_status) VALUES (?, ?, ?, ?, ?)");
        $insert_result->bind_param("sidss", $student_id, $totalMarks, $percentage, $grade, $result_status);
        $insert_result->execute();
        $insert_result->close();
    }

    $check_result->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Grade Report</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #80deea);
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 1000px;
            margin: auto;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #00796b;
        }
        .student-info p {
            margin: 8px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #0097a7;
            color: white;
        }
        td input[type="number"] {
            width: 70px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            display: block;
            width: 250px;
            margin: 25px auto 0;
            padding: 14px;
            font-size: 16px;
            background-color: #00796b;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #004d40;
        }
        .final-result {
            background-color: #e0f2f1;
            border-left: 6px solid #00796b;
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
        }
        .final-result h3 {
            margin: 0 0 10px;
        }
        .btn-link {
            text-align: center;
            margin-top: 40px;
        }
        .btn-link a, .btn-link button {
            padding: 12px 20px;
            background-color: #0097a7;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-link a:hover, .btn-link button:hover {
            background-color: #006064;
        }
        .error {
            background-color: #ffdddd;
            color: #d8000c;
            border-left: 6px solid #f44336;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Grade Report</h2>

    <?php if (!empty($error_message)): ?>
        <div class="error"><?= $error_message ?></div>
    <?php endif; ?>

    <?php if (!$studentExists): ?>
    <div class="student-info">
        <p><strong>ID:</strong> <?= htmlspecialchars($student_id) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($first_name . ' ' . $last_name) ?></p>
        <p><strong>Class:</strong> <?= htmlspecialchars($class) ?> <strong>Section:</strong> <?= htmlspecialchars($section) ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars(formatDate($dob)) ?></p>
        <p><strong>Parent Phone:</strong> <?= htmlspecialchars($parent_phone) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($address) ?></p>
    </div>

    <form method="post">
        <?php
        foreach (['student_id', 'first_name', 'last_name', 'class', 'section', 'dob', 'parent_phone', 'address'] as $field) {
            echo "<input type='hidden' name='$field' value='" . htmlspecialchars($$field) . "'>";
        }

        echo "<table><tr>
                <th>Course ID</th>
                <th>Subject</th>
                <th>Internal</th>
                <th>Final</th>";
        if ($showGrades && !$updateDetails) echo "<th>Total</th><th>Grade</th>";
        echo "</tr>";

        $totalMarks = 0;
        $maxMarks = 0;

        for ($i = 0; $i < count($course_ids); $i++) {
            $internal = (float)$internal_marks[$i];
            $final = (float)$final_marks[$i];
            $total = $internal + $final;
            $grade = calculateGrade($total);
            $totalMarks += $total;
            $maxMarks += 100;

            echo "<tr>
                    <td><input type='hidden' name='course_id[]' value='" . htmlspecialchars($course_ids[$i]) . "'>" . htmlspecialchars($course_ids[$i]) . "</td>
                    <td><input type='hidden' name='subject_name[]' value='" . htmlspecialchars($subject_names[$i]) . "'>" . htmlspecialchars($subject_names[$i]) . "</td>";

            if ($updateDetails) {
                echo "<td><input type='number' name='internal_mark[]' value='$internal' min='0' max='20' required></td>
                      <td><input type='number' name='final_mark[]' value='$final' min='0' max='80' required></td>";
            } else {
                echo "<td><input type='hidden' name='internal_mark[]' value='$internal'>$internal</td>
                      <td><input type='hidden' name='final_mark[]' value='$final'>$final</td>";
            }

            if ($showGrades && !$updateDetails) {
                echo "<td>$total</td><td>$grade</td>";
            }

            echo "</tr>";
        }
        echo "</table>";

        if ($showGrades && !$updateDetails) {
            $percentage = ($totalMarks / $maxMarks) * 100;
            $finalResult = ($percentage >= 85) ? "Distinction" :
                           (($percentage >= 75) ? "First Class" :
                           (($percentage >= 60) ? "Second Class" :
                           (($percentage >= 50) ? "Third Class" : "Fail")));

            echo "<div class='final-result'>
                    <h3>Final Result</h3>
                    <p><strong>Total Marks:</strong> $totalMarks / $maxMarks</p>
                    <p><strong>Percentage:</strong> " . number_format($percentage, 2) . "%</p>
                    <p><strong>Overall Result:</strong> <span style='font-weight: bold; color: " . ($finalResult === 'Fail' ? 'red' : 'green') . ";'>$finalResult</span></p>
                  </div>";
        }
        ?>

        <?php if ($showGrades && !$updateDetails): ?>
            <button type="submit" name="update_details" class="btn">Update Details</button>
        <?php elseif ($updateDetails): ?>
            <button type="submit" name="save_details" class="btn">Save</button>
        <?php else: ?>
            <button type="submit" name="calculate_grade" class="btn">Calculate Grade</button>
        <?php endif; ?>
    </form>
    <?php endif; ?>

    <div class="btn-link">
        <a href="admin_dashboard.html">Go to Admin Dashboard</a><br><br>
        <!--<form action="submit.php" method="get" style="display:inline;">
            <button type="submit">Go to Submit Page</button>
        </form>-->
    </div>
</div>
</body>
</html>