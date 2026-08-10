<?php $this->load->helper('video');
$platform_labels = array('youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'other' => 'Lainnya');
?>
<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php $show_trash = isset($show_trash) && $show_trash; echo educard_start($show_trash ? 'Sampah — Video' : 'Video Sekolah', 'video'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <?php if ($show_trash): ?><a href="<?php echo base_url('admin/videos'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali</a>
            <?php else: ?><a href="<?php echo base_url('admin/videos/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Video</a>
                <a href="<?php echo base_url('admin/videos?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2"><?php echo render_icon('trash-can', 'mr-1'); ?> Sampah</a>
            <?php endif; ?>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Video', $show_trash ? 'Tempat sampah kosong.' : 'Tambahkan video profil, kegiatan, atau dokumentasi sekolah.', 'video', $show_trash ? '' : 'Tambah Video', $show_trash ? '' : 'admin/videos/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="videos-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th style="width:8%">Thumbnail</th><th>Judul</th><th>Platform</th><th>Featured</th><th>Urutan</th><th>Status</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): $thumb = resolve_video_thumbnail($item->thumbnail ?? '', $item->platform ?? 'youtube', $item->video_url ?? ''); ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <?php if (!empty($thumb)): ?>
                                <img src="<?php echo esc_attr($thumb); ?>" alt="" style="width:70px;height:40px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width:70px;height:40px;border-radius:6px;"><?php echo render_icon('video'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-medium"><?php echo esc_html($item->title ?? '-'); ?></td>
                        <td><span class="badge badge-info"><?php echo esc_html($platform_labels[$item->platform] ?? $item->platform); ?></span></td>
                        <td><?php echo !empty($item->is_featured) ? '<span class="badge badge-warning text-dark">Featured</span>' : '<span class="text-muted small">-</span>'; ?></td>
                        <td><?php echo (int) ($item->order_num ?? 0); ?></td>
                        <td>
                            <?php if (($item->status ?? 'draft') === 'published'): ?>
                                <span class="badge badge-success">Terbit</span>
                            <?php else: ?>
                                <span class="badge badge-warning text-dark">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, $item->slug ?? '', 'admin/videos', $show_trash, !empty($item->slug) ? 'video/' . $item->slug : ''); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#videos-table').length){ EduTable.init('#videos-table'); } });</script>
