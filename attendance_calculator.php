<?php
date_default_timezone_set('Asia/Kolkata');

$days_routine = [
    'Monday' => 0,
    'Tuesday' => 7,
    'Wednesday' => 7,
    'Thursday' => 4,
    'Friday' => 7,
    'Saturday' => 7,
    'Sunday' => 0
];

$result = null;
$error = null;
$default_date = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $total_classes = isset($_POST['total_classes']) ? (int)$_POST['total_classes'] : 0;
    $attended_classes = isset($_POST['attended_classes']) ? (int)$_POST['attended_classes'] : 0;
    $leave_days = isset($_POST['leave_days']) ? (int)$_POST['leave_days'] : 0;
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : $default_date;
    $attended_during_leave = isset($_POST['attended_during_leave']) ? (int)$_POST['attended_during_leave'] : 0;
    
    if ($attended_classes > $total_classes) {
        $error = "Attended classes cannot be greater than total classes.";
    } elseif ($leave_days < 0) {
        $error = "Leave days cannot be negative.";
    } elseif ($attended_during_leave < 0) {
        $error = "Attended classes during leave cannot be negative.";
    } else {
        $classes_happening = 0;
        $current_date = new DateTime($start_date);
        
        for ($i = 0; $i < $leave_days; $i++) {
            $day_name = $current_date->format('l');
            if (isset($days_routine[$day_name])) {
                $classes_happening += $days_routine[$day_name];
            }
            $current_date->modify('+1 day');
        }
        
        if ($attended_during_leave > $classes_happening) {
            $error = "Cannot attend more classes ($attended_during_leave) than scheduled ($classes_happening) in this period.";
        } else {
            $missed_classes = $classes_happening - $attended_during_leave;
            $new_total = $total_classes + $classes_happening;
            $new_attended = $attended_classes + $attended_during_leave;
            
            if ($new_total > 0) {
                $current_percentage = ($total_classes > 0) ? round(($attended_classes / $total_classes) * 100, 2) : 0;
                $new_percentage = round(($new_attended / $new_total) * 100, 2);
                
                $end_date_obj = new DateTime($start_date);
                if ($leave_days > 0) {
                    $end_date_obj->modify('+' . ($leave_days - 1) . ' days');
                }
                
                $result = [
                    'current_percentage' => $current_percentage,
                    'new_percentage' => $new_percentage,
                    'missed_classes' => $missed_classes,
                    'new_total' => $new_total,
                    'new_attended' => $new_attended,
                    'leave_days' => $leave_days,
                    'start_date' => $start_date,
                    'end_date' => $end_date_obj->format('Y-m-d'),
                    'attended_during_leave' => $attended_during_leave
                ];
            } else {
                $error = "Total classes must be greater than zero.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Predictor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #ffffff;
            --text-main: #111111;
            --text-muted: #666666;
            --border-color: #e5e5e5;
            --input-bg: #fafafa;
            --primary: #000000;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            width: 100%;
            max-width: 520px;
        }

        .card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 2.5rem;
        }

        .header {
            margin-bottom: 2.5rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1.5rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }

        .header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .time-display {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-weight: 400;
            font-variant-numeric: tabular-nums;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary);
        }

        input[type="number"], input[type="date"] {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.6rem 0.75rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            outline: none;
            border-radius: 0;
            appearance: none;
        }

        input[type="number"]:focus, input[type="date"]:focus {
            border-color: var(--primary);
            background: #ffffff;
        }

        .routine-section {
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            margin-top: 1.5rem;
        }

        .routine-title {
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .routine-info {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 1.25rem;
            letter-spacing: 0.02em;
        }

        .btn {
            background: var(--primary);
            color: #ffffff;
            border: 1px solid var(--primary);
            padding: 0.75rem;
            width: 100%;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: #ffffff;
            color: var(--primary);
        }

        .error-message {
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .result-card {
            margin-top: 2rem;
            border: 1px solid var(--border-color);
            padding: 2rem;
            animation: fadeIn 0.4s ease-out;
        }

        .result-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }

        .stat-box {
            text-align: center;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .percentage-ring-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .attendance-status {
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
            color: var(--text-muted);
        }

        .missed-info {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .footer {
            border-top: 1px solid var(--border-color);
            padding-top: 1.5rem;
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .fw-600 { font-weight: 600; color: var(--primary); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            .card {
                padding: 1.5rem;
                border-left: none;
                border-right: none;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h1>Attendance Predictor</h1>
            <p>Calculate attendance after missing future classes</p>
            <div class="time-display" id="clock">Loading current time...</div>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="total_classes">Total Classes</label>
                    <input type="number" id="total_classes" name="total_classes" required min="1" 
                           value="<?= isset($_POST['total_classes']) ? htmlspecialchars($_POST['total_classes']) : '' ?>"
                           placeholder="0">
                </div>
                <div class="form-group">
                    <label for="attended_classes">Attended</label>
                    <input type="number" id="attended_classes" name="attended_classes" required min="0" 
                           value="<?= isset($_POST['attended_classes']) ? htmlspecialchars($_POST['attended_classes']) : '' ?>"
                           placeholder="0">
                </div>
            </div>

            <div class="routine-section">
                <div class="routine-title">Leave Details</div>
                <div class="form-row" style="margin-bottom: 0;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required 
                               value="<?= isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : $default_date ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="leave_days">Duration (Days)</label>
                        <input type="number" id="leave_days" name="leave_days" required min="1" 
                               value="<?= isset($_POST['leave_days']) ? htmlspecialchars($_POST['leave_days']) : '' ?>"
                               placeholder="1">
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 1.25rem; border-top: 1px dashed var(--border-color); padding-top: 1.25rem; margin-bottom: 0;">
                    <label for="attended_during_leave">Classes Attended During Leave (Optional)</label>
                    <input type="number" id="attended_during_leave" name="attended_during_leave" min="0" 
                           value="<?= isset($_POST['attended_during_leave']) && $_POST['attended_during_leave'] !== '' ? htmlspecialchars($_POST['attended_during_leave']) : '' ?>"
                           placeholder="0">
                </div>
                
                <div class="routine-info">
                    Routine: Mon(0) &middot; Tue(7) &middot; Wed(7) &middot; Thu(4) &middot; Fri(7) &middot; Sat(7) &middot; Sun(0)
                </div>
            </div>

            <button type="submit" class="btn">Calculate</button>
        </form>

        <?php if ($result): ?>
            <div class="result-card" id="result">
                <div class="percentage-ring-container">
                    <div class="stat-label">Predicted Attendance</div>
                    <div class="stat-value" style="font-size: 3.5rem; letter-spacing: -0.05em; margin: 0.5rem 0;">
                        <?= $result['new_percentage'] ?>%
                    </div>
                    <?php 
                        $new_pct = $result['new_percentage'];
                        $status_text = $new_pct >= 75 ? 'Safe Zone' : ($new_pct >= 65 ? 'Warning Zone' : 'Danger Zone');
                    ?>
                    <div class="attendance-status"><?= $status_text ?></div>
                </div>

                <div class="result-stats">
                    <div class="stat-box">
                        <div class="stat-label">Current</div>
                        <div class="stat-value"><?= $result['current_percentage'] ?>%</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Missed Classes</div>
                        <div class="stat-value"><?= $result['missed_classes'] ?></div>
                    </div>
                </div>
                
                <div class="missed-info">
                    Total classes will become <span class="fw-600"><?= $result['new_total'] ?></span>, 
                    with <span class="fw-600"><?= $result['new_attended'] ?></span> attended.
                    <?php if ($result['attended_during_leave'] > 0): ?>
                        <br><span style="font-size: 0.8rem;">(Includes <span class="fw-600"><?= $result['attended_during_leave'] ?></span> classes attended during leave)</span>
                    <?php endif; ?>
                    <br><br>
                    Leave from <span class="fw-600"><?= htmlspecialchars((new DateTime($result['start_date']))->format('M j, Y')) ?></span> 
                    to <span class="fw-600"><?= htmlspecialchars((new DateTime($result['end_date']))->format('M j, Y')) ?></span> 
                    (<?= $result['leave_days'] ?> days).
                </div>
            </div>
            
            <div class="footer">
                Made with love by aritra
            </div>
            
            <script>
                document.getElementById('result').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            </script>
        <?php endif; ?>
        
        <?php if (!$result): ?>
            <div class="footer" style="margin-top: 2rem;">
                Made with love by aritra
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('clock').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

</body>
</html>
