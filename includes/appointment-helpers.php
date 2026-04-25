<?php
require_once __DIR__ . '/../includes/auth.php';

/**
 * Shared appointment UI helpers.
 */

function appt_status_badge(array $row): string
{
    $us = (int)($row['userStatus'] ?? -1);
    $ds = (int)($row['doctorStatus'] ?? -1);

    if ($us === 1 && $ds === 1) {
        $bg = "bg-blue-50 text-blue-700 ring-blue-600/20";
        $label = "Active";
    } elseif ($us === 0) {
        $bg = "bg-red-50 text-red-700 ring-red-600/20";
        $label = "Cancelled";
    } elseif ($ds === 0) {
        $bg = "bg-orange-50 text-orange-700 ring-orange-600/20";
        $label = "Cancelled";
    } elseif ($us === 2 && $ds === 2) {
        $bg = "bg-green-50 text-green-700 ring-green-600/20";
        $label = "Done";
    } else {
        $bg = "bg-gray-50 text-gray-600 ring-gray-400/20";
        $label = "Unknown";
    }

    return "<span class='inline-flex items-center rounded-md px-2 py-1 text-sm font-medium ring-1 ring-inset {$bg}'>{$label}</span>";
}

function appt_cancelled_by_text(array $row): string
{
    $us = (int)($row['userStatus'] ?? -1);
    $ds = (int)($row['doctorStatus'] ?? -1);
    $by = trim((string)($row['cancelledBy'] ?? ''));

    if (($us === 1 && $ds === 1) || ($us === 2 && $ds === 2)) {
        return '-';
    }

    if ($by !== '') {
        return $by;
    }

    if ($us === 0) {
        return 'Patient';
    }

    if ($ds === 0) {
        return 'Doctor';
    }

    return '-';
}

function appt_cancelled_by(array $row): string
{
    $byText = appt_cancelled_by_text($row);

    if ($byText === '-') {
        return '';
    }

    return "<span class='block text-xs text-gray-400 mt-1'>Cancelled by: <span class='font-medium text-red-500'>" . htmlspecialchars($byText) . "</span></span>";
}

function appt_action_buttons(array $row, string $returnUrl): string
{
    $role = $_SESSION['role'] ?? '';
    $apid = (int)($row['apid'] ?? 0);
    $us = (int)($row['userStatus'] ?? -1);
    $ds = (int)($row['doctorStatus'] ?? -1);
    $isActive = ($us === 1 && $ds === 1);

    $base = '/includes/appointment-action.php';
    $ret = urlencode($returnUrl);
    $html = '<div class="flex gap-1 justify-center flex-wrap">';

    if ($role === 'Patient') {
        if ($isActive) {
            $html .= "<a href='{$base}?action=edit&id={$apid}&return={$ret}'
                class='inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 hover:bg-blue-100'>
                <i class='bi bi-pencil me-1'></i>Edit</a>";

            $html .= "<a href='{$base}?action=cancel&id={$apid}&return={$ret}'
                class='inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100'
                onclick=\"return confirm('Cancel this appointment?');\">
                <i class='bi bi-x-circle me-1'></i>Cancel</a>";
        }
    } elseif ($role === 'Doctor') {
        if ($isActive) {
            $html .= "<a href='{$base}?action=cancel&id={$apid}&return={$ret}'
                class='inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100'
                onclick=\"return confirm('Cancel this appointment?');\">
                <i class='bi bi-x-circle me-1'></i>Cancel</a>";
        }
    } elseif (in_array($role, ['Admin', 'System Admin'], true)) {
        if ($isActive) {
            $html .= "<a href='{$base}?action=edit&id={$apid}&return={$ret}'
                class='inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 hover:bg-blue-100'>
                <i class='bi bi-pencil me-1'></i>Edit</a>";

            $html .= "<a href='{$base}?action=cancel&id={$apid}&return={$ret}'
                class='inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100'
                onclick=\"return confirm('Cancel this appointment?');\">
                <i class='bi bi-x-circle me-1'></i>Cancel</a>";
        }
    } else {
        $html .= "<span class='text-gray-400 text-xs'>-</span>";
    }

    $html .= '</div>';
    return $html;
}
?>
