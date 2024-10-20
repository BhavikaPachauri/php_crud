<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe CRUD APP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Recipe CRUD APP</h2>

        <form method="GET" action="index.php" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="search" placeholder="Search by name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <a href="create.php" class="btn btn-primary mb-3">Create</a>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Prep Time</th>
                    <th scope="col">Difficulty</th>
                    <th scope="col">Vegetarian</th>
                    <th scope="col">Rating</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db.php';

                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $limit = 5;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                $sql = "SELECT * FROM recipe WHERE name LIKE ? LIMIT ?, ?";
                $stmt = $conn->prepare($sql);
                $searchParam = '%' . $search . '%';
                $stmt->bind_param('sii', $searchParam, $offset, $limit);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>   
                        <tr>
                            <th scope="row"><?php echo $row["id"]; ?></th>
                            <td><?php echo $row["name"]; ?></td>
                            <td><?php echo $row["prep_time"]; ?></td>
                            <td><?php echo $row["difficulty"]; ?></td>
                            <td><?php echo $row["vegetarian"] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo $row["rating"]; ?></td>
                            <td>
                                <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-info">EDIT</a>
                                <form action="delete.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger">DELETE</button>
                                </form>
                                <form action="rate.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <select name="rating" onchange="this.form.submit()">
                                        <option value="1" <?php if($row['rating'] == 1) echo 'selected'; ?>>1</option>
                                        <option value="2" <?php if($row['rating'] == 2) echo 'selected'; ?>>2</option>
                                        <option value="3" <?php if($row['rating'] == 3) echo 'selected'; ?>>3</option>
                                        <option value="4" <?php if($row['rating'] == 4) echo 'selected'; ?>>4</option>
                                        <option value="5" <?php if($row['rating'] == 5) echo 'selected'; ?>>5</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                }
                $stmt->close();

                $sql_total = "SELECT COUNT(*) as count FROM recipe WHERE name LIKE ?";
                $stmt_total = $conn->prepare($sql_total);
                $stmt_total->bind_param('s', $searchParam);
                $stmt_total->execute();
                $result_total = $stmt_total->get_result();
                $total = $result_total->fetch_assoc()['count'];
                $stmt_total->close();

                $conn->close();
                ?>
            </tbody>
        </table>

        <?php
        $total_pages = ceil($total / $limit);
        if ($total_pages > 1) {
            echo '<nav>';
            echo '<ul class="pagination justify-content-center">';
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="index.php?page=' . $i . '&search=' . $search . '">' . $i . '</a></li>';
            }
            echo '</ul>';
            echo '</nav>';
        }
        ?>
    </div>
</body>

</html>
