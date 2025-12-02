<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kluwa Hotel - Luxury Stay in Balikpapan</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body data-page="<?php echo $_GET['route'] ?? 'home'; ?>">
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo url('home'); ?>" class="logo">
                <span class="logo-icon">✦</span>
                <span class="logo-text">KLUWA</span>
                <span class="logo-tagline">HOTEL</span>
            </a>
            
            <ul class="nav-menu">
                <li><a href="<?php echo url('home'); ?>">Beranda</a></li>
                <li class="nav-home-only"><a href="#rooms-section" class="scroll-link">Kamar</a></li>
                <li class="nav-home-only"><a href="#location-section" class="scroll-link">Lokasi</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-dropdown">
                            <a href="<?php echo url('admin/dashboard'); ?>">Admin Panel</a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url('admin/dashboard'); ?>">Dashboard</a></li>
                                <li><a href="<?php echo url('admin/roomTypes'); ?>">Tipe Kamar</a></li>
                                <li><a href="<?php echo url('admin/rooms'); ?>">Kelola Kamar</a></li>
                                <li><a href="<?php echo url('admin/bookings'); ?>">Reservasi</a></li>
                                <li><a href="<?php echo url('admin/payments'); ?>">Pembayaran</a></li>
                            </ul>
                        </li>
                        <li class="nav-user admin-user">
                            <span class="user-icon">👤</span>
                            <span class="user-name"><?php echo e($_SESSION['user_name']); ?></span>
                        </li>
                        <li><a href="<?php echo url('auth/logout'); ?>" class="btn-nav-logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo url('booking/search'); ?>">Reservasi</a></li>
                        <li><a href="<?php echo url('booking/myBookings'); ?>">Booking Saya</a></li>
                        <li class="nav-user">
                            <a href="<?php echo url('auth/profile'); ?>">
                                <span class="user-icon">👤</span>
                                <?php echo e($_SESSION['user_name']); ?>
                            </a>
                        </li>
                        <li><a href="<?php echo url('auth/logout'); ?>" class="btn-nav-logout">Logout</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?php echo url('auth/login'); ?>" class="btn-nav-login">Masuk</a></li>
                    <li><a href="<?php echo url('auth/register'); ?>" class="btn-nav-register">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php 
    $flash = getFlash();
    if ($flash): 
    ?>
        <div class="alert alert-<?php echo $flash['type']; ?>">
            <div class="container">
                <?php echo e($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
