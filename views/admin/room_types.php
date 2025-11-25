<div class="container">
    <div class="page-header">
        <h2>Manajemen Tipe Kamar</h2>
        <a href="<?php echo url('admin/roomTypeForm'); ?>" class="btn btn-primary">Tambah Tipe Kamar</a>
    </div>
    
    <?php if (!empty($roomTypes)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Harga/Malam</th>
                    <th>Deskripsi</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roomTypes as $type): ?>
                    <tr>
                        <td><?php echo $type['id']; ?></td>
                        <td><?php echo e($type['name']); ?></td>
                        <td><?php echo formatRupiah($type['price']); ?></td>
                        <td><?php echo e(substr($type['description'], 0, 50)); ?><?php echo strlen($type['description']) > 50 ? '...' : ''; ?></td>
                        <td><?php echo e($type['image']); ?></td>
                        <td>
							<a href="<?php echo url('admin/roomTypeForm', ['id' => $type['id']]); ?>" 
                               class="btn btn-small btn-secondary">Edit</a>
							<a href="<?php echo url('admin/deleteRoomType', ['id' => $type['id']]); ?>" 
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Yakin ingin menghapus tipe kamar ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Belum ada tipe kamar.</p>
    <?php endif; ?>
</div>
