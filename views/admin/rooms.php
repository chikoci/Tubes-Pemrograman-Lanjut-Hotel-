<div class="container">
    <div class="page-header">
        <h2>Manajemen Kamar</h2>
        <a href="<?php echo url('admin/roomForm'); ?>" class="btn btn-primary">Tambah Kamar</a>
    </div>
    
    <?php if (!empty($rooms)): ?>
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
