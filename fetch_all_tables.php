<?php
// Database configuration
$host = 'localhost'; // Default host
$dbname = 'travel_form'; // Replace with your database name
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password for XAMPP

try {
    // Establish a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if update form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        $column = $_POST['column'];
        $value = $_POST['value'];

        // Update query
        $updateQuery = "UPDATE `$table` SET `$column` = :value WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "Data updated successfully in table $table.<br>";
        } else {
            echo "Failed to update data.<br>";
        }
    }

    // Check if delete form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        $column = $_POST['column'];

        if ($column === 'ALL') {
            // Delete the entire row
            $deleteQuery = "DELETE FROM `$table` WHERE id = :id";
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                echo "Row deleted successfully from table $table.<br>";
            } else {
                echo "Failed to delete row.<br>";
            }
        } else {
            // Delete only the specific column's data (set it to NULL)
            $deleteQuery = "UPDATE `$table` SET `$column` = NULL WHERE id = :id";
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                echo "Column $column deleted successfully from table $table.<br>";
            } else {
                echo "Failed to delete column.<br>";
            }
        }
    }

    // Fetch all table names in the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "No tables found in the database.";
        exit;
    }

    // Loop through all tables and fetch data
    foreach ($tables as $table) {
        echo "<h2>Data from table: $table</h2>";

        // Prepare and execute the query
        $query = "SELECT * FROM `$table`";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Fetch all rows
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            echo "No data found in table $table.<br>";
            continue;
        }

        // Display table data in an HTML table
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>";
        foreach (array_keys($rows[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>$cell</td>";
            }
            echo "</tr>";
        }
        echo "</table><br>";

        // Add a form to update data in this table
        echo "<h3>Update Data in $table</h3>";
        echo "
        <form method='post'>
            <input type='hidden' name='table' value='$table'>
            <label for='id'>ID:</label>
            <input type='number' name='id' required>
            <label for='column'>Column:</label>
            <input type='text' name='column' required>
            <label for='value'>New Value:</label>
            <input type='text' name='value' required>
            <button type='submit' name='update'>Update</button>
        </form>
        <br>";

        // Add a form to delete data in this table
        echo "<h3>Delete Data in $table</h3>";
        echo "
        <form method='post'>
            <input type='hidden' name='table' value='$table'>
            <label for='id'>ID:</label>
            <input type='number' name='id' required>
            <label for='column'>Column:</label>
            <input type='text' name='column' required>
            <button type='submit' name='delete'>Delete</button>
        </form>
        <br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
