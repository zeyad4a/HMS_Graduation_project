<?php
date_default_timezone_set('Africa/Cairo');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/audit.php';

function hms_role_home(string $role): string
{
    return match ($role) {
        'System Admin', 'Admin', 'Doctor', 'Patient', 'User' => '/modules/dashboard.php',
        default => '/index.php',
    };
}

function hms_redirect_with_popup(string $message, string $target = '/index.php'): void
{
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $safeTarget = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');

    echo "<script>alert('{$safeMessage}'); window.location.href='{$safeTarget}';</script>";
    exit();
}

function hms_is_authorized_path(string $role, string $path): bool
{
    if ($path === '/modules/dashboard.php') {
        return true;
    }

    // Shared module and includes pages are accessible to all logged-in users
    if (str_starts_with($path, '/modules/shared/') || str_starts_with($path, '/includes/')) {
        return true;
    }

    $rolePaths = [
        'System Admin' => ['/modules/super-admin/', '/modules/admin/'],
        'Admin' => ['/modules/admin/'],
        'Doctor' => ['/modules/doctor/'],
        'Patient' => ['/modules/patient/'],
        'User' => ['/modules/user/'],
    ];

    foreach ($rolePaths[$role] ?? [] as $prefix) {
        if (str_starts_with($path, $prefix)) {
            return true;
        }
    }

    return false;
}

if (empty($_SESSION['logged_in']) || empty($_SESSION['login']) || empty($_SESSION['role'])) {
    header("Location: /index.php");
    exit();
}

$currentPath = parse_url($_SERVER['PHP_SELF'] ?? '', PHP_URL_PATH) ?: '';
$auditConnect = @new mysqli("localhost", "root", "", "hms");
$auditEnabled = $auditConnect instanceof mysqli && !$auditConnect->connect_error;

if ($auditEnabled) {
    hms_audit_auto_log_request($auditConnect);
}

if (str_starts_with($currentPath, '/modules/') && !hms_is_authorized_path($_SESSION['role'], $currentPath)) {
    if ($auditEnabled) {
        hms_audit_log($auditConnect, 'access.denied', [
            'entity_type' => 'request',
            'entity_id' => basename($currentPath) ?: $currentPath,
            'description' => 'Unauthorized access attempt',
            'details' => [
                'path' => $currentPath,
                'role' => $_SESSION['role'] ?? null,
            ],
        ]);
        $auditConnect->close();
    }

    hms_redirect_with_popup(
        'Access denied. You are not allowed to open this page.',
        hms_role_home($_SESSION['role'])
    );
}

if ($auditEnabled) {
    $auditConnect->close();
}
