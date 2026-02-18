<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Panel'; ?> - BuySimple Admin</title>
    <link rel="stylesheet" href="<?php echo Helper::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo Helper::asset('css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-shopping-bag"></i> BuySimple </h2>
            </div>
            <nav class="sidebar-nav">
                <a href="<?php echo Helper::url('admin/dashboard'); ?>" class="nav-item">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
                <a href="<?php echo Helper::url('admin/categories'); ?>" class="nav-item">
                    <i class="fas fa-folder"></i> Categories
                </a>
                <a href="<?php echo Helper::url('admin/products'); ?>" class="nav-item">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="<?php echo Helper::url('admin/hero-slides'); ?>" class="nav-item">
                    <i class="fas fa-images"></i> Hero Slides
                </a>
                <a href="<?php echo Helper::url('admin/collections'); ?>" class="nav-item">
                    <i class="fas fa-th-large"></i> Collections
                </a>
                <a href="<?php echo Helper::url('admin/banner-texts'); ?>" class="nav-item">
                    <i class="fas fa-bullhorn"></i> Banner Texts
                </a>
                <a href="<?php echo Helper::url('admin/site-settings'); ?>" class="nav-item">
                    <i class="fas fa-cog"></i> Site Settings
                </a>
                <a href="<?php echo Helper::url('admin/orders'); ?>" class="nav-item">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
                <a href="<?php echo Helper::url(''); ?>" class="nav-item">
                    <i class="fas fa-home"></i> View Site
                </a>
                <a href="<?php echo Helper::url('logout'); ?>" class="nav-item">
                    <i class="fas fa-sign-out"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1><?php echo $pageTitle ?? 'Admin Panel'; ?></h1>
                <div class="admin-user">
                    <span><?php echo htmlspecialchars(Auth::user()['name'] ?? 'Admin'); ?></span>
                </div>
            </header>

            <?php if ($message = Session::flash('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($message = Session::flash('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="admin-content">

