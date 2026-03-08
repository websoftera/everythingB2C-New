<?php
/**
 * Admin Authentication & Authorization Check
 * Include this file at the start of any admin page to check permissions
 * 
 * Usage:
 *   require_once 'includes/auth-check.php';
 *   checkAdminPermission('permission_code');
 *   
 * Or multiple permissions (OR logic):
 *   checkAdminPermission(['permission_code1', 'permission_code2']);
 *   
 * You can also use checkAdminPermissionAll for AND logic:
 *   checkAdminPermissionAll(['permission_code1', 'permission_code2']);
 */

if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

/**
 * Check if admin is logged in and has required permission
 * If not, redirects to login or permission denied page
 * 
 * @param string|array $requiredPermission - Single permission code or array of codes
 * @param bool $requireAll - If true, requires ALL permissions, otherwise ANY permission
 */
function checkAdminPermission($requiredPermission, $requireAll = false) {
    // Check if admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/login.php');
        exit;
    }
    
    // Get admin permissions from session or database
    $adminId = $_SESSION['admin_id'];
    $permissions = $_SESSION['admin_permissions'] ?? [];
    
    // If no permissions in session, fetch from database
    if (empty($permissions)) {
        $role = getAdminRole($adminId);
        if ($role) {
            $rolePermissions = getRolePermissions($role['id']);
            $permissions = array_column($rolePermissions, 'code');
            $_SESSION['admin_permissions'] = $permissions;
        }
    }
    
    // Normalize to array
    if (!is_array($requiredPermission)) {
        $requiredPermission = [$requiredPermission];
    }
    
    // Check permissions
    if ($requireAll) {
        $hasPermission = count(array_intersect($requiredPermission, $permissions)) === count($requiredPermission);
    } else {
        $hasPermission = count(array_intersect($requiredPermission, $permissions)) > 0;
    }
    
    if (!$hasPermission) {
        // Redirect to permission denied page
        if (file_exists(__DIR__ . '/../permission-denied.php')) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/permission-denied.php');
        } else {
            http_response_code(403);
            die('Access Denied: You do not have permission to access this page.');
        }
        exit;
    }
}

/**
 * Check if admin has a permission (alias for checkAdminPermission with single permission)
 * @param string $permissionCode
 */
function requirePermission($permissionCode) {
    checkAdminPermission($permissionCode, false);
}

/**
 * Check if admin has all specified permissions
 * @param array $permissionCodes
 */
function requireAllPermissions($permissionCodes) {
    checkAdminPermission($permissionCodes, true);
}

/**
 * Get current admin's info with role details
 * @return array|null
 */
function getCurrentAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }
    
    return getAdminById($_SESSION['admin_id']);
}

/**
 * Check if current admin has a specific permission
 * @param string $permissionCode
 * @return bool
 */
function canAccess($permissionCode) {
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }
    
    $permissions = $_SESSION['admin_permissions'] ?? [];
    if (empty($permissions)) {
        $adminId = $_SESSION['admin_id'];
        $role = getAdminRole($adminId);
        if ($role) {
            $rolePermissions = getRolePermissions($role['id']);
            $permissions = array_column($rolePermissions, 'code');
        }
    }
    
    return in_array($permissionCode, $permissions);
}
