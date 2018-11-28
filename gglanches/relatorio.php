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
    
    
    
    class relatorioPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $selectQuery = 'SELECT *, Sum(itemvalor * itemqtd) AS pedtotal,
            date_format(datapedido,"%d/%m/%Y") as data
            FROM itens, pedidos WHERE itemcodpedido = codigopedido
            GROUP BY codigopedido';
            $insertQuery = array();
            $updateQuery = array();
            $deleteQuery = array();
            $this->dataset = new QueryDataset(
              MyPDOConnectionFactory::getInstance(), 
              GetConnectionOptions(),
              $selectQuery, $insertQuery, $updateQuery, $deleteQuery, 'relatorio');
            $this->dataset->addFields(
                array(
                    new IntegerField('iditem', true, true, true),
                    new IntegerField('itemproduto', true),
                    new IntegerField('itemvalor', true),
                    new IntegerField('itemqtd', true),
                    new StringField('itemcodpedido'),
                    new IntegerField('idpedido', true, true, true),
                    new StringField('codigopedido'),
                    new DateField('datapedido'),
                    new IntegerField('idcliente'),
                    new IntegerField('idfuncionario', true),
                    new IntegerField('pedtotal'),
                    new StringField('data')
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
            $chart = new Chart('Chart01', Chart::TYPE_AREA, $this->dataset);
            $chart->setTitle('Comparativo');
            $chart->setHeight(500);
            $chart->setDomainColumn('iditem', 'iditem', 'int');
            $chart->addDataColumn('pedtotal', 'Total', 'float')
                  ->setTooltipColumn('data')
                  ->setAnnotationColumn('pedtotal')
                  ->setAnnotationTextColumn('data');
            $this->addChart($chart, 0, ChartPosition::BEFORE_GRID, 12);
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'iditem', 'iditem', 'Iditem'),
                new FilterColumn($this->dataset, 'itemproduto', 'itemproduto', 'Itemproduto'),
                new FilterColumn($this->dataset, 'itemvalor', 'itemvalor', 'Itemvalor'),
                new FilterColumn($this->dataset, 'itemqtd', 'itemqtd', 'Itemqtd'),
                new FilterColumn($this->dataset, 'itemcodpedido', 'itemcodpedido', 'Itemcodpedido'),
                new FilterColumn($this->dataset, 'idpedido', 'idpedido', 'Idpedido'),
                new FilterColumn($this->dataset, 'codigopedido', 'codigopedido', 'Codigo'),
                new FilterColumn($this->dataset, 'datapedido', 'datapedido', 'Data'),
                new FilterColumn($this->dataset, 'idcliente', 'idcliente_nome', 'Cliente'),
                new FilterColumn($this->dataset, 'idfuncionario', 'idfuncionario_nome', 'Funcionário'),
                new FilterColumn($this->dataset, 'pedtotal', 'pedtotal', 'Total'),
                new FilterColumn($this->dataset, 'data', 'data', 'Data')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['codigopedido'])
                ->addColumn($columns['datapedido'])
                ->addColumn($columns['idcliente'])
                ->addColumn($columns['idfuncionario'])
                ->addColumn($columns['pedtotal'])
                ->addColumn($columns['data']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
            $columnFilter
                ->setOptionsFor('datapedido')
                ->setOptionsFor('idcliente')
                ->setOptionsFor('idfuncionario');
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
    
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
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
            
            //
            // View column for pedtotal field
            //
            $column = new NumberViewColumn('pedtotal', 'pedtotal', 'Total', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator('.');
            $column->setDecimalSeparator(',');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for data field
            //
            $column = new TextViewColumn('data', 'data', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for data field
            //
            $column = new TextViewColumn('data', 'data', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for codigopedido field
            //
            $editor = new TextEdit('codigopedido_edit');
            $editColumn = new CustomEditColumn('Codigo', 'codigopedido', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
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
                    new StringField('email')
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
                    new IntegerField('status')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Funcionário', 'idfuncionario', 'idfuncionario_nome', 'edit_idfuncionario_nome_search', $editor, $this->dataset, $lookupDataset, 'idfuncionario', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for pedtotal field
            //
            $editor = new TextEdit('pedtotal_edit');
            $editColumn = new CustomEditColumn('Total', 'pedtotal', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for data field
            //
            $editor = new TextEdit('data_edit');
            $editColumn = new CustomEditColumn('Data', 'data', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for codigopedido field
            //
            $editor = new TextEdit('codigopedido_edit');
            $editColumn = new CustomEditColumn('Codigo', 'codigopedido', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for datapedido field
            //
            $editor = new DateTimeEdit('datapedido_edit', false, 'd/m/Y');
            $editColumn = new CustomEditColumn('Data', 'datapedido', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
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
                    new StringField('email')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Cliente', 'idcliente', 'idcliente_nome', 'multi_edit_idcliente_nome_search', $editor, $this->dataset, $lookupDataset, 'idcliente', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
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
                    new IntegerField('status')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Funcionário', 'idfuncionario', 'idfuncionario_nome', 'multi_edit_idfuncionario_nome_search', $editor, $this->dataset, $lookupDataset, 'idfuncionario', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for pedtotal field
            //
            $editor = new TextEdit('pedtotal_edit');
            $editColumn = new CustomEditColumn('Total', 'pedtotal', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for data field
            //
            $editor = new TextEdit('data_edit');
            $editColumn = new CustomEditColumn('Data', 'data', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for codigopedido field
            //
            $editor = new TextEdit('codigopedido_edit');
            $editColumn = new CustomEditColumn('Codigo', 'codigopedido', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for datapedido field
            //
            $editor = new DateTimeEdit('datapedido_edit', false, 'd/m/Y');
            $editColumn = new CustomEditColumn('Data', 'datapedido', $editor, $this->dataset);
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
                    new StringField('email')
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
                    new IntegerField('status')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $editColumn = new DynamicLookupEditColumn('Funcionário', 'idfuncionario', 'idfuncionario_nome', 'insert_idfuncionario_nome_search', $editor, $this->dataset, $lookupDataset, 'idfuncionario', 'nome', '');
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for pedtotal field
            //
            $editor = new TextEdit('pedtotal_edit');
            $editColumn = new CustomEditColumn('Total', 'pedtotal', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for data field
            //
            $editor = new TextEdit('data_edit');
            $editColumn = new CustomEditColumn('Data', 'data', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $editColumn->GetCaption()));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(false && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for codigopedido field
            //
            $column = new TextViewColumn('codigopedido', 'codigopedido', 'Codigo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for datapedido field
            //
            $column = new DateTimeViewColumn('datapedido', 'datapedido', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d/m/Y');
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
            
            //
            // View column for pedtotal field
            //
            $column = new NumberViewColumn('pedtotal', 'pedtotal', 'Total', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator('.');
            $column->setDecimalSeparator(',');
            $grid->AddPrintColumn($column);
            
            //
            // View column for data field
            //
            $column = new TextViewColumn('data', 'data', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for codigopedido field
            //
            $column = new TextViewColumn('codigopedido', 'codigopedido', 'Codigo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for datapedido field
            //
            $column = new DateTimeViewColumn('datapedido', 'datapedido', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDateTimeFormat('d/m/Y');
            $grid->AddExportColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('idcliente', 'idcliente_nome', 'Cliente', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for nome field
            //
            $column = new TextViewColumn('idfuncionario', 'idfuncionario_nome', 'Funcionário', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for pedtotal field
            //
            $column = new NumberViewColumn('pedtotal', 'pedtotal', 'Total', $this->dataset);
            $column->SetOrderable(true);
            $column->setNumberAfterDecimal(2);
            $column->setThousandsSeparator('.');
            $column->setDecimalSeparator(',');
            $grid->AddExportColumn($column);
            
            //
            // View column for data field
            //
            $column = new TextViewColumn('data', 'data', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for data field
            //
            $column = new TextViewColumn('data', 'data', 'Data', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
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
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && false);
            $result->setTableBordered(true);
            $result->setTableCondensed(false);
            $result->SetTotal('pedtotal', PredefinedAggregate::$Sum);
            $result->setReloadPageAfterAjaxOperation(true);
            
            $result->SetHighlightRowAtHover(true);
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
            $this->setAllowPrintSelectedRecords(true);
            $this->setExportListAvailable(array('pdf', 'excel'));
            $this->setExportSelectedRecordsAvailable(array('pdf', 'excel'));
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
                '`cliente`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('idcliente', true, true, true),
                    new StringField('nome', true),
                    new StringField('cpf', true),
                    new StringField('email')
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
                    new IntegerField('status')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
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
                    new StringField('email')
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
                    new IntegerField('status')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'edit_idfuncionario_nome_search', 'idfuncionario', 'nome', null, 20);
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
                    new StringField('email')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_idcliente_nome_search', 'idcliente', 'nome', null, 20);
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
                    new IntegerField('status')
                )
            );
            $lookupDataset->setOrderByField('nome', 'ASC');
            $handler = new DynamicSearchHandler($lookupDataset, $this, 'multi_edit_idfuncionario_nome_search', 'idfuncionario', 'nome', null, 20);
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
            if($columnName == 'pedtotal')
            {
            $customText = '<span style="font-size:24px; color: red">Total do período R$ ' . number_format($totalValue,'2',',','.') . '</span>';
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
    
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
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
        $Page = new relatorioPage("relatorio", "relatorio.php", GetCurrentUserPermissionSetForDataSource("relatorio"), 'UTF-8');
        $Page->SetTitle('Relatorio');
        $Page->SetMenuLabel('Relatório');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("relatorio"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
