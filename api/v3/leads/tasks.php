<?php
/**
 * API: Fetch Lead Follow-Up Tasks
 * File: api/v3/leads/tasks.php
 */

header("Content-Type: application/json; charset=utf-8");

require_once LOGIN_CHECK;

if (!isset($connection)) {
    $configPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/configuration/index.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

$tblPrefix  = defined('TABLE_PREFIX') ? TABLE_PREFIX : '';
$siteId     = defined('SITE_ID')      ? (int) SITE_ID : 0;
$currentUserId = defined('USER_ID')   ? (int) USER_ID : 0;
$accessType = isset($site_access_type) ? $site_access_type : 'administrator';
$isPrivileged = in_array(strtolower($accessType), ['administrator', 'admin', 'superadmin', 'operations', 'owner'], true);

$rawLeadId = $_GET['leadId'] ?? ($_GET['lead_id'] ?? ($_GET['id'] ?? ''));
$filterStatus = strtolower(trim($_GET['status'] ?? 'pending')); // pending, completed, all, overdue, today, upcoming
$agentFilter = $_GET['agent_id'] ?? ($_GET['agentId'] ?? '');

$where = [];
if ($siteId > 0) {
    $where[] = "(t.task_site_id = {$siteId} OR t.task_site_id = 0)";
}

if (!empty($rawLeadId)) {
    $decoded = base64_decode(trim((string)$rawLeadId), true);
    $leadId  = ($decoded !== false && is_numeric($decoded)) ? (int)$decoded : (int)$rawLeadId;
    if ($leadId > 0) {
        $where[] = "t.task_enquiry_id = {$leadId}";
    }
}

if (!$isPrivileged && $currentUserId > 0 && empty($rawLeadId)) {
    $where[] = "t.task_agent_id = {$currentUserId}";
} elseif (is_numeric($agentFilter) && (int)$agentFilter > 0) {
    $where[] = "t.task_agent_id = " . (int)$agentFilter;
}

$todayStart = date('Y-m-d 00:00:00');
$todayEnd   = date('Y-m-d 23:59:59');
$nowStr     = date('Y-m-d H:i:s');

if ($filterStatus === 'pending') {
    $where[] = "t.task_status = 'pending'";
} elseif ($filterStatus === 'completed') {
    $where[] = "t.task_status = 'completed'";
} elseif ($filterStatus === 'overdue') {
    $where[] = "t.task_status = 'pending' AND t.task_due_at < '{$todayStart}'";
} elseif ($filterStatus === 'today') {
    $where[] = "t.task_status = 'pending' AND t.task_due_at BETWEEN '{$todayStart}' AND '{$todayEnd}'";
} elseif ($filterStatus === 'upcoming') {
    $where[] = "t.task_status = 'pending' AND t.task_due_at > '{$todayEnd}'";
}

$whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

$sql = "SELECT 
    t.*,
    e.enquiry_first_name,
    e.enquiry_last_name,
    e.enquiry_mobile,
    e.enquiry_subject,
    e.enquiry_status,
    p.profile_display_name AS agent_name,
    cp.profile_display_name AS completed_by_name
FROM {$tblPrefix}enquiry_task t
LEFT JOIN {$tblPrefix}enquiry e ON t.task_enquiry_id = e.enquiry_id
LEFT JOIN {$tblPrefix}profile p ON t.task_agent_id = p.profile_user_id
LEFT JOIN {$tblPrefix}profile cp ON t.task_completed_by = cp.profile_user_id
{$whereClause}
ORDER BY 
    CASE WHEN t.task_status = 'pending' THEN 0 ELSE 1 END,
    t.task_due_at ASC
LIMIT 100";

$tasks = [];
$stats = [
    'pending_total' => 0,
    'due_today'     => 0,
    'overdue'       => 0,
    'upcoming'      => 0,
    'completed'     => 0
];

if (isset($connection) && ($res = mysqli_query($connection, $sql))) {
    while ($row = mysqli_fetch_assoc($res)) {
        $dueTs = strtotime($row['task_due_at']);
        $isOverdue = false;
        $isToday = false;
        $isUpcoming = false;

        if ($row['task_status'] === 'pending') {
            $stats['pending_total']++;
            if ($row['task_due_at'] < $todayStart) {
                $isOverdue = true;
                $stats['overdue']++;
            } elseif ($row['task_due_at'] <= $todayEnd) {
                $isToday = true;
                $stats['due_today']++;
            } else {
                $isUpcoming = true;
                $stats['upcoming']++;
            }
        } elseif ($row['task_status'] === 'completed') {
            $stats['completed']++;
        }

        $custName = trim(($row['enquiry_first_name'] ?? '') . ' ' . ($row['enquiry_last_name'] ?? ''));
        if (empty($custName)) {
            $custName = 'Lead #' . $row['task_enquiry_id'];
        }

        $badge = 'upcoming';
        $badgeLabel = 'Upcoming';
        if ($row['task_status'] === 'completed') {
            $badge = 'completed';
            $badgeLabel = 'Completed';
        } elseif ($isOverdue) {
            $badge = 'overdue';
            $badgeLabel = 'Overdue';
        } elseif ($isToday) {
            $badge = 'today';
            $badgeLabel = 'Due Today';
        }

        $typeIcons = [
            'Call'     => 'fi fi-rr-phone-call',
            'WhatsApp' => 'fi fi-rr-comment-alt',
            'Email'    => 'fi fi-rr-envelope',
            'Meeting'  => 'fi fi-rr-users-alt',
            'Other'    => 'fi fi-rr-alarm-clock'
        ];

        $tasks[] = [
            'id'             => (int)$row['task_id'],
            'lead_id'        => (int)$row['task_enquiry_id'],
            'lead_encoded_id'=> base64_encode((string)$row['task_enquiry_id']),
            'customer_name'  => $custName,
            'mobile'         => $row['enquiry_mobile'] ?? '',
            'subject'        => $row['enquiry_subject'] ?? '',
            'type'           => $row['task_type'],
            'type_icon'      => $typeIcons[$row['task_type']] ?? 'fi fi-rr-alarm-clock',
            'due_at'         => $row['task_due_at'],
            'due_formatted'  => date('d M Y, h:i A', $dueTs),
            'due_date'       => date('d M Y', $dueTs),
            'due_time'       => date('h:i A', $dueTs),
            'notes'          => $row['task_notes'] ?? '',
            'next_action'    => $row['task_next_action'] ?? '',
            'status'         => $row['task_status'],
            'outcome'        => $row['task_outcome'] ?? '',
            'completed_at'   => !empty($row['task_completed_at']) ? date('d M Y, h:i A', strtotime($row['task_completed_at'])) : null,
            'completed_by'   => $row['completed_by_name'] ?? '',
            'agent_id'       => (int)$row['task_agent_id'],
            'agent_name'     => $row['agent_name'] ?? 'Assigned Agent',
            'badge'          => $badge,
            'badge_label'    => $badgeLabel,
            'is_overdue'     => $isOverdue,
            'is_today'       => $isToday,
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'data'   => $tasks,
    'stats'  => $stats,
    'total'  => count($tasks)
]);
exit();
