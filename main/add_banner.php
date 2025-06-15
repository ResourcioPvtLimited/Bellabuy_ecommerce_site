<?php
require "../config.php";

// Get shop_id safely
$shop_id = $_GET['shop_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <?php require "inc/head.php"; ?>
    <title>Products</title>
</head>
<body>
<?php require "inc/nav.php"; ?>

<div class="flex_">
    <?php require "inc/sidebar.php"; ?>
    <div class="right">
        <div class="padding">
            <h3 class="page-title">Category</h3>

            <!-- Upload Form -->
            <form action="submit2.php" method="post" enctype="multipart/form-data" class="form-group" style="display: flex; flex-wrap: wrap; gap: 10px;">
                <input type="text" name="text3" required class="form-control" placeholder="Heading">
                <input type="text" name="text4" required class="form-control" placeholder="Paragraph">
                <input type="text" name="text5" required class="form-control" placeholder="Link">
                <input type="file" name="image1" accept="image/*" required class="form-control">
                <input type="submit" value="Submit" class="btn btn-success">
            </form>

            <br>

            <!-- Search Bar -->
            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for links..." class="form-control">

            <!-- Banner Table -->
            <table class="table table-striped padding" id="tab">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Link</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM banner";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = htmlspecialchars($row['id']);
                            $link = htmlspecialchars($row['link']);

                            echo "
                                <tr>
                                    <td>$id</td>
                                    <td>$link</td>
                                    <td><button class='btn btn-danger delete_banner' title='$id'>Delete</button></td>
                                </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Search Script -->
            <script>
                function myFunction() {
                    var input = document.getElementById("myInput");
                    var filter = input.value.toUpperCase();
                    var table = document.getElementById("tab");
                    var tr = table.getElementsByTagName("tr");

                    for (let i = 1; i < tr.length; i++) {
                        let td = tr[i].getElementsByTagName("td")[1];
                        if (td) {
                            let txtValue = td.textContent || td.innerText;
                            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
                        }
                    }
                }
            </script>
        </div>
    </div>
</div>
</body>
</html>
