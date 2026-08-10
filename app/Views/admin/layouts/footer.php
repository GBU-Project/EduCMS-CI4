</div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer text-sm" style="border-top: 1px solid #e2e8f0; background-color: #ffffff;">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      EduCMS v1.2 (LOCKED)
    </div>
    <!-- Default to the left -->
    <strong>Hak Cipta &copy; 2026 <a href="#" style="color: #6366f1;">EduCMS</a>.</strong> Hak cipta dilindungi undang-undang.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?php echo base_url('assets/shared/jquery/jquery.min.js'); ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('assets/admin/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/admin/js/adminlte.min.js'); ?>"></script>
<!-- DataTables JS -->
<script src="<?php echo base_url('assets/admin/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/admin/datatables/js/dataTables.bootstrap4.min.js'); ?>"></script>
<!-- SweetAlert2 JS -->
<script src="<?php echo base_url('assets/admin/sweetalert2/sweetalert2.all.min.js'); ?>"></script>
<!-- EduCMS Custom JS -->
<script src="<?php echo base_url('assets/admin/js/educms-admin.js'); ?>"></script>
<?php
    // RC4 Blueprint v1.2, TASK 15: Image Picker modal, loaded once globally
    // so every eduform_file() image field (Posts, Pages, Sliders,
    // Achievements, Extracurriculars, Staff, Teachers) can open it without
    // each view needing to load it individually.
    echo view('admin/components/image_picker');
?>
</body>
</html>
