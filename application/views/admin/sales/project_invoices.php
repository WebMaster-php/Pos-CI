<?php //include_once(APPPATH . 'views/admin/invoices/invoices_top_stats.php'); ?>
<?php include_once(APPPATH . 'views/admin/invoices/invoices_top_stats_so.php'); ?>
<div class="project_invoices">
    <?php include_once(APPPATH.'views/admin/invoices/filter_params.php'); ?>
    <?php //$this->load->view('admin/invoices/list_template'); ?>
    <?php $this->load->view('admin/invoices/list_template_so'); ?>
</div>
