<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, #e0eafc, #cfdef3, #a1c4fd, #c2e9fb);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 0;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h2 {
            color: #222;
            font-size: 30px;
            margin-bottom: 25px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 360px;
        }

        input[type="text"] {
            width: 90%;
            padding: 10px 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.3);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px 0;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
            transform: translateY(-2px);
        }

        .back-button {
            display: inline-block;
            margin-top: 15px;
            background: #444;
            width: 100%;
            max-width: 360px;
            padding: 12px 0;
            color: white;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background: #222;
            transform: translateY(-2px);
        }

        @media (max-width: 400px) {
            form, .back-button {
                width: 100%;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <h2>🔍 View Student Record</h2>
    <form method="GET" action="student_details.php">
        <input type="text" name="student_id" required placeholder="Enter Student ID">
        <button type="submit">View ➡</button>
    </form>

    <a class="back-button" href="admin_dashboard.html">← Back to Dashboard</a>

</body>
</html>
