<?php
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$nameErr = $prepTimeErr = $difficultyErr = $vegetarianErr = "";
$name = $prepTime = $difficulty = $vegetarian = "";

if ($id) {
    $sql = "SELECT * FROM recipe WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
    $stmt->close();

    if ($recipe) {
        $name = $recipe["name"];
        $prepTime = $recipe["prep_time"];
        $difficulty = $recipe["difficulty"];
        $vegetarian = $recipe["vegetarian"];
    } else {
        echo "Recipe not found";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
        $isValid = false;
    } else {
        $name = test_input($_POST["name"]);
    }

    if (empty($_POST["prep_time"])) {
        $prepTimeErr = "Prep Time is required";
        $isValid = false;
    } else {
        $prepTime = test_input($_POST["prep_time"]);
    }

    if (empty($_POST["difficulty"])) {
        $difficultyErr = "Difficulty is required";
        $isValid = false;
    } else {
        $difficulty = test_input($_POST["difficulty"]);
        if (!is_numeric($difficulty) || $difficulty < 1 || $difficulty > 3) {
            $difficultyErr = "Difficulty must be a number between 1 and 3";
            $isValid = false;
        }
    }

    if (!isset($_POST["vegetarian"])) {
        $vegetarianErr = "Vegetarian is required";
        $isValid = false;
    } else {
        $vegetarian = test_input($_POST["vegetarian"]);
    }

    if ($isValid) {
        $sql = "UPDATE recipe SET name = ?, prep_time = ?, difficulty = ?, vegetarian = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssiii', $name, $prepTime, $difficulty, $vegetarian, $id);

        if ($stmt->execute()) {
            header("Location: index.php?msg=Record updated successfully");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Recipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h2 class="text-center">Update Recipe</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
                <span class="text-danger"><?php echo $nameErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="prep_time" class="form-label">Prep Time</label>
                <input type="text" class="form-control" id="prep_time" name="prep_time" value="<?php echo $prepTime; ?>">
                <span class="text-danger"><?php echo $prepTimeErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="difficulty" class="form-label">Difficulty</label>
                <input type="number" class="form-control" id="difficulty" name="difficulty" value="<?php echo $difficulty; ?>">
                <span class="text-danger"><?php echo $difficultyErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="vegetarian" class="form-label">Vegetarian</label>
                <select class="form-control" id="vegetarian" name="vegetarian">
                    <option value="1" <?php if ($vegetarian === 1) echo 'selected'; ?>>Yes</option>
                    <option value="0" <?php if ($vegetarian === 0) echo 'selected'; ?>>No</option>
                </select>
                <span class="text-danger"><?php echo $vegetarianErr; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>
