<?php

if (!function_exists('getTypeBadgeClass')) {
    function getTypeBadgeClass($type)
    {
        if (strtolower($type) === 'piutang') {
            return ['bg-warning-subtle text-warning', 'badge bg-warning text-dark'];
        }
        return ['bg-success-subtle text-success', 'badge bg-success text-white'];
    }
}

if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status)
    {
        return match (strtolower($status)) {
            'lunas' => 'badge bg-success text-white',
            'jatuh tempo' => 'badge bg-danger text-white',
            default => 'badge bg-warning text-dark',
        };
    }
}
