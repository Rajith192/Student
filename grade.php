<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_name = $_POST['student_name'];
    $register_number = $_POST['register_number'];
    $class = $_POST['class'];
    $section = $_POST['section'];
    
    $subjects = $_POST['subjects'];
    $total_marks = 0;
    $total_subjects = count($subjects);
    $failed = false;
    $grades = [];
    
    foreach ($subjects as $subject => $marks) {
        $marks = (int)$marks;
        $total_marks += $marks;
        
        if ($marks >= 80) {
            $grade = "Excellent";
        } elseif ($marks >= 65) {
            $grade = "First Grade";
        } elseif ($marks >= 50) {
            $grade = "Second Grade";
        } elseif ($marks >= 35) {
            $grade = "Third Grade";
        } else {
            $grade = "Fail";
            $failed = true;
        }
        $grades[$subject] = $grade;
    }
    
    $percentage = $total_marks / $total_subjects;
    
    if ($failed) {
        $final_result = "Fail";
    } else {
        if ($percentage >= 80) {
            $final_result = "Distinction";
        } elseif ($percentage >= 60) {
            $final_result = "First Class";
        } elseif ($percentage >= 50) {
            $final_result = "Second Class";
        } elseif ($percentage >= 35) {
            $final_result = "Third Class";
        } else {
            $final_result = "Fail";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Grade Calculator</title>
    <script>
        function addSubject() {
            let subjectDiv = document.createElement("div");
            subjectDiv.innerHTML = '<input type="text" name="subjects[]" placeholder="Subject Name" required> <input type="number" name="marks[]" placeholder="Marks" required>';
            document.getElementById("subjects").appendChild(subjectDiv);
        }
    </script>
</head>
<body>
    <h2>Student Grade Calculator</h2>
    <form method="post">
        Name: <input type="text" name="student_name" required><br>
        Register Number: <input type="text" name="register_number" required><br>
        Class: <input type="text" name="class" required><br>
        Section: <input type="text" name="section" required><br>
        
        <h3>Subjects</h3>
        <div id="subjects">
            <div>
                <input type="text" name="subjects[]" placeholder="Subject Name" required>
                <input type="number" name="marks[]" placeholder="Marks" required>
            </div>
        </div>
        <button type="button" onclick="addSubject()">Add Subject</button>
        <br><br>
        <input type="submit" value="Calculate Grade">
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
        <h3>Result</h3>
        <p>Name: <?php echo $student_name; ?></p>
        <p>Register Number: <?php echo $register_number; ?></p>
        <p>Class: <?php echo $class; ?></p>
        <p>Section: <?php echo $section; ?></p>
        <h4>Subject Grades:</h4>
        <ul>
            <?php foreach ($grades as $subject => $grade) {
                echo "<li>$subject: $grade</li>";
            } ?>
        </ul>
        <p>Total Marks: <?php echo $total_marks; ?></p>
        <p>Percentage: <?php echo number_format($percentage, 2); ?>%</p>
        <p>Final Result: <?php echo $final_result; ?></p>
    <?php } ?>
</body>
</html>
