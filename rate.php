<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["id"]);
    $rating = intval($_POST["rating"]);

    if ($rating >= 1 && $rating <= 5) {
        $sql = "UPDATE recipe SET rating = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $rating, $id);

        if ($stmt->execute()) {
            header("Location: index.php?msg=Rating updated successfully");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid rating value";
    }
}

$conn->close();
?>
