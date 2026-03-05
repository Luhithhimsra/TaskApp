// Task Model — handles all database queries for tasks
// Includes CRUD, soft delete, restore, filtering and pagination
// Tasks page — admin view showing all tasks with filtering and pagination
// Supports filtering by status, priority and keyword search

<?php

// ── INSERT ────────────────────────────────────────────────────────────────────
function insert_task($conn, $data) {
    $sql  = "INSERT INTO tasks (title, description, assigned_to, due_date, priority)
             VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// ── GET ALL (with filters + pagination, excludes soft-deleted) ────────────────
function get_all_tasks($conn, $filters = [], $page = 1, $per_page = 10) {
    $where  = ["t.deleted_at IS NULL"];
    $params = [];

    if (!empty($filters['status'])) {
        $where[]  = "t.status = ?";
        $params[] = $filters['status'];
    }
    if (!empty($filters['priority'])) {
        $where[]  = "t.priority = ?";
        $params[] = $filters['priority'];
    }
    if (!empty($filters['search'])) {
        $where[]  = "(t.title LIKE ? OR t.description LIKE ?)";
        $term     = '%' . $filters['search'] . '%';
        $params[] = $term;
        $params[] = $term;
    }
    if (!empty($filters['assigned_to'])) {
        $where[]  = "t.assigned_to = ?";
        $params[] = $filters['assigned_to'];
    }

    $whereSQL = implode(' AND ', $where);

    // Total count for pagination
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM tasks t WHERE $whereSQL");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    // Paginated results — cast to int to safely embed in SQL (avoids MySQL 8.4 bind issues)
    $offset  = (int)(($page - 1) * $per_page);
    $per_page = (int)$per_page;
    $sql    = "SELECT t.*, u.full_name AS assigned_name
               FROM tasks t
               LEFT JOIN users u ON t.assigned_to = u.id
               WHERE $whereSQL
               ORDER BY t.created_at DESC
               LIMIT $per_page OFFSET $offset";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll();

    return [
        'data'         => $tasks,
        'total'        => $total,
        'per_page'     => $per_page,
        'current_page' => $page,
        'last_page'    => (int) ceil($total / $per_page),
    ];
}

// ── GET SINGLE ────────────────────────────────────────────────────────────────
function get_task_by_id($conn, $id) {
    $sql  = "SELECT t.*, u.full_name AS assigned_name
             FROM tasks t
             LEFT JOIN users u ON t.assigned_to = u.id
             WHERE t.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0 ? $stmt->fetch() : false;
}

// ── UPDATE ────────────────────────────────────────────────────────────────────
function update_task($conn, $data) {
    $sql  = "UPDATE tasks SET title=?, description=?, assigned_to=?, due_date=?, priority=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

function update_task_status($conn, $data) {
    $sql  = "UPDATE tasks SET status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

// ── SOFT DELETE ───────────────────────────────────────────────────────────────
function soft_delete_task($conn, $id) {
    $sql  = "UPDATE tasks SET deleted_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
}

// ── RESTORE ───────────────────────────────────────────────────────────────────
function restore_task($conn, $id) {
    $sql  = "UPDATE tasks SET deleted_at = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
}

// ── GET TRASHED ───────────────────────────────────────────────────────────────
function get_trashed_tasks($conn) {
    $sql  = "SELECT t.*, u.full_name AS assigned_name
             FROM tasks t
             LEFT JOIN users u ON t.assigned_to = u.id
             WHERE t.deleted_at IS NOT NULL
             ORDER BY t.deleted_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// ── COUNTS ────────────────────────────────────────────────────────────────────
function count_tasks($conn) {
    return (int) $conn->query("SELECT COUNT(*) FROM tasks WHERE deleted_at IS NULL")->fetchColumn();
}
function count_pending_tasks($conn) {
    return (int) $conn->query("SELECT COUNT(*) FROM tasks WHERE status='pending' AND deleted_at IS NULL")->fetchColumn();
}
function count_in_progress_tasks($conn) {
    return (int) $conn->query("SELECT COUNT(*) FROM tasks WHERE status='in_progress' AND deleted_at IS NULL")->fetchColumn();
}
function count_completed_tasks($conn) {
    return (int) $conn->query("SELECT COUNT(*) FROM tasks WHERE status='completed' AND deleted_at IS NULL")->fetchColumn();
}
function count_tasks_due_today($conn) {
    return (int) $conn->query("SELECT COUNT(*) FROM tasks WHERE due_date=CURDATE() AND status!='completed' AND deleted_at IS NULL")->fetchColumn();
}
function count_tasks_overdue($conn) {
    return (int) $conn->query("SELECT COUNT(*) FROM tasks WHERE due_date<CURDATE() AND status!='completed' AND deleted_at IS NULL")->fetchColumn();
}
function count_my_tasks($conn, $id) {
    $s = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND deleted_at IS NULL");
    $s->execute([$id]); return (int)$s->fetchColumn();
}
function count_my_pending_tasks($conn, $id) {
    $s = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND status='pending' AND deleted_at IS NULL");
    $s->execute([$id]); return (int)$s->fetchColumn();
}
function count_my_in_progress_tasks($conn, $id) {
    $s = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND status='in_progress' AND deleted_at IS NULL");
    $s->execute([$id]); return (int)$s->fetchColumn();
}
function count_my_completed_tasks($conn, $id) {
    $s = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND status='completed' AND deleted_at IS NULL");
    $s->execute([$id]); return (int)$s->fetchColumn();
}
function count_my_tasks_overdue($conn, $id) {
    $s = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to=? AND due_date<CURDATE() AND status!='completed' AND deleted_at IS NULL");
    $s->execute([$id]); return (int)$s->fetchColumn();
}

// ── EMPLOYEE-SCOPED ───────────────────────────────────────────────────────────
function get_all_tasks_by_employee($conn, $employee_id, $filters = [], $page = 1, $per_page = 10) {
    $filters['assigned_to'] = $employee_id;
    return get_all_tasks($conn, $filters, $page, $per_page);
}
