<div class="container">
    <div class="page-header">
        <h2>Manajemen Tipe Kamar</h2>
        <a href="<?php echo url('admin/roomTypeForm'); ?>" class="btn btn-primary">Tambah Tipe Kamar</a>
    </div>
    
    <?php if (!empty($roomTypes)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 150px;">Nama Tipe</th>
                        <th style="width: 140px;">Harga/Malam</th>
                        <th>Deskripsi</th>
                        <th style="width: 150px;">Gambar</th>
                        <th style="width: 160px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roomTypes as $type): ?>
                        <tr>
                            <td><strong><?php echo $type['id']; ?></strong></td>
                            <td><strong><?php echo e($type['name']); ?></strong></td>
                            <td><?php echo formatRupiah($type['price']); ?></td>
                            <td class="text-muted"><?php echo e(substr($type['description'], 0, 60)); ?><?php echo strlen($type['description']) > 60 ? '...' : ''; ?></td>
                            <td class="text-muted"><?php echo $type['image'] ? e($type['image']) : '-'; ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?php echo url('admin/roomTypeForm', ['id' => $type['id']]); ?>" 
                                       class="btn btn-small btn-secondary">Edit</a>
                                    <a href="<?php echo url('admin/deleteRoomType', ['id' => $type['id']]); ?>" 
                                       class="btn btn-small btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus tipe kamar ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-data">Belum ada tipe kamar.</p>
    <?php endif; ?>
</div>
