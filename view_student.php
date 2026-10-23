<?php
include 'db_connect.php';

// Get student ID
$student_id = $_GET['student_id'] ?? '';
if (!$student_id) {
    die("No Student ID provided.");
}

// Fetch student basic info
$student_query = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'");
if ($student_query->num_rows === 0) {
    echo "
    <div style='padding: 20px; margin: 50px auto; max-width: 400px; text-align: center; border: 1px solid #ccc; border-radius: 8px; font-family: Arial, sans-serif;'>
        <h2 style='color: red;'>Student ID Not Found</h2>
        <p>The student ID <strong>$student_id</strong> does not exist in our records.</p>
        <a href='user_dashboard.html' style='display: inline-block; padding: 10px 20px; margin-top: 10px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 4px;'>Back</a>
    </div>
    ";
    exit;
}
$student = $student_query->fetch_assoc();
;

// Fetch subjects
$subjects = $conn->query("SELECT * FROM courses WHERE student_id = '$student_id'");

// Function to format date in 'Day Month Year' format with integers (e.g., "28 04 2025")
function format_date($date) {
    $date_obj = DateTime::createFromFormat('Y-m-d', $date); // Assuming the format is 'Y-m-d'
    return $date_obj ? $date_obj->format('d-m-Y') : $date; // Format as 'Day Month Year' with integers
}

// Calculate Total Marks, Percentage, Grade
$total_marks = 0;
$total_max_marks = 0;
$subject_rows = [];

while ($sub = $subjects->fetch_assoc()) {
    $internal = $sub['internal_mark'];
    $final = $sub['final_mark'];
    $subject_total = $internal + $final;
    $grade = '';

    // Calculate Grade
    $percentage = ($subject_total / 100) * 100; // assuming each subject is out of 100
    if ($percentage >= 90) {
        $grade = 'A+';
    } elseif ($percentage >= 80) {
        $grade = 'A';
    } elseif ($percentage >= 70) {
        $grade = 'B';
    } elseif ($percentage >= 60) {
        $grade = 'C';
    } elseif ($percentage >= 50) {
        $grade = 'D';
    } else {
        $grade = 'F';
    }

    $subject_rows[] = [
        'course_id' => $sub['course_id'],
        'subject_name' => $sub['subject_name'],
        'internal_mark' => $internal,
        'final_mark' => $final,
        'total' => $subject_total,
        'grade' => $grade,
    ];

    $total_marks += $subject_total;
    $total_max_marks += 100; // Assuming each subject is out of 100
}

// Final Percentage
$overall_percentage = ($total_max_marks > 0) ? ($total_marks / $total_max_marks) * 100 : 0;

// Overall Result
$overall_result = '';
if ($overall_percentage >= 85) {
    $overall_result = 'Distinction';
} elseif ($overall_percentage >= 75) {
    $overall_result = 'First Class';
} elseif ($overall_percentage >= 60) {
    $overall_result = 'Second Class';
} elseif ($overall_percentage >= 50) {
    $overall_result = 'Third Class';
} else {
    $overall_result = 'Fail';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Info</title>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        min-height: 100vh;
        margin: 0;
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .container {
        background: #ffffff;
        padding: 40px;
        width: 85%;
        max-width: 1000px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    h2 {
        text-align: center;
        color: #4a148c;
        margin-bottom: 30px;
        font-size: 32px;
    }
    p {
        font-size: 16px;
        margin: 5px 0;
        color: #333;
    }
    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        background: #fafafa;
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        border: 1px solid #b2dfdb;
        padding: 12px;
        text-align: center;
    }
    th {
        background: #00897b;
        color: white;
        font-size: 16px;
    }
    td {
        background: #e0f2f1;
        font-weight: 500;
    }
    .result-summary {
        margin-top: 30px;
        background: #f1f8e9;
        padding: 25px;
        border-radius: 12px;
        box-shadow: inset 0 0 10px #c8e6c9;
    }
    .result-summary p {
        font-size: 18px;
        margin: 10px 0;
        color: #33691e;
        font-weight: bold;
    }
    .buttons {
        margin-top: 30px;
        text-align: center;
    }
    .buttons button {
        background-color: #4a148c;
        border: none;
        padding: 12px 25px;
        margin: 10px;
        color: white;
        font-size: 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .buttons button:hover {
        background-color: #6a1b9a;
    }
    .success {
        color: green;
        font-weight: bold;
    }
    .fail {
        color: red;
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="container" id="reportContent">
    <h2>Student Grade Report</h2>

    <div>
        <p><strong>ID:</strong> <?= htmlspecialchars($student['student_id']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></p>
        <p><strong>Class:</strong> <?= htmlspecialchars($student['class']) ?> <strong>Section:</strong> <?= htmlspecialchars($student['section']) ?></p>
        <p><strong>Date of Birth:</strong> <?= format_date($student['dob']) ?></p> <!-- Date formatted here -->
        <p><strong>Parent Phone:</strong> <?= htmlspecialchars($student['parent_phone']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Subject</th>
                <th>Internal</th>
                <th>Final</th>
                <th>Total</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($subject_rows as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['course_id']) ?></td>
                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                <td><?= htmlspecialchars($row['internal_mark']) ?></td>
                <td><?= htmlspecialchars($row['final_mark']) ?></td>
                <td><?= htmlspecialchars($row['total']) ?></td>
                <td><?= htmlspecialchars($row['grade']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="result-summary">
        <h3>Final Result</h3>
        <p><strong>Total Marks:</strong> <?= $total_marks ?> / <?= $total_max_marks ?></p>
        <p><strong>Percentage:</strong> <?= number_format($overall_percentage, 2) ?>%</p>
        <p><strong>Overall Result:</strong> 
            <span class="<?= $overall_result == 'Fail' ? 'fail' : 'success' ?>">
                <?= $overall_result ?>
            </span>
        </p>
    </div>

    <div class="buttons">
        <button type="button" onclick="downloadPDF()">Download PDF</button>

        <form action="user_dashboard.html" method="GET" style="display:inline;">
            <button type="submit">Go to User Dashboard</button>
        </form>
    </div>
</div>

<!-- Include html2pdf.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    // Hide the buttons
    var buttons = document.querySelector('.buttons');
    buttons.style.display = 'none';

    var element = document.getElementById('reportContent');
    var opt = {
        margin:       0.5,
        filename:     'Student_Report_<?= htmlspecialchars($student['student_id']) ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save()
    .then(() => {
        // Show the buttons again after saving
        buttons.style.display = 'block';
    });
}
</script>

</body>
</html>