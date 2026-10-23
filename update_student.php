<?php
include 'db_connect.php';

// Get student_id from URL (example: update_student.php?student_id=1)
$student_id = $_GET['student_id'];

$result = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'");
$student = $result->fetch_assoc();

// Function to format date in 'Day Month Year' format with integers (e.g., "28 04 2025")
function format_date($date) {
    $date_obj = DateTime::createFromFormat('Y-m-d', $date); // Assuming the format is 'Y-m-d'
    return $date_obj ? $date_obj->format('d m Y') : $date; // Format as 'Day Month Year' with integers
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
        }
        .form-container {
            width: 500px;
            margin: 50px auto;
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button, .view-button {
            padding: 12px 25px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .view-button {
            background: blue;
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        .success-message {
            width: 500px;
            margin: 30px auto;
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
        .error-message {
            width: 500px;
            margin: 30px auto;
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
    </style>
</head>
<body>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $class = $_POST['class'];
    $section = $_POST['section'];
    $dob = $_POST['dob'];
    $parent_phone = $_POST['parent_phone'];
    $address = $_POST['address'];

    $update = $conn->query("UPDATE students SET
        first_name = '$first_name',
        last_name = '$last_name',
        class = '$class',
        section = '$section',
        dob = '$dob',
        parent_phone = '$parent_phone',
        address = '$address'
        WHERE student_id = '$student_id'
    ");

    if ($update) {
        echo '<div class="success-message">Student details updated successfully!</div>';
    } else {
        echo '<div class="error-message">Failed to update student details.</div>';
    }
}
?>

<div class="form-container">
    <h2>Update Student Details</h2>
    <form method="POST" action="">
        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
        
        <label>First Name:</label>
        <input type="text" name="first_name" value="<?php echo $student['first_name']; ?>" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?php echo $student['last_name']; ?>" required>

        <label>Class:</label>
        <input type="text" name="class" value="<?php echo $student['class']; ?>" required>

        <label>Section:</label>
        <input type="text" name="section" value="<?php echo $student['section']; ?>" required>

        <label>Date of Birth:</label>
        <!-- Display date picker with correct format for input -->
        <input type="date" name="dob" value="<?php echo $student['dob']; ?>" required>

        <label>Parent Phone:</label>
        <input type="text" name="parent_phone" value="<?php echo $student['parent_phone']; ?>" required>

        <label>Address:</label>
        <input type="text" name="address" value="<?php echo $student['address']; ?>" required>

        <div class="button-group">
            <button type="submit">Save Changes</button>
            <a href="view.php" class="view-button">Go to View Page</a>
        </div>
    </form>
</div>

</body>
</html>