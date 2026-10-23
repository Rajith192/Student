<?php
include 'db_connect.php';

// Get student_id from POST or GET
$student_id = $_POST['student_id'] ?? $_GET['student_id'] ?? '';

if (empty($student_id)) {
    die("Error: Student ID not provided.");
}

// Start a transaction to ensure all deletions are successful
$conn->begin_transaction();

try {
    // Delete from courses
    $stmt = $conn->prepare("DELETE FROM courses WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->close();

    // Delete from student_results
    $stmt = $conn->prepare("DELETE FROM student_results WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->close();

    // Delete from students
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("No student found with ID '$student_id'.");
    }

    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo "<script>alert('Student deleted successfully'); window.location.href = 'view.php';</script>";

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo "<script>alert('Error deleting student: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
}

$conn->close();
?>
