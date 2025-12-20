<div class="container">
    <div class="page-header">
        <h2>Manajemen Kamar</h2>
        <a href="<?php echo url('admin/roomForm'); ?>" class="btn btn-primary">Tambah Kamar</a>
    </div>
    
    <!-- Filter Rentang Waktu -->
    <div class="filter-card">
        <form method="GET" action="<?php echo url('admin/rooms'); ?>" class="filter-form">
            <input type="hidden" name="route" value="admin/rooms">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i> Tanggal Check-in</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="<?php echo e($startDate ?? date('Y-m-d')); ?>">
                </div>
                <div class="filter-group">
                    <label for="end_date"><i class="fas fa-calendar-alt"></i> Tanggal Check-out</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="<?php echo e($endDate ?? date('Y-m-d', strtotime('+1 day'))); ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cek Ketersediaan</button>
                    <a href="<?php echo url('admin/rooms'); ?>" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Hari Ini</a>
                </div>
            </div>
        </form>
        <?php if (isset($startDate) && isset($endDate)): ?>
            <div class="filter-info">
                <i class="fas fa-info-circle"></i>
                Menampilkan status kamar untuk periode <strong><?php echo formatDate($startDate); ?></strong> sampai <strong><?php echo formatDate($endDate); ?></strong>
            </div>
        <?php else: ?>
            <div class="filter-info">
                <i class="fas fa-info-circle"></i>
                Menampilkan status kamar untuk <strong>hari ini (<?php echo formatDate(date('Y-m-d')); ?>)</strong>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($rooms)): ?>
        <!-- Status Summary -->
        <?php
            $availableCount = count(array_filter($rooms, fn($r) => $r['status'] === 'Available'));
            $occupiedCount = count(array_filter($rooms, fn($r) => $r['status'] === 'Occupied'));
            $maintenanceCount = count(array_filter($rooms, fn($r) => $r['status'] === 'Maintenance'));
        ?>
        <div class="room-status-summary">
            <div class="summary-item summary-available">
                <span class="summary-count"><?php echo $availableCount; ?></span>
                <span class="summary-label">Available</span>
            </div>
            <div class="summary-item summary-occupied">
                <span class="summary-count"><?php echo $occupiedCount; ?></span>
                <span class="summary-label">Occupied</span>
            </div>
            <div class="summary-item summary-maintenance">
                <span class="summary-count"><?php echo $maintenanceCount; ?></span>
                <span class="summary-label">Maintenance</span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 120px;">No. Kamar</th>
                        <th style="width: 180px;">Tipe Kamar</th>
                        <th style="width: 150px;">Harga/Malam</th>
                        <th style="width: 120px; text-align: center;">Status</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><strong><?php echo $room['id']; ?></strong></td>
                            <td><strong><?php echo e($room['room_number']); ?></strong></td>
                            <td><?php echo e($room['room_type_name']); ?></td>
                            <td><?php echo formatRupiah($room['price']); ?></td>
                            <td style="text-align: center;">
                                <span class="status-badge status-<?php echo strtolower($room['status']); ?>">
                                    <?php echo e($room['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo url('admin/roomForm', ['id' => $room['id']]); ?>" 
                                       class="btn btn-small btn-secondary">Edit</a>
                                    <a href="<?php echo url('admin/deleteRoom', ['id' => $room['id']]); ?>" 
                                       class="btn btn-small btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus kamar ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-data">Belum ada kamar.</p>
    <?php endif; ?>
</div>
