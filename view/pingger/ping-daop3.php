<?php
include '../../lib/db/dbconfig.php';
$d_blok = $conn->query("SELECT (id_blok) FROM blok WHERE pusat_client='1'")->fetch_assoc();
$id_st = $d_blok['id_blok'];
$limit = 5; // jumlah Ip per Halaman
$start = 1;
$slice = 9;
$self_server = "./home&id=$id_st";
$q = "SELECT * FROM client WHERE id_blok='$id_st'";
$r = $conn->query($q);
$totalrows = $r->num_rows;

if (!isset($_GET['pn']) || !is_numeric($_GET['pn'])) {
    $page = 1;
} else {
    $page = $_GET['pn'];
}

$numofpages = ceil($totalrows / $limit);
$limitvalue = $page * $limit - ($limit);

$q = "SELECT * FROM client WHERE id_blok='$id_st'";
//jika user nakal paging lebih dari data yg dimiliki
$cek_page = $conn->query($q);
if ($cek_page->num_rows != 0) {
    if ($r = $conn->query($q)) {
        if ($r->num_rows !== 0) {
            echo "
            <div class='panel-body'>
                    <div class='row'>";
            $no = 0;

            while ($client = $r->fetch_assoc()) {
                extract($client);
                $ip =  "$ip_client";
                // Query untuk update status client
                $sql = "UPDATE client SET status_client=? WHERE id_client='$id_client'";
                exec("ping -n 1 $ip_client", $output['ke' . $ip_client], $status);
                if ($status == 0) {
                    $cut = explode(":", $output['ke' . $ip_client]['2']);
                    $hasil = trim($cut['0'], " .");
                    switch ($hasil) {
                        case 'Permintaan habis':
                            $log = "Permintaan habis";
                            if ($status_client !== "$log") {
                                if ($statement = $conn->prepare($sql)) {
                                    $statement->bind_param("s", $log);
                                    $statement->execute();
                                }
                            }
                            $status = "<span class='badge bg-success'>$log</span>";
                            break;

                        default:
                            $hasil1 = trim($cut['1'], " .");
                            switch ($hasil1) {
                                case 'Jaringan tidak terjangkau':
                                    $log =  "Jaringan tidak terjangkau";
                                    if ($status_client !== "$log") {
                                        if ($statement = $conn->prepare($sql)) {
                                            $statement->bind_param("s", $log);
                                            $statement->execute();
                                        }
                                    }
                                    $status = "<span class='badge bg-warning'>$log</span>";
                                    break;
                                case 'Host tidak terjangkau':
                                    $log = "Host tidak terjangkau";
                                    if ($status_client !== "$log") {
                                        if ($statement = $conn->prepare($sql)) {
                                            $statement->bind_param("s", $log);
                                            $statement->execute();
                                        }
                                    }
                                    $status = "<span class='badge bg-warning'>$log</span>";
                                    break;

                                default:
                                    $log = "Terhubung";
                                    if ($status_client !== "$log") {
                                        if ($statement = $conn->prepare($sql)) {
                                            $statement->bind_param("s", $log);
                                            $statement->execute();
                                        }
                                    }
                                    $status = "<span class='badge bg-success'>$log</span>";
                                    break;
                            }
                            break;
                    }
                } else {
                    $log = "Terputus";
                    if ($status_client !== "$log") {
                        if ($statement = $conn->prepare($sql)) {
                            $statement->bind_param("s", $log);
                            $statement->execute();
                        }
                    }
                    $status = "<span class='badge bg-danger'>$log</span>";
                }
                $no++;

                echo "
                        <div class='col-md-4 mb-4'>
                        <div class='card shadow-md text-center' style='margin-bottom : 30px'>
                            <div class='card-body d-flex align-items-center p-3'>
                                <img src='asset/dist/img/user2-160x160.jpg' alt='Client Photo' class='rounded-circle me-3' style='width: 70px; height: 70px;'>
                                <div>
                                    <h5 class='card-title'><b>$name_client</b></h5>
                                    <p class='card-text'><strong>IP Client:</strong> $ip_client</p>
                                    <p class='card-text'><strong>Status:</strong> $status</p>
                                </div>
                            </div>
                        </div>
                      </div>";
            }
            echo "</div></div>";
        } else {
            echo "<div class='alert alert-warning'><strong>Tidak ada Data untuk ditampilkan!</strong></div>";
        }
    } else {
        echo "Terjadi kesalahan";
    }
    // $sql_cek_row = "SELECT*FROM client WHERE id_blok='$id_st'";
    // $q_cek = $conn->query($sql_cek_row);
    // $hitung = $q_cek->num_rows;
    // if ($hitung >= $limit) {
    //     echo "<hr><ul class='pagination'>";
    //     if ($page != 1) {
    //         $pageprev = $page - 1;
    //         echo '<li><a href="' . $self_server . '&pn=' . $pageprev . '"><i class="fa fa-chevron-left"></i></a></li>';
    //     } else {
    //         echo "<li><li><a href='#'><i class='fa fa-chevron-left'></i></a></li>";
    //     }

    //     if (($page + $slice) < $numofpages) {
    //         $this_far = $page + $slice;
    //     } else {
    //         $this_far = $numofpages;
    //     }

    //     if (($start + $page) >= 10 && ($page - 10) > 0) {
    //         $start = $page - 10;
    //     }

    //     for ($i = $start; $i <= $this_far; $i++) {
    //         if ($i == $page) {
    //             echo "<li class='active'><a href='#'>" . $i . "</a></li> ";
    //         } else {
    //             echo '<li><a href="' . $self_server . '&pn=' . $i . '">' . $i . '</a></li> ';
    //         }
    //     }

    //     if (($totalrows - ($limit * $page)) > 0) {
    //         $pagenext = $page + 1;
    //         echo '<li><a href="' . $self_server . '&pn=' . $pagenext . '"><i class="fa fa-chevron-right"></i></a></li>';
    //     } else {
    //         echo "<li><li><a href='#'><i class='fa fa-chevron-right'></i></a></li>";
    //     }
    //     echo "</ul>";
    // }
} else {
    include '../not_data.php';
}
