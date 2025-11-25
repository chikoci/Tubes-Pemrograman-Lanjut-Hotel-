<div class="container">
    <div class="page-header">
        <h2>Manajemen Kamar</h2>
        <a href="<?php echo url('admin/roomForm'); ?>" class="btn btn-primary">Tambah Kamar</a>
    </div>
    
    <?php if (!empty($rooms)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nomor Kamar</th>
                    <th>Tipe Kamar</th>
                    <th>Harga/Malam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?php echo $room['id']; ?></td>
                        <td><?php echo e($room['room_number']); ?></td>
                        <td><?php echo e($room['room_type_name']); ?></td>
                        <td><?php echo formatRupiah($room['price']); ?></td>
                        <td><span class="status-badge status-<?php echo strtolower($room['status']); ?>">
                            <?php echo e($room['status']); ?>
                        </span></td>
                        <td>
							<a href="<?php echo url('admin/roomForm', ['id' => $room['id']]); ?>" 
                               class="btn btn-small btn-secondary">Edit</a>
							<a href="<?php echo url('admin/deleteRoom', ['id' => $room['id']]); ?>" 
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Yakin ingin menghapus kamar ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Belum ada kamar.</p>
    <?php endif; ?>
</div>
