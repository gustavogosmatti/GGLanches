<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page_includes.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthentication()->applyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class pedidos_itensvendasPage extends DetailPage
    {
        protected function DoBeforeCreate()
        {
            $selectQuery = 'select *,(itemvalor * itemqtd) as itemsub from itens';
            $insertQuery = array('insert into itens (itemproduto, itemvalor, itemqtd, itemcodpedido)
            values (:itemproduto, :itemvalor, :itemqtd, :itemcodpedido)');
            $updateQuery = array('UPDATE itens set 
            itemproduto=:itemproduto, 
            itemvalor =:itemvalor, 
            itemqtd=:itemqtd, 
            itemcodpedido=:itemcodpedido
            WHERE iditem = :iditem');
            $deleteQuery = array('DELETE FROM itens WHERE iditem =:iditem');
            $this->dataset = new QueryDataset(
              MyPDOConnectionFactory::getInstance(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'itensvendas');
            $this->dataset->addFields(
                array(
                    new IntegerField('iditem', false, true),
                    new IntegerField('itemproduto'),
                    new IntegerField('itemvalor'),
                    new IntegerField('itemqtd'),
                    new StringField('itemcodpedido'),
                    new IntegerField('itemsub')
                )
            );
            $this->dataset->AddLookupField('itemproduto', 'lanche', new IntegerField('idlanche'), new StringField('nome', false, false, false, false, 'itemproduto_nome', 'itemproduto_nome_lanche'), 'itemproduto_nome_lanche');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(30);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'iditem', 'iditem', 'Iditem'),
                new FilterColumn($this->dataset, 'itemproduto', 'itemproduto_nome', 'Lanche'),
                new FilterColumn($this->dataset, 'itemvalor', 'itemvalor', 'Valor'),
                new FilterColumn($this->dataset, 'itemqtd', 'itemqtd', 'Quantidade'),
                new FilterColumn($this->dataset, 'itemcodpedido', 'itemcodpedido', 'Itemcodpedido'),
                new FilterColumn($this->dataset, 'itemsub', 'itemsub', 'Subtotal')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
    
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new AjaxOperation(OPERATION_EDIT,
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'),
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'), $this->dataset,
                    $this->GetGridEditHandler(), $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for nome field
            //
            $column = new TextViewColumn('itemproduto', 'itemproduto_nome', 'Lanche', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for itemvalor field
            //
            $column = new NumberViewColumn('itemvalor', 'itemvalor', 'Valor', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator('.');
            $column->setDecimalSeparator(',');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for itemqtd field
            //
            $column = new NumberViewColumn('itemqtd', 'itemqtd', 'Quantidade', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(0);
            $column->setThousandsSeparator('.');
            $column->setDecimalSeparator('');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for itemsub field
            //
            $column = new NumberViewColumn('itemsub', 'itemsub', 'Subtotal', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator('.');
            $column->setDecimalSeparator(',');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
    
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for itemproduto field
            //
            $editor = new DynamicCombobox('itemproduto_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`lanche`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idlanche', true, true, true),
                    new StringField('nome', true),
                    new StringField('descricao', true),
                    new IntegerField('preco'),
                    new IntegerField('lancheqtd', true),
                    new IntegerField('idprodutoestoque'),
                    new IntegerField('vezespedido', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Lanche', 'itemproduto', 'itemproduto_nome', 'edit_itemproduto_nome_search', $editor, $this->dataset, $lookupDataset, 'idlanche', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for itemvalor field
            //
            $editor = new TextEdit('itemvalor_edit');
            $editColumn = new CustomEditColumn('Valor', 'itemvalor', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for itemqtd field
            //
            $editor = new SpinEdit('itemqtd_edit');
            $editColumn = new CustomEditColumn('Quantidade', 'itemqtd', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxValueValidator(999, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinValueValidator(1, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new NumberValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('NumberValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for itemcodpedido field
            //
            $editor = new TextEdit('itemcodpedido_edit');
            $editColumn = new CustomEditColumn('Itemcodpedido', 'itemcodpedido', $editor, $this->dataset);
            $editColumn->SetReadOnly(true);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
    
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for itemproduto field
            //
            $editor = new DynamicCombobox('itemproduto_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`lanche`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idlanche', true, true, true),
                    new StringField('nome', true),
                    new StringField('descricao', true),
                    new IntegerField('preco'),
                    new IntegerField('lancheqtd', true),
                    new IntegerField('idprodutoestoque'),
                    new IntegerField('vezespedido', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Lanche', 'itemproduto', 'itemproduto_nome', 'insert_itemproduto_nome_search', $editor, $this->dataset, $lookupDataset, 'idlanche', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for itemqtd field
            //
            $editor = new SpinEdit('itemqtd_edit');
            $editColumn = new CustomEditColumn('Quantidade', 'itemqtd', $editor, $this->dataset);
            $editColumn->SetInsertDefaultValue('1');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MaxValueValidator(999, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MaxValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new MinValueValidator(1, StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('MinValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $validator = new NumberValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('NumberValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for itemcodpedido field
            //
            $editor = new TextEdit('itemcodpedido_edit');
            $editColumn = new CustomEditColumn('Itemcodpedido', 'itemcodpedido', $editor, $this->dataset);
            $editColumn->SetReadOnly(true);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
    
        }
    
        protected function AddExportColumns(Grid $grid)
        {
    
        }
    
        private function AddCompareColumns(Grid $grid)
        {
    
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        
        public function GetEnableModalGridInsert() { return true; }
        public function GetEnableModalGridEdit() { return true; }
        
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(true);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->setAllowSortingByDialog(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(false);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && false);
            $result->setTableBordered(true);
            $result->setTableCondensed(true);
            $result->SetTotal('itemsub', PredefinedAggregate::$Sum);
            $result->setReloadPageAfterAjaxOperation(true);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(false);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(false);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(false);
            $this->setAllowPrintSelectedRecords(false);
            $this->setExportListAvailable(array());
            $this->setExportSelectedRecordsAvailable(array());
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array());
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`lanche`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idlanche', true, true, true),
                    new StringField('nome', true),
                    new StringField('descricao', true),
                    new IntegerField('preco'),
                    new IntegerField('lancheqtd', true),
                    new IntegerField('idprodutoestoque'),
                    new IntegerField('vezespedido', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_itemproduto_nome_search', 'idlanche', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`lanche`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idlanche', true, true, true),
                    new StringField('nome', true),
                    new StringField('descricao', true),
                    new IntegerField('preco'),
                    new IntegerField('lancheqtd', true),
                    new IntegerField('idprodutoestoque'),
                    new IntegerField('vezespedido', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_itemproduto_nome_search', 'idlanche', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
            $cellFontColor['itemsub'] = 'red';
            $cellFontColor['itemvalor'] = 'red';
            $cellFontColor['itemproduto'] = 'blue';
            $cellFontColor['itemqtd'] = 'blue';
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
            if($columnName == 'itemsub')
            {
            $customText = '<span style="font-size:20px; color: red">TOTAL R$ ' . $totalValue . '</span>';
            $handled = true;
            }
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
            $lanches = array();
            
            $id = $rowData['itemproduto'];
            $sql = "SELECT preco, lancheqtd FROM lanche WHERE idlanche = {$id}";
            
            $this->GetConnection()->ExecQueryToArray($sql, $lanches);
            
            $estoque = 0;
            
            
            foreach($lanches as $lanche)
            {
            $rowData['itemvalor'] = $lanche['preco'];
            $estoque = $lanche['lancheqtd'];
            }
            
            //verifico o estoque antes de inserir
            if($rowData['itemqtd'] > $estoque )
            {
            $cancel = true;
            $message = ' Estoque insuficiente, em estoque: ' . $estoque . ' tentando inserir: ' . $rowData['itemqtd']; 
            }
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
            $lanches = array();
            
            $id = $rowData['itemproduto'];
            $sql = "SELECT preco, lancheqtd FROM lanche WHERE idlanche = {$id}";
            
            $this->GetConnection()->ExecQueryToArray($sql, $lanches);
            //pego estoque
            $estoque = $lanches[0]['lancheqtd'];
            //pego quantidade
            $qtd = $rowData['itemqtd'] - $oldRowData['itemqtd'];
            //verifico o estoque antes de inserir
            if($qtd > $estoque )
            {
            $cancel = true;
            $message = ' Estoque insuficiente, em estoque: ' . $estoque . ' tentando inserir + : ' . $qtd; 
            }
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
            //pegando dados do produto
            
            $id = $rowData['itemproduto'];
            $qtd = $rowData['itemqtd'];
            
            
            //removendo do estoque
            $sql = "UPDATE lanche SET lancheqtd = ( lancheqtd - {$qtd} ), vezespedido = vezespedido+1  WHERE idlanche = {$id}";
            $this->GetConnection()->ExecSQL($sql);
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
            //pegando dados do produto
            
            $id = $rowData['itemproduto'];
            $qtd = $rowData['itemqtd'] - $oldRowData['itemqtd'];
            
            
            
            //removendo ou adicionando ao estoque
            $sql = "UPDATE lanche SET lancheqtd = ( lancheqtd - {$qtd} ) WHERE idlanche = {$id}";
            $this->GetConnection()->ExecSQL($sql);
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
            //pegando dados do produto
            
            $id = $rowData['itemproduto'];
            $qtd = $rowData['itemqtd'];
            
            
            //voltar ao estoque apos remover do pedido
            $sql = "UPDATE lanche SET lancheqtd = ( lancheqtd + {$qtd} ), vezespedido = vezespedido - 1 WHERE idlanche = {$id}";
            $this->GetConnection()->ExecSQL($sql);
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }
    
    // OnBeforePageExecute event handler
    
    
    
    class pedidosPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`pedidos`');
            $this->dataset->addFields(
                array(
                    new IntegerField('idpedido', true, true, true),
                    new StringField('codigopedido'),
                    new DateField('datapedido'),
                    new IntegerField('idcliente'),
                    new IntegerField('idfuncionario', true)
                )
            );
            $this->dataset->AddLookupField('idcliente', 'cliente', new IntegerField('idcliente'), new StringField('nome', false, false, false, false, 'idcliente_nome', 'idcliente_nome_cliente'), 'idcliente_nome_cliente');
            $this->dataset->AddLookupField('idfuncionario', 'funcionario', new IntegerField('idfuncionario'), new StringField('nome', false, false, false, false, 'idfuncionario_nome', 'idfuncionario_nome_funcionario'), 'idfuncionario_nome_funcionario');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(30);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'idpedido', 'idpedido', 'Idpedido'),
                new FilterColumn($this->dataset, 'datapedido', 'datapedido', 'Data'),
                new FilterColumn($this->dataset, 'codigopedido', 'codigopedido', 'Codigo'),
                new FilterColumn($this->dataset, 'idcliente', 'idcliente_nome', 'Cliente'),
                new FilterColumn($this->dataset, 'idfuncionario', 'idfuncionario_nome', 'Funcionário')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['datapedido'])
                ->addColumn($columns['codigopedido'])
                ->addColumn($columns['idcliente'])
                ->addColumn($columns['idfuncionario']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('datapedido')
                ->setOptionsFor('codigopedido')
                ->setOptionsFor('idcliente')
                ->setOptionsFor('idfuncionario');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new AjaxOperation(OPERATION_EDIT,
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'),
                    $this->GetLocalizerCaptions()->GetMessageString('Edit'), $this->dataset,
                    $this->GetGridEditHandler(), $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            if (GetCurrentUserPermissionSetForDataSource('pedidos.itensvendas')->HasViewGrant() && $withDetails)
            {
            //
            // View column for pedidos_itensvendas detail
            //
            $column = new DetailColumn(array('codigopedido'), 'pedidos.itensvendas', 'pedidos_itensvendas_handler', $this->dataset, 'Detalhes do pedido');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $grid->AddViewColumn($column);
            }
            
            //
            // View column for datapedido field
            //
            $column = new DateTimeViewColumn('datapedido', 'datapedido', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d/m/Y');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for codigopedido field
            //
            $column = new TextViewColumn('codigopedido', 'codigopedido', 'Codigo', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('idcliente', 'idcliente_nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('idfuncionario', 'idfuncionario_nome', 'Funcionário', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
    
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for datapedido field
            //
            $editor = new DateTimeEdit('datapedido_edit', false, 'd/m/Y');
            $editColumn = new CustomEditColumn('Data', 'datapedido', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for idcliente field
            //
            $editor = new DynamicCombobox('idcliente_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cliente`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcliente', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('email'),
                    new IntegerField('frequencia', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cliente', 'idcliente', 'idcliente_nome', 'edit_idcliente_nome_search', $editor, $this->dataset, $lookupDataset, 'idcliente', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for idfuncionario field
            //
            $editor = new DynamicCombobox('idfuncionario_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcionario`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idfuncionario', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('cargo', true),
                    new BooleanField('status', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), 'funcionario.status = 1
            '));
            $editColumn = new DynamicLookupEditColumn('Funcionário', 'idfuncionario', 'idfuncionario_nome', 'edit_idfuncionario_nome_search', $editor, $this->dataset, $lookupDataset, 'idfuncionario', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
    
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for datapedido field
            //
            $editor = new DateTimeEdit('datapedido_edit', false, 'd/m/Y');
            $editColumn = new CustomEditColumn('Data', 'datapedido', $editor, $this->dataset);
            $editColumn->SetInsertDefaultValue('%CURRENT_DATE%');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for idcliente field
            //
            $editor = new DynamicCombobox('idcliente_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cliente`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcliente', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('email'),
                    new IntegerField('frequencia', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cliente', 'idcliente', 'idcliente_nome', 'insert_idcliente_nome_search', $editor, $this->dataset, $lookupDataset, 'idcliente', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for idfuncionario field
            //
            $editor = new DynamicCombobox('idfuncionario_edit', $this->CreateLinkBuilder());
            $editor->setAllowClear(true);
            $editor->setMinimumInputLength(0);
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcionario`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idfuncionario', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('cargo', true),
                    new BooleanField('status', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), 'funcionario.status = 1
            '));
            $editColumn = new DynamicLookupEditColumn('Funcionário', 'idfuncionario', 'idfuncionario_nome', 'insert_idfuncionario_nome_search', $editor, $this->dataset, $lookupDataset, 'idfuncionario', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for datapedido field
            //
            $column = new DateTimeViewColumn('datapedido', 'datapedido', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d/m/Y');
            $grid->AddPrintColumn($column);
            
            //
            // View column for codigopedido field
            //
            $column = new TextViewColumn('codigopedido', 'codigopedido', 'Codigo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('idcliente', 'idcliente_nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('idfuncionario', 'idfuncionario_nome', 'Funcionário', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
    
        }
    
        private function AddCompareColumns(Grid $grid)
        {
    
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function CreateMasterDetailRecordGrid()
        {
            $result = new Grid($this, $this->dataset);
            
            $this->AddFieldColumns($result, false);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            
            $result->SetAllowDeleteSelected(false);
            $result->SetShowUpdateLink(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(false);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $this->setupGridColumnGroup($result);
            $this->attachGridEventHandlers($result);
            
            return $result;
        }
        
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        
        public function GetEnableModalGridInsert() { return true; }
        public function GetEnableModalGridEdit() { return true; }
        
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->setAllowSortingByDialog(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(false);
            $result->SetShowUpdateLink(false);
            $result->setAllowAddMultipleRecords(false);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && false);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(false);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(true);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(false);
            $this->setAllowPrintSelectedRecords(false);
            $this->setExportListAvailable(array('pdf', 'excel', 'word', 'xml', 'csv'));
            $this->setExportSelectedRecordsAvailable(array());
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array());
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            $detailPage = new pedidos_itensvendasPage('pedidos_itensvendas', $this, array('itemcodpedido'), array('codigopedido'), $this->GetForeignKeyFields(), $this->CreateMasterDetailRecordGrid(), $this->dataset, GetCurrentUserPermissionSetForDataSource('pedidos.itensvendas'), 'UTF-8');
            $detailPage->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource('pedidos.itensvendas'));
            $detailPage->SetTitle('itensvenda');
            $detailPage->SetMenuLabel('Detalhes do pedido');
            $detailPage->SetHeader(GetPagesHeader());
            $detailPage->SetFooter(GetPagesFooter());
            $detailPage->SetHttpHandlerName('pedidos_itensvendas_handler');
            $handler = new PageHTTPHandler('pedidos_itensvendas_handler', $detailPage);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cliente`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcliente', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('email'),
                    new IntegerField('frequencia', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_idcliente_nome_search', 'idcliente', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcionario`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idfuncionario', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('cargo', true),
                    new BooleanField('status', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), 'funcionario.status = 1
            '));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'insert_idfuncionario_nome_search', 'idfuncionario', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`cliente`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcliente', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('email'),
                    new IntegerField('frequencia', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_idcliente_nome_search', 'idcliente', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
            
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`funcionario`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idfuncionario', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('cargo', true),
                    new BooleanField('status', true)
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $lookupDataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), 'funcionario.status = 1
            '));
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_idfuncionario_nome_search', 'idfuncionario', 'nome', null, 20);
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
    
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
            //Setando o timezone
            date_default_timezone_set('America/Sao_Paulo');
            
            $rand = chr(rand(65,90));
            $rand .= chr(rand(65,90));
            $cod = date('ymdHid') . $rand; //ano mes e dia, hora minuto e segundos
            
            $rowData['codigopedido'] = $cod;
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
            $id = $rowData['idcliente'];
            
            
            
            //removendo do estoque
            $sql = "UPDATE cliente SET frequencia = frequencia+1 WHERE idcliente = {$id}";
            $this->GetConnection()->ExecSQL($sql);
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }

    SetUpUserAuthorization();

    try
    {
        $Page = new pedidosPage("pedidos", "pedidos.php", GetCurrentUserPermissionSetForDataSource("pedidos"), 'UTF-8');
        $Page->SetTitle('Pedidos');
        $Page->SetMenuLabel('Pedidos');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("pedidos"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
