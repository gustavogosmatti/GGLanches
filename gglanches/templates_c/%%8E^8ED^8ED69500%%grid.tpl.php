<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'to_json', 'compare/grid.tpl', 45, false),)), $this); ?>
<p>
    <a href="<?php echo $this->_tpl_vars['Page']->getLink(); ?>
">&larr; <?php echo $this->_tpl_vars['Captions']->getMessageString('ReturnFromDetailToMaster'); ?>
</a>
</p>

<?php if ($this->_tpl_vars['DataGrid']['isDiffers']): ?>
    <p class="btn-group" data-toggle="buttons">
        <label class="btn btn-default active">
            <input type="radio" name="compare_mode" class="js-compare-mode" data-mode="diff" autocomplete="off" checked> <?php echo $this->_tpl_vars['Captions']->getMessageString('CompareShowDiff'); ?>

        </label>
        <label class="btn btn-default">
            <input type="radio" name="compare_mode" class="js-compare-mode" data-mode="all" autocomplete="off"> <?php echo $this->_tpl_vars['Captions']->getMessageString('CompareShowAll'); ?>

        </label>
    </p>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['DataGrid']['records'] ) > 0): ?>
    <table class="table table-bordered">
    <?php if (count ( $this->_tpl_vars['DataGrid']['columns']['HeaderColumns'] ) > 0): ?>
        <tr>
            <th>
            </th>
            <?php $_from = $this->_tpl_vars['DataGrid']['records']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['record']):
?>
                <th>
                    <?php $_from = $this->_tpl_vars['DataGrid']['columns']['HeaderColumns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['column']):
?>
                        <?php $this->assign('columnName', $this->_tpl_vars['column']->getName()); ?>
                        <div><?php echo $this->_tpl_vars['record']['HeaderColumns'][$this->_tpl_vars['columnName']]['Data']; ?>
</div>
                    <?php endforeach; endif; unset($_from); ?>
                </th>
            <?php endforeach; endif; unset($_from); ?>
        </tr>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['DataGrid']['columns']['DataColumns']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['column']):
?>
        <?php $this->assign('columnName', $this->_tpl_vars['column']->getName()); ?>
        <tr<?php if (! $this->_tpl_vars['DataGrid']['columnsDiff'][$this->_tpl_vars['columnName']]): ?> class="success"<?php if ($this->_tpl_vars['DataGrid']['isDiffers']): ?> style="display: none"<?php endif; ?><?php endif; ?> data-diff="<?php if ($this->_tpl_vars['DataGrid']['columnsDiff'][$this->_tpl_vars['columnName']]): ?>true<?php else: ?>false<?php endif; ?>">
            <th><?php echo $this->_tpl_vars['column']->getCaption(); ?>
</th>
            <?php $_from = $this->_tpl_vars['DataGrid']['records']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['record']):
?>
                <?php $this->assign('recordColumn', $this->_tpl_vars['record']['DataColumns'][$this->_tpl_vars['columnName']]); ?>
                <td><?php echo $this->_tpl_vars['recordColumn']['Data']; ?>
</td>
            <?php endforeach; endif; unset($_from); ?>
        </tr>
    <?php endforeach; endif; unset($_from); ?>
    <tr class="js-selection-actions-container" data-selection-id="<?php echo $this->_tpl_vars['DataGrid']['SelectionId']; ?>
">
        <th></th>
        <?php $_from = $this->_tpl_vars['DataGrid']['records']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['record']):
?>
            <td><a href="<?php echo $this->_tpl_vars['record']['RemoveLink']; ?>
" class="js-action" data-type="compare-remove" data-value="<?php echo smarty_function_to_json(array('value' => $this->_tpl_vars['record']['Keys'],'escape' => true), $this);?>
"><?php echo $this->_tpl_vars['Captions']->getMessageString('CompareRemove'); ?>
</a></td>
        <?php endforeach; endif; unset($_from); ?>
    </tr>
    </table>
<?php else: ?>
    <div class="alert alert-warning"><?php echo $this->_tpl_vars['Captions']->getMessageString('NoDataToDisplay'); ?>
</div>
<?php endif; ?>