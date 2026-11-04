
<?php
require_once "auth.php";
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimi i API - Student Hub</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <span>Student</span> Hub
            </div>
            <a href="dashboard.php" class="login-btn">Kthehu</a>
        </nav>
    </header>

    <main class="dashboard">
        <h1>Testimi i API</h1>
        <p>Lendet e marra nga databaza permes API:</p>

        <div class="dashboard-grid" id="coursesList"></div>
    </main>

    <script>
        fetch("api_courses.php")
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById("coursesList");

                data.courses.forEach(course => {
                    const card = document.createElement("div");
                    card.className = "dashboard-card";

                    const title = document.createElement("h3");
                    title.textContent = course.name;

                    const code = document.createElement("p");
                    code.textContent = "Kodi: " + course.code;

                    const credits = document.createElement("p");
                    credits.textContent = "ECTS: " + course.credits;

                    const semester = document.createElement("p");
                    semester.textContent = "Semestri: " + course.semester;

                    card.append(title, code, credits, semester);
                    list.appendChild(card);
                });
            })
            .catch(error => {
                console.error("Gabim ne API:", error);
                document.getElementById("coursesList").textContent = "Nuk u ngarkuan lendet.";
            });
    </script>
</body>
</html>
