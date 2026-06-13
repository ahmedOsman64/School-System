<?php
require_once '../lib/db.php';

$class_id = $_GET['class_id'] ?? '';
$week_start = $_GET['week_start'] ?? '';

if (!$week_start) {
    // Default to this week's Monday
    $monday = strtotime('last monday', strtotime('tomorrow'));
    $week_start = date('Y-m-d', $monday);
}

// Generate the 6 days of the week (Mon-Sat)
$days = [];
for ($i = 0; $i < 6; $i++) {
    $date = date('Y-m-d', strtotime("$week_start +$i days"));
    $days[] = [
        'date' => $date,
        'day' => date('D', strtotime($date)),
        'display' => date('d-M', strtotime($date))
    ];
}

$class_name = "Select Class";
if ($class_id) {
    $res = $conn->query("SELECT class_name FROM classes WHERE id = $class_id");
    if ($res && $res->num_rows > 0) {
        $class_name = $res->fetch_assoc()['class_name'];
    }
}

// Stats variables
$total_present = 0;
$total_absent = 0;
$total_late = 0;
$total_records = 0;

$attendance_data = [];
if ($class_id) {
    $students = $conn->query("SELECT id, full_name, admission_no FROM students WHERE class_id = $class_id AND status = 1 ORDER BY full_name");
    while ($s = $students->fetch_assoc()) {
        $st_attendance = [];
        $p_count = 0;
        $a_count = 0;
        $l_count = 0;
        
        foreach ($days as $d) {
            $att_res = $conn->query("SELECT status FROM student_attendance WHERE student_id = {$s['id']} AND attendance_date = '{$d['date']}'");
            if ($att_res && $att_res->num_rows > 0) {
                $status = $att_res->fetch_assoc()['status'];
                $st_attendance[$d['date']] = $status;
                if ($status == 'Present') { $p_count++; $total_present++; }
                elseif ($status == 'Absent') { $a_count++; $total_absent++; }
                elseif ($status == 'Late') { $l_count++; $total_late++; }
                $total_records++;
            } else {
                $st_attendance[$d['date']] = '-';
            }
        }
        
        $attendance_data[] = [
            'id' => $s['id'],
            'full_name' => $s['full_name'],
            'admission_no' => $s['admission_no'],
            'attendance' => $st_attendance,
            'p_count' => $p_count,
            'a_count' => $a_count,
            'l_count' => $l_count
        ];
    }
}

// Percentages
$present_percent = ($total_records > 0) ? round(($total_present / $total_records) * 100) : 0;
$absent_percent = ($total_records > 0) ? round(($total_absent / $total_records) * 100) : 0;
$late_percent = ($total_records > 0) ? round(($total_late / $total_records) * 100) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../lib/head.php'; ?>
    <style>
        :root {
            --primary-blue: #4a90e2;
            --light-blue: #ebf3ff;
            --present-green: #47b370;
            --absent-red: #e74c3c;
            --late-orange: #f39c12;
            --text-dark: #333;
        }

        .attendance-header {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            padding: 40px 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            margin-bottom: 0;
        }

        .attendance-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .summary-container {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .week-starting-box {
            background: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .week-starting-box .label {
            background: #f0f0f0;
            padding: 8px;
            font-weight: 600;
            color: #555;
            border-bottom: 1px solid #ddd;
        }

        .week-starting-box .content {
            display: flex;
            align-items: center;
            padding: 15px;
        }

        .week-starting-box .day {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-blue);
            line-height: 1;
            margin-right: 15px;
        }

        .week-starting-box .month-year {
            text-align: left;
        }

        .week-starting-box .month {
            font-size: 1.2rem;
            font-weight: 700;
            display: block;
        }

        .week-starting-box .year {
            color: #888;
            font-size: 1rem;
        }

        .stat-circle-container {
            text-align: center;
            margin: 10px;
        }

        .stat-circle-container .label {
            font-weight: 700;
            margin-bottom: 15px;
            display: block;
            font-size: 1.1rem;
            color: #444;
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            position: relative;
        }

        .progress-circle svg {
            width: 120px;
            height: 120px;
            transform: rotate(-90deg);
        }

        .progress-circle circle {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
        }

        .progress-circle .bg {
            stroke: #eee;
        }

        .progress-circle .bar {
            transition: stroke-dashoffset 1s ease-in-out;
        }

        .progress-circle .percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
            font-weight: 800;
            color: #333;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-top: 20px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th {
            background-color: var(--primary-blue);
            color: white;
            font-weight: 600;
            padding: 15px 10px;
            text-align: center;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .attendance-table th.name-col {
            text-align: left;
            padding-left: 20px;
        }

        .attendance-table td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .attendance-table td.name-col {
            text-align: left;
            padding-left: 20px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .status-p { background-color: #d4edda; color: var(--present-green); }
        .status-a { background-color: #f8d7da; color: var(--absent-red); }
        .status-l { background-color: #fff3cd; color: var(--late-orange); }
        .status-none { color: #ccc; }

        .count-badge {
            font-weight: 700;
            color: #555;
        }

        .percent-bar-container {
            width: 80px;
            height: 15px;
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }

        .percent-bar {
            height: 100%;
            background: var(--present-green);
        }

        .filter-card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        @media print {
            .no-print { display: none; }
            .content-wrapper { margin-left: 0 !important; }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php include '../lib/nav.php';?>
  <?php require '../lib/sidebar.php';?>

  <div class="content-wrapper">
    <div class="content-header no-print">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Weekly Attendance Board</h1>
          </div>
          <div class="col-sm-6">
            <button onclick="window.print()" class="btn btn-primary float-right"><i class="fas fa-print"></i> Print Report</button>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <!-- Filter Card -->
        <div class="card filter-card no-print">
          <div class="card-body">
            <form method="get" class="row align-items-end">
              <div class="col-md-4">
                <label>Select Class</label>
                <select name="class_id" class="form-control select2" required>
                  <option value="">-- Choose Class --</option>
                  <?php
                  $cls = $conn->query("SELECT * FROM classes ORDER BY class_name");
                  while($c = $cls->fetch_assoc()){
                    $sel = ($c['id'] == $class_id) ? 'selected' : '';
                    echo "<option value='{$c['id']}' $sel>{$c['class_name']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <label>Week Starting (Monday)</label>
                <input type="date" name="week_start" class="form-control" value="<?= $week_start ?>">
              </div>
              <div class="col-md-4">
                <button type="submit" class="btn btn-success btn-block"><i class="fas fa-sync"></i> Refresh Dashboard</button>
              </div>
            </form>
          </div>
        </div>

        <?php if ($class_id): ?>
        <div id="attendance-board">
            <div class="attendance-header">
                <h1>Weekly Attendance Sheet</h1>
                <p><?= $class_name ?> | <?= date('F d, Y', strtotime($week_start)) ?> to <?= date('F d, Y', strtotime($days[5]['date'])) ?></p>
            </div>

            <div class="summary-container">
                <div class="week-starting-box">
                    <div class="label">Week Starting</div>
                    <div class="content">
                        <div class="day"><?= date('d', strtotime($week_start)) ?></div>
                        <div class="month-year">
                            <span class="month"><?= date('F', strtotime($week_start)) ?></span>
                            <span class="year"><?= date('Y', strtotime($week_start)) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Present % Circle -->
                <div class="stat-circle-container">
                    <span class="label">Present%</span>
                    <div class="progress-circle">
                        <svg>
                            <circle class="bg" cx="60" cy="60" r="50"></circle>
                            <circle class="bar" cx="60" cy="60" r="50" style="stroke: var(--present-green); stroke-dasharray: 314; stroke-dashoffset: <?= 314 - (314 * ($present_percent / 100)) ?>;"></circle>
                        </svg>
                        <div class="percentage"><?= $present_percent ?>%</div>
                    </div>
                </div>

                <!-- Absent % Circle -->
                <div class="stat-circle-container">
                    <span class="label">Absent%</span>
                    <div class="progress-circle">
                        <svg>
                            <circle class="bg" cx="60" cy="60" r="50"></circle>
                            <circle class="bar" cx="60" cy="60" r="50" style="stroke: var(--absent-red); stroke-dasharray: 314; stroke-dashoffset: <?= 314 - (314 * ($absent_percent / 100)) ?>;"></circle>
                        </svg>
                        <div class="percentage text-danger"><?= $absent_percent ?>%</div>
                    </div>
                </div>

                <!-- Late % Circle -->
                <div class="stat-circle-container">
                    <span class="label">Late%</span>
                    <div class="progress-circle">
                        <svg>
                            <circle class="bg" cx="60" cy="60" r="50"></circle>
                            <circle class="bar" cx="60" cy="60" r="50" style="stroke: var(--late-orange); stroke-dasharray: 314; stroke-dashoffset: <?= 314 - (314 * ($late_percent / 100)) ?>;"></circle>
                        </svg>
                        <div class="percentage text-warning"><?= $late_percent ?>%</div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ADMN ID</th>
                            <th class="name-col">Student Name</th>
                            <?php foreach ($days as $d): ?>
                                <th><?= $d['day'] ?><br><small><?= $d['display'] ?></small></th>
                            <?php endforeach; ?>
                            <th>P</th>
                            <th>A</th>
                            <th>L</th>
                            <th>Status %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_data as $data): ?>
                            <?php 
                                $total_days = $data['p_count'] + $data['a_count'] + $data['l_count'];
                                $student_percent = ($total_days > 0) ? round(($data['p_count'] / $total_days) * 100) : 0;
                            ?>
                            <tr>
                                <td><?= $data['admission_no'] ?></td>
                                <td class="name-col"><?= $data['full_name'] ?></td>
                                <?php foreach ($days as $d): ?>
                                    <td>
                                        <?php 
                                        $s = $data['attendance'][$d['date']];
                                        if ($s == 'Present') echo '<span class="status-badge status-p">P</span>';
                                        elseif ($s == 'Absent') echo '<span class="status-badge status-a">A</span>';
                                        elseif ($s == 'Late') echo '<span class="status-badge status-l">L</span>';
                                        else echo '<span class="status-none">-</span>';
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="count-badge"><?= $data['p_count'] ?></td>
                                <td class="count-badge text-danger"><?= $data['a_count'] ?></td>
                                <td class="count-badge text-warning"><?= $data['l_count'] ?></td>
                                <td>
                                    <span style="font-weight: 700; display: inline-block; width: 40px;"><?= $student_percent ?>%</span>
                                    <div class="percent-bar-container no-print">
                                        <div class="percent-bar" style="width: <?= $student_percent ?>%;"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info"></i> Welcome!</h5>
            Please select a class and week starting date to view the attendance dashboard.
        </div>
        <?php endif; ?>

      </div>
    </section>
  </div>

  <?php include '../lib/footer.php';?>
</div>
<?php include '../lib/script.php';?>
</body>
</html>
