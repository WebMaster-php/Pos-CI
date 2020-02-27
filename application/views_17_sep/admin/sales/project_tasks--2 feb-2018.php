<!-- Project Tasks -->
<?php
    if($project->settings->hide_tasks_on_main_tasks_table == '1') {
        echo '<i class="fa fa-exclamation fa-2x pull-left" data-toggle="tooltip" data-title="'._l('project_hide_tasks_settings_info').'"></i>';
    }
?>
<div class="tasks-table">

    <table class="table dt-table table-items-groups" data-order-col="0" data-order-type="asc">
        <thead>
        <tr>
            <th><?php echo 'Task'; ?></th>

        </tr>
        </thead>
        <tbody>
        <?php
        foreach($tasks as $group){ ?>
            <tr data-group-row-id="<?php echo $group['id']; ?>">
                <td data-order="<?php echo $group['task_name']; ?>">
                    <?php echo $group['task_name']; ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php //init_relation_tasks_table(array( 'data-new-rel-id'=>$project->id,'data-new-rel-type'=>'project')); ?>
</div>
