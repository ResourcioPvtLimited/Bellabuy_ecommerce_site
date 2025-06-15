<?php
require "../config.php";
session_start();

if (!isset($_SESSION['shop_id'])) {
    header("Location: login.php");
    exit();
}

$shop_id = $_SESSION['shop_id'];

// Handle review actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $review_id = intval($_POST['review_id']);
        
        switch ($_POST['action']) {
            case 'report_abuse':
                $stmt = $conn->prepare("UPDATE review SET abuse = '1' WHERE id = ?");
                $stmt->bind_param("i", $review_id);
                $stmt->execute();
                $_SESSION['message'] = "Review reported for abuse";
                break;
                
            case 'delete_review':
                $stmt = $conn->prepare("DELETE FROM review WHERE id = ?");
                $stmt->bind_param("i", $review_id);
                $stmt->execute();
                $_SESSION['message'] = "Review deleted successfully";
                break;
                
            case 'approve_review':
                $stmt = $conn->prepare("UPDATE review SET abuse = '0' WHERE id = ?");
                $stmt->bind_param("i", $review_id);
                $stmt->execute();
                $_SESSION['message'] = "Review approved and made visible";
                break;
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require "inc/head.php"; ?>
    <title>Product Reviews - Vendor Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            
            --light-bg: #f8f9fa;
        }
        
        
        
        .review-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
            border: none;
            overflow: hidden;
        }
        
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .review-card.reported {
            border-left: 4px solid var(--danger-color);
        }
        
        .review-card.approved {
            border-left: 4px solid var(--success-color);
        }
        
        .star-rating {
            color: #f1c40f;
            margin-bottom: 10px;
        }
        
        .star-rating .empty {
            color: #ddd;
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        
        .review-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .review-content {
            position: relative;
        }
        
        .review-actions {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .filter-container {
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 15px;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-link {
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .review-actions {
                position: static;
                margin-top: 15px;
                display: flex;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <?php require "inc/sidebar.php"; ?>

    <div class="content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Product Reviews</h2>
            <div>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reviewStatsModal">
                    <i class="fas fa-chart-pie"></i> View Stats
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-container">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="rating-filter" class="form-label">Rating</label>
                    <select class="form-select" id="rating-filter" name="rating">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status-filter" class="form-label">Status</label>
                    <select class="form-select" id="status-filter" name="status">
                        <option value="">All Reviews</option>
                        <option value="reported">Reported</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date-from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date-from" name="date_from">
                </div>
                <div class="col-md-3">
                    <label for="date-to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date-to" name="date_to">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <!-- Reviews List -->
        <div class="row">
            <?php
            // Build query with filters
            $where = "WHERE i.shop_id = '$shop_id'";
            
            if (isset($_GET['rating']) && !empty($_GET['rating'])) {
                $rating = intval($_GET['rating']);
                $where .= " AND r.star = '$rating'";
            }
            
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                if ($_GET['status'] == 'reported') {
                    $where .= " AND r.abuse = '1'";
                } elseif ($_GET['status'] == 'approved') {
                    $where .= " AND r.abuse = '0'";
                }
            }
            
            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                $date_from = date('Y-m-d', strtotime($_GET['date_from']));
                $where .= " AND r.date >= '$date_from'";
            }
            
            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                $date_to = date('Y-m-d', strtotime($_GET['date_to']));
                $where .= " AND r.date <= '$date_to'";
            }
            
            // Pagination
            $per_page = 10;
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($page - 1) * $per_page;
            
            // Get total count for pagination
            $count_sql = "SELECT COUNT(r.id) as total 
                          FROM review r
                          LEFT JOIN item i ON r.p_id = i.id
                          $where";
            $count_result = $conn->query($count_sql);
            $total_reviews = $count_result->fetch_assoc()['total'];
            $total_pages = ceil($total_reviews / $per_page);
            
            // Get reviews
            $sql = "SELECT r.*, i.name AS product_name, i.img1, c.name, c.lname
                    FROM review r
                    LEFT JOIN item i ON r.p_id = i.id
                    LEFT JOIN cust c ON r.u_id = c.id
                    $where
                    ORDER BY r.date DESC
                    LIMIT $offset, $per_page";
            
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $review_id = $row['id'];
                    $customer_name = htmlspecialchars($row['name'] . ' ' . $row['lname']);
                    $date = date("M j, Y", strtotime($row['date']));
                    $product_name = htmlspecialchars($row['product_name'] ?? "Product");
                    $review_text = nl2br(htmlspecialchars($row['review']));
                    $short_rev = htmlspecialchars($row['short_rev'] ?? '');
                    $rating = intval($row['star']);
                    $product_id = $row['p_id'];
                    $is_reported = ($row['abuse'] == '1');
                    $img_path = !empty($row['img1']) ? "../prod/" . $row['img1'] : "../prod/no-image.png";
                    
                    // Generate stars
                    $stars = str_repeat('<i class="fas fa-star"></i>', $rating);
                    if ($rating < 5) {
                        $stars .= str_repeat('<i class="far fa-star empty"></i>', 5 - $rating);
                    }
                    ?>
                    <div class="col-md-6">
                        <div class="review-card <?php echo $is_reported ? 'reported' : 'approved'; ?>">
                            <div class="card-body">
                                <div class="review-content">
                                    <div class="review-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="reviewActions<?php echo $review_id; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="reviewActions<?php echo $review_id; ?>">
                                                <?php if ($is_reported): ?>
                                                    <li>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">
                                                            <input type="hidden" name="action" value="approve_review">
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="fas fa-check-circle"></i> Approve Review
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php else: ?>
                                                    <li>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">
                                                            <input type="hidden" name="action" value="report_abuse">
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="fas fa-flag"></i> Report Abuse
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">
                                                        <input type="hidden" name="action" value="delete_review">
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this review?')">
                                                            <i class="fas fa-trash-alt"></i> Delete Review
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-start mb-3">
                                        <img src="<?php echo $img_path; ?>" class="product-img me-3" alt="<?php echo $product_name; ?>">
                                        <div>
                                            <h5 class="mb-1"><?php echo $product_name; ?></h5>
                                            <div class="star-rating mb-1"><?php echo $stars; ?></div>
                                            <p class="review-meta mb-0">
                                                Reviewed by <strong><?php echo $customer_name; ?></strong> on <?php echo $date; ?>
                                                <?php if ($is_reported): ?>
                                                    <span class="badge bg-danger ms-2">Reported</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($short_rev)): ?>
                                        <h6 class="fw-bold mb-2"><?php echo $short_rev; ?></h6>
                                    <?php endif; ?>
                                    
                                    <p class="mb-0"><?php echo $review_text; ?></p>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="../single-product.php?id=<?php echo $product_id; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> View Product
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#responseModal<?php echo $review_id; ?>">
                                        <i class="fas fa-reply"></i> Respond
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Response Modal -->
                    <div class="modal fade" id="responseModal<?php echo $review_id; ?>" tabindex="-1" aria-labelledby="responseModalLabel<?php echo $review_id; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="responseModalLabel<?php echo $review_id; ?>">Respond to Review</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="responseForm<?php echo $review_id; ?>">
                                        <div class="mb-3">
                                            <label for="responseText<?php echo $review_id; ?>" class="form-label">Your Response</label>
                                            <textarea class="form-control" id="responseText<?php echo $review_id; ?>" rows="4" placeholder="Write your response to this review..."></textarea>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="publicResponse<?php echo $review_id; ?>">
                                            <label class="form-check-label" for="publicResponse<?php echo $review_id; ?>">
                                                Make this response public
                                            </label>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="submitResponse(<?php echo $review_id; ?>)">Submit Response</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '
                <div class="col-12">
                    <div class="empty-state">
                        <i class="far fa-comment-alt"></i>
                        <h4>No reviews found</h4>
                        <p>There are no reviews matching your criteria.</p>
                        <a href="'.$_SERVER['PHP_SELF'].'" class="btn btn-primary">Reset Filters</a>
                    </div>
                </div>';
            }
            ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Reviews pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo isset($_GET['rating']) ? '&rating='.$_GET['rating'] : ''; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : ''; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['rating']) ? '&rating='.$_GET['rating'] : ''; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo isset($_GET['rating']) ? '&rating='.$_GET['rating'] : ''; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from='.$_GET['date_from'] : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to='.$_GET['date_to'] : ''; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
    
    <!-- Review Stats Modal -->
    <div class="modal fade" id="reviewStatsModal" tabindex="-1" aria-labelledby="reviewStatsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewStatsModalLabel">Review Statistics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Average Rating</h6>
                                    <?php
                                    $avg_rating_sql = "SELECT AVG(star) as avg_rating FROM review r
                                                      LEFT JOIN item i ON r.p_id = i.id
                                                      WHERE i.shop_id = '$shop_id'";
                                    $avg_rating_result = $conn->query($avg_rating_sql);
                                    $avg_rating = $avg_rating_result->fetch_assoc()['avg_rating'];
                                    $full_stars = floor($avg_rating);
                                    $half_star = ($avg_rating - $full_stars) >= 0.5;
                                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                    ?>
                                    <div class="text-center mb-2">
                                        <div class="star-rating display-4">
                                            <?php echo str_repeat('<i class="fas fa-star"></i>', $full_stars); ?>
                                            <?php if ($half_star): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php endif; ?>
                                            <?php echo str_repeat('<i class="far fa-star empty"></i>', $empty_stars); ?>
                                        </div>
                                        <h2 class="mt-2"><?php echo number_format($avg_rating, 1); ?> out of 5</h2>
                                        <p class="text-muted">Based on <?php echo $total_reviews; ?> reviews</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Rating Distribution</h6>
                                    <?php
                                    $distribution_sql = "SELECT star, COUNT(*) as count FROM review r
                                                        LEFT JOIN item i ON r.p_id = i.id
                                                        WHERE i.shop_id = '$shop_id'
                                                        GROUP BY star
                                                        ORDER BY star DESC";
                                    $distribution_result = $conn->query($distribution_sql);
                                    ?>
                                    <div class="rating-distribution">
                                        <?php while ($dist = $distribution_result->fetch_assoc()): 
                                            $percentage = ($total_reviews > 0) ? ($dist['count'] / $total_reviews) * 100 : 0;
                                        ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2" style="width: 50px;">
                                                <?php echo $dist['star']; ?> <i class="fas fa-star text-warning"></i>
                                            </div>
                                            <div class="progress flex-grow-1" style="height: 20px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $percentage; ?>%" 
                                                     aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="ms-2" style="width: 40px; text-align: right;">
                                                <?php echo $dist['count']; ?>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Review Status</h6>
                                    <?php
                                    $status_sql = "SELECT 
                                                    SUM(CASE WHEN abuse = '1' THEN 1 ELSE 0 END) as reported,
                                                    SUM(CASE WHEN abuse = '0' THEN 1 ELSE 0 END) as approved
                                                  FROM review r
                                                  LEFT JOIN item i ON r.p_id = i.id
                                                  WHERE i.shop_id = '$shop_id'";
                                    $status_result = $conn->query($status_sql);
                                    $status = $status_result->fetch_assoc();
                                    ?>
                                    <canvas id="reviewStatusChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Recent Reviews</h6>
                                    <?php
                                    $recent_sql = "SELECT r.star, i.name as product_name, r.date 
                                                  FROM review r
                                                  LEFT JOIN item i ON r.p_id = i.id
                                                  WHERE i.shop_id = '$shop_id'
                                                  ORDER BY r.date DESC
                                                  LIMIT 5";
                                    $recent_result = $conn->query($recent_sql);
                                    ?>
                                    <ul class="list-group list-group-flush">
                                        <?php while ($recent = $recent_result->fetch_assoc()): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo htmlspecialchars($recent['product_name']); ?></span>
                                                <span class="star-rating">
                                                    <?php echo str_repeat('<i class="fas fa-star"></i>', $recent['star']); ?>
                                                    <?php echo str_repeat('<i class="far fa-star empty"></i>', 5 - $recent['star']); ?>
                                                </span>
                                            </div>
                                            <small class="text-muted"><?php echo date('M j, Y', strtotime($recent['date'])); ?></small>
                                        </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize charts when modal is shown
        document.getElementById('reviewStatsModal').addEventListener('shown.bs.modal', function() {
            // Review Status Chart
            const statusCtx = document.getElementById('reviewStatusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Reported'],
                    datasets: [{
                        data: [<?php echo $status['approved']; ?>, <?php echo $status['reported']; ?>],
                        backgroundColor: ['#2ecc71', '#e74c3c'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
        
        // Submit review response
        function submitResponse(reviewId) {
            const responseText = document.getElementById('responseText' + reviewId).value;
            const isPublic = document.getElementById('publicResponse' + reviewId).checked;
            
            if (!responseText.trim()) {
                alert('Please enter your response');
                return;
            }
            
            // In a real implementation, this would make an AJAX call to save the response
            alert('Response would be saved:\n\n' + responseText + '\n\nPublic: ' + (isPublic ? 'Yes' : 'No'));
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('responseModal' + reviewId));
            modal.hide();
        }
        
        // Apply filters when dropdowns change (optional)
        document.getElementById('rating-filter').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('status-filter').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>