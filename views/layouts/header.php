<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo url('home'); ?>" class="logo">Hotel System</a>
            
            <ul class="nav-menu">
                <li><a href="<?php echo url('home'); ?>">Home</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?php echo url('admin/dashboard'); ?>">Dashboard Admin</a></li>
                        <li><a href="<?php echo url('admin/roomTypes'); ?>">Tipe Kamar</a></li>
                        <li><a href="<?php echo url('admin/rooms'); ?>">Kamar</a></li>
                        <li><a href="<?php echo url('admin/payments'); ?>">Pembayaran</a></li>
                        <li><a href="<?php echo url('admin/bookings'); ?>">Booking</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo url('booking/search'); ?>">Cari Kamar</a></li>
                        <li><a href="<?php echo url('booking/myBookings'); ?>">Booking Saya</a></li>
                        <li><a href="<?php echo url('auth/profile'); ?>">Profil Saya</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo url('auth/logout'); ?>">Logout (<?php echo e($_SESSION['user_name']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="<?php echo url('auth/login'); ?>">Login</a></li>
                    <li><a href="<?php echo url('auth/register'); ?>">Daftar</a></li>
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
