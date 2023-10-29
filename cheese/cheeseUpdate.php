<!DOCTYPE html>
<html>
<head>
    <link rel='icon' type='image/x-icon' href='../spearers.png'>
    <title>Contact List</title>
</head>
<body>
    <h1><img src='../images/SpearersLogoClearWhite.png' height='80'>Timelord Page</h1>
    <h2>The Spearers</h2>

    <?php
        // Database connection details
        $servername = "localhost";  //:3306
        $username = "westends_admin";
        $password = "Nanana11!!";
        $dbname = "westends_cheese";
    
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
    
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Fetch Spearers records from the database
        $sql = "SELECT * FROM Spearers ORDER BY handicap";
        $result = $conn->query($sql);
        
        //echo json_encode($result->fetch_all());
        
        if ($result->num_rows > 0) {
            echo "<table id='spearers'>";
            echo "<tr><th>Name</th><th>H-cap</th><th>Comment</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr data-id='" . $row['id'] . "'>";
                //echo "<td><input type='text' size='3' value='".$row['id']."' readonly></td>";
                echo "<td><input type='text' name='name' size='15' value='".$row['name']."' oninput='this.style.backgroundColor=\"#FFCCCB\";'></td>";
                echo "<td><input type='text' name='handicap' size='4' value='".$row['handicap']."' oninput='this.style.backgroundColor=\"#FFCCCB\";'></td>";
                echo "<td><input type='text' name='comment' size='18' value='".$row['comment']."' oninput='this.style.backgroundColor=\"#FFCCCB\";'></td>";
                echo "<td><button class='save-btn'>Save</button></td>";
                echo "<td><button class='remove-btn'>Remove</button></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No records found.";
        }

    ?>
    
    <br>
    <form action="cheese_add_record.php" method="post">
        <table><tr>
            <!-- <td><input type="text" size="3" name="id" placeholder="ID" readonly></td> -->
            <td><input type="text" size="15" name="name" placeholder="Name" required oninput="this.style.backgroundColor='#FFCCCB';"></td>
            <td><input type="text" size="4" name="handicap" placeholder="Handicap" oninput="this.style.backgroundColor='#FFCCCB';"></td>
            <td><input type="text" size="18" name="comment" placeholder="Comment" oninput="this.style.backgroundColor='#FFCCCB';"></td>
            <td><button type="submit">Add Record</button></td>
        </tr></table>
    </form>
    <h2>Events</h2>
    
    <?php
        // Fetch Events records from the database
        $sql = "SELECT * FROM Events ORDER BY date";
        $result = $conn->query($sql);
        
        //echo json_encode($result->fetch_all());
        
        if ($result->num_rows > 0) {
            echo "<table id='events'>";
            echo "<tr><th>Date</th><th>Event</th><th>Comment</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr data-id='" . $row['id'] . "'>";
                //echo "<td><input type='text' size='3' value='".$row['id']."' readonly></td>";
                echo "<td><input type='text' name='date' size='10' value='".$row['date']."' oninput='this.style.backgroundColor=\"#FFCCCB\";'></td>";
                echo "<td><input type='text' name='event' size='15' value='".$row['event']."' oninput='this.style.backgroundColor=\"#FFCCCB\";'></td>";
                echo "<td><input type='text' name='evcomment' size='15' value='".$row['evcomment']."' oninput='this.style.backgroundColor=\"#FFCCCB\";'></td>";
                echo "<td><button class='save-btn'>Save</button></td>";
                echo "<td><button class='remove-btn'>Remove</button></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No records found.";
        }
    
        $conn->close();
    ?>
    
    <br>
    <form action="events_add_record.php" method="post">
        <table><tr>
            <!-- <td><input type="text" size="3" name="id" placeholder="ID" readonly></td> -->
            <td><input type="text" size="10" name="date" placeholder="Date" required oninput="this.style.backgroundColor='#FFCCCB';"></td>
            <td><input type="text" size="15" name="event" placeholder="Event" oninput="this.style.backgroundColor='#FFCCCB';"></td>
            <td><input type="text" size="15" name="evcomment" placeholder="Comment" oninput="this.style.backgroundColor='#FFCCCB';"></td>
            <td><button type="submit">Add Record</button></td>
        </tr></table>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Handle Spearers Save button click
            $("#spearers").on("click", ".save-btn", function () {
                var row = $(this).closest("tr");
                var name = row.find("input[name='name']").val();
                var handicap = row.find("input[name='handicap']").val();
                var comment = row.find("input[name='comment']").val();
                var id = row.attr("data-id");  //row.find("input[name='id']").val();
    
                $.post("cheese_update_record.php", {
                    id: id,
                    name: name,
                    handicap: handicap,
                    comment: comment
                }, function (data) {
                    if (data === "success") {
                        row.find("input[name='name']").css("background-color", "transparent");
                        row.find("input[name='handicap']").css("background-color", "transparent");
                        row.find("input[name='comment']").css("background-color", "transparent");
                        //alert("Record " + id + " updated successfully.");
                    } else {
                        alert("Failed to update record " + id + ".");
                    }
                });
            });
    
            // Handle Spearers Remove button click
            $("#spearers").on("click", ".remove-btn", function () {
                var row = $(this).closest("tr");
                var id = row.attr("data-id");
    
                $.post("cheese_remove_record.php", { id: id }, function (data) {
                    if (data === "success") {
                        row.remove();
                        //alert("Record " + id + " removed successfully.");
                    } else {
                        alert("Failed to remove record " + id + ".");
                    }
                });
            });

            // Handle Events Save button click
            $("#events").on("click", ".save-btn", function () {
                var row = $(this).closest("tr");
                var date = row.find("input[name='date']").val();
                var event = row.find("input[name='event']").val();
                var evcomment = row.find("input[name='evcomment']").val();
                var id = row.attr("data-id");  //row.find("input[name='id']").val();
    
                $.post("events_update_record.php", {
                    id: id,
                    date: date,
                    event: event,
                    evcomment: evcomment
                }, function (data) {
                    if (data === "success") {
                        row.find("input[name='date']").css("background-color", "transparent");
                        row.find("input[name='event']").css("background-color", "transparent");
                        row.find("input[name='evcomment']").css("background-color", "transparent");
                        //alert("Record " + id + " updated successfully.");
                    } else {
                        alert("Failed to update record " + id + ".");
                    }
                });
            });
    
            // Handle Events Remove button click
            $("#events").on("click", ".remove-btn", function () {
                var row = $(this).closest("tr");
                var id = row.attr("data-id");
    
                $.post("events_remove_record.php", { id: id }, function (data) {
                    if (data === "success") {
                        row.remove();
                        //alert("Record " + id + " removed successfully.");
                    } else {
                        alert("Failed to remove record " + id + ".");
                    }
                });
            });

        });
    </script>
    <h2><a href="../getTides.php">Update tides_westend.js</a></h2>
    <p>(click above link once per year)</p>
    <h2><a href="../cheese.php#athletes">Back to westendspearers.com.au</a></h2>

</body>
</html>
