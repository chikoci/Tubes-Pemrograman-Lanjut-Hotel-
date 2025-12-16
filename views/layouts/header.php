<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kluwa Hotel - Luxury Stay in Balikpapan</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                        <li class="nav-user-dropdown">
                            <button class="user-dropdown-toggle" onclick="toggleUserMenu()">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo e($_SESSION['user_name']); ?></span>
                                <i class="fas fa-ellipsis-v dropdown-dots"></i>
                            </button>
                            <ul class="user-dropdown-menu" id="userDropdownMenu">
                                <li class="dropdown-header">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo e($_SESSION['user_name']); ?></span>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li><a href="<?php echo url('auth/profile'); ?>"><i class="fas fa-id-card"></i> Profil Saya</a></li>
                                <li><a href="<?php echo url('auth/logout'); ?>" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-user-dropdown">
                            <button class="user-dropdown-toggle" onclick="toggleUserMenu()">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo e($_SESSION['user_name']); ?></span>
                                <i class="fas fa-ellipsis-v dropdown-dots"></i>
                            </button>
                            <ul class="user-dropdown-menu" id="userDropdownMenu">
                                <li class="dropdown-header">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo e($_SESSION['user_name']); ?></span>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li><a href="<?php echo url('booking/search'); ?>"><i class="fas fa-calendar-plus"></i> Reservasi</a></li>
                                <li><a href="<?php echo url('booking/myBookings'); ?>"><i class="fas fa-list-alt"></i> Booking Saya</a></li>
                                <li><a href="<?php echo url('auth/profile'); ?>"><i class="fas fa-id-card"></i> Profil Saya</a></li>
                                <li class="dropdown-divider"></li>
                                <li><a href="<?php echo url('auth/logout'); ?>" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
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
