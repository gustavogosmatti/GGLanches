<div class="row">
    <?php $_from = $this->_tpl_vars['Forms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['forms'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['forms']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['Form']):
        $this->_foreach['forms']['iteration']++;
?>
        <div class="col-md-12">
            <?php echo $this->_tpl_vars['Form']; ?>

            <?php if (! ($this->_foreach['forms']['iteration'] == $this->_foreach['forms']['total'])): ?><hr><?php endif; ?>
        </div>
    <?php endforeach; endif; unset($_from); ?>
</div>

<div class="btn-toolbar pull-right">

    <button class="btn btn-default js-cancel">
        <?php echo $this->_tpl_vars['Captions']->GetMessageString('Cancel'); ?>

    </button>
    
    <div class="btn-group">
        <button type="submit" class="btn btn-primary js-save js-primary-save">
            <?php echo $this->_tpl_vars['Captions']->GetMessageString('Save'); ?>

        </button>
        <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="#" class="js-save" data-action="edit"><?php echo $this->_tpl_vars['Captions']->GetMessageString('SaveAndEdit'); ?>
</a></li>
        </ul>
    </div>

</div>