<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): Valmir Carlos Trindade/Translate to Brazilian Portuguese| 03/03/2012 |Curitiba/Paraná/Brasil.|www.ttcasolucoes.com.br
 ********************************************************************************/

if ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true')) {
	$toggle_historicos = 'See Non Converted Leads';
	$toggle_name = 'Converted Leads';
} else {
	$toggle_historicos = 'See Converted Leads';
	$toggle_name = 'Leads';
}

$mod_strings = Array(
'LBL_TGL_HISTORICOS' => $toggle_historicos,
'LBL_MODULE_NAME'=>$toggle_name,
'Leads' => $toggle_name,
'LBL_DIRECT_REPORTS_FORM_NAME'=>'Subordinado Direto',
'LBL_MODULE_TITLE'=>'Leads: Principal',
'LBL_SEARCH_FORM_TITLE'=>'Pesquisa Lead',
'LBL_LIST_FORM_TITLE'=>'Lista Lead',
'LBL_NEW_FORM_TITLE'=>'Novo Lead',
'LBL_LEAD_OPP_FORM_TITLE'=>'Oportunidade-Contato:',
'LBL_LEAD'=>'Lead:',
'LBL_ADDRESS_INFORMATION'=>'Dados do Endereço',
'LBL_CUSTOM_INFORMATION'=>'Informação Customizada',

'LBL_LIST_NAME'=>'Nome',
'LBL_LIST_LAST_NAME'=>'Sobrenome',
'LBL_LIST_COMPANY'=>'Empresa',
'LBL_LIST_WEBSITE'=>'Website',
'LBL_LIST_LEAD_NAME'=>'Nome Lead',
'LBL_LIST_EMAIL'=>'E-mail',
'LBL_LIST_PHONE'=>'Fone',
'LBL_LIST_LEAD_ROLE'=>'Função',

'LBL_NAME'=>'Nome:',
'LBL_LEAD_NAME'=>'Nome Lead:',
'LBL_LEAD_INFORMATION'=>'Informação Lead',
'LBL_FIRST_NAME'=>'Nome:',
'LBL_COMPANY'=>'Empresa:',
'LBL_DESIGNATION'=>'Função:', //Contribuição de Neimar Hahmeier
'LBL_PHONE'=>'Fone:',
'LBL_LAST_NAME'=>'Sobrenome:',
'LBL_MOBILE'=>'Celular:',
'LBL_EMAIL'=>'E-mail:',
'LBL_LEAD_SOURCE'=>'Fonte Lead:',
'LBL_LEAD_STATUS'=>'Status Lead:',
'LBL_WEBSITE'=>'Website:',
'LBL_FAX'=>'Fax:',
'LBL_INDUSTRY'=>'Atividade:',
'LBL_ANNUAL_REVENUE'=>'Receita Anual:',
'LBL_RATING'=>'Avaliação:',
'LBL_LICENSE_KEY'=>'Chave Licença:',
'LBL_NO_OF_EMPLOYEES'=>'No. Empregados:',
'LBL_YAHOO_ID'=>'ID Yahoo!:',

'LBL_ADDRESS_STREET'=>'Rua:',
'LBL_ADDRESS_POSTAL_CODE'=>'CEP:',
'LBL_ADDRESS_CITY'=>'Cidade:',
'LBL_ADDRESS_COUNTRY'=>'País:',
'LBL_ADDRESS_STATE'=> 'Estado:',
'LBL_ADDRESS'=>'Endereço:',
'LBL_DESCRIPTION_INFORMATION'=>'Descrição',
'LBL_DESCRIPTION'=>'Descrição:',

'LBL_CONVERT_LEAD'=>'Converter Lead:',
'LBL_CONVERT_LEAD_INFORMATION'=>'Converte Informação Lead',
'LBL_ACCOUNT_NAME'=>'Nome Organização',
'LBL_POTENTIAL_NAME'=>'Nome Oportunidade',
'LBL_POTENTIAL_CLOSE_DATE'=>'Data Oportunidade Fechada',
'LBL_POTENTIAL_AMOUNT'=>'Valor Oportunidade',
'LBL_POTENTIAL_SALES_STAGE'=>'Estágio Oportunidade de Vendas',

'NTC_DELETE_CONFIRMATION'=>'Você tem certeza que deseja deletar este registro?',
'NTC_REMOVE_CONFIRMATION'=>'Você tem certeza que deseja remover este Contato deste Caso?',
'NTC_REMOVE_DIRECT_REPORT_CONFIRMATION'=>'Você tem certeza que deseja remover este registro de um subordinado report?',
'NTC_REMOVE_OPP_CONFIRMATION'=>'Você tem certeza que deseja remover este Contato desta Oportunidade?',
'ERR_DELETE_RECORD'=>'Defina um número de registro para deletar o Contato.',

'LBL_COLON'=>' : ',
'LBL_IMPORT_LEADS'=>'Importar Leads',
'LBL_LEADS_FILE_LIST'=>'Lista de Arquivos de Leads',
'LBL_INSTRUCTIONS'=>'Instruções',
'LBL_KINDLY_PROVIDE_AN_XLS_FILE'=>'Forneça um único arquivo .xls como entrada',
'LBL_PROVIDE_ATLEAST_ONE_FILE'=>'Por favor forneça ao menos um arquivo como entrada',

'LBL_NONE'=>'Nada',
'LBL_ASSIGNED_TO'=>'Responsável:',
'LBL_SELECT_LEAD'=>'Seleciona Lead',
'LBL_GENERAL_INFORMATION'=>'Informação Geral',
'LBL_DO_NOT_CREATE_NEW_POTENTIAL'=>'Não criar Nova Oportunidade após Conversão',

'LBL_NEW_POTENTIAL'=>'Nova Oportunidade',
'LBL_POTENTIAL_TITLE'=>'Oportunidades',

'LBL_NEW_TASK'=>'Nova Tarefa',
'LBL_TASK_TITLE'=>'Tarefas',
'LBL_NEW_CALL'=>'Nova Chamada',
'LBL_CALL_TITLE'=>'Chamadas',
'LBL_NEW_MEETING'=>'Nova Reunião',
'LBL_MEETING_TITLE'=>'Reuniões',
'LBL_NEW_EMAIL'=>'Novo E-mail',
'LBL_EMAIL_TITLE'=>'E-mails',
'LBL_NEW_NOTE'=>'Novo Documento',
'LBL_NOTE_TITLE'=>'Documentos',
'LBL_NEW_ATTACHMENT'=>'Novo Anexo',
'LBL_ATTACHMENT_TITLE'=>'Anexos',

'LBL_ALL'=>'Todos',
'LBL_CONTACTED'=>'Contactado',
'LBL_LOST'=>'Perdido',
'LBL_HOT'=>'Quente',
'LBL_COLD'=>'Frio',

'LBL_TOOL_FORM_TITLE'=>'Ferramentas Lead',

'Salutation'=>'Saudação',
'First Name'=>'Nome',
'Phone'=>'Fone',
'Last Name'=>'Sobrenome',
'Mobile'=>'Celular',
'Company'=>'Empresa',
'Fax'=>'Fax',
'Email'=>'E-mail',
'Lead Source'=>'Fonte Lead',
'Website'=>'Website',
'Annual Revenue'=>'Receita Anual',
'Lead Status'=>'Status Lead',
'Industry'=>'Atividade',
'Rating'=>'Avaliação',
'No Of Employees'=>'No. Empregados',
'Assigned To'=>'Responsável',
'Yahoo Id'=>'ID Yahoo!',
'Created Time'=>'Data Criação',
'Modified Time'=>'Data Modificação',
'Street'=>'Rua',
'Postal Code'=>'CEP',
'City'=>'Cidade',
'Country'=>'País',
'State'=>'Estado',
'Description'=>'Descrição',
'Po Box'=>'Cx Postal',
'Campaign Source'=>'Origem Campanha',
//Added for CustomView 4.2 Release
'Name'=>'Nome',
'LBL_NEW_LEADS'=>'Meus Novos Leads',

//Added for Existing Picklist Entries

'--None--'=>'--Nada--',
'Mr.'=>'Sr.',
'Ms.'=>'Sra.',
'Mrs.'=>'Srta.',
'Dr.'=>'Dr',
'Prof.'=>'Prof.',

'Acquired'=>'Adquirido',
'Active'=>'Ativo',
'Market Failed'=>'Negócio Perdido',
'Project Cancelled'=>'Projeto Cancelado',
'Shutdown'=>'Fechado',

'Apparel'=>'Vestuário',
'Banking'=>'Bancos',
'Biotechnology'=>'Biotecnologia',
'Chemicals'=>'Química',
'Communications'=>'Comunicações',
'Construction'=>'Construção',
'Consulting'=>'Consultoria',
'Education'=>'Educação',
'Electronics'=>'Eletrônica',
'Energy'=>'Energia',
'Engineering'=>'Engenharia',
'Entertainment'=>'Entretenimento',
'Environmental'=>'Meio Ambiente',
'Finance'=>'Finanças',
'Food & Beverage'=>'Alimentos & Bebidas',
'Government'=>'Governo',
'Healthcare'=>'Saúde',
'Hospitality'=>'Hotelaria',
'Insurance'=>'Seguro',
'Machinery'=>'Máquinas',
'Manufacturing'=>'Indústria',
'Media'=>'Mídia',
'Not For Profit'=>'ONGs',
'Recreation'=>'Recreação',
'Retail'=>'Comércio',
'Shipping'=>'Transporte Marítimo',
'Technology'=>'Tecnologia',
'Telecommunications'=>'Telecomunicações',
'Transportation'=>'Transportes',
'Utilities'=>'Serviço Público',
'Other'=>'Outro',

'Cold Call'=>'Cold Call',
'Existing Customer'=>'Cliente Existente',
'Self Generated'=>'Auto Gerado',
'Employee'=>'Empregado',
'Partner'=>'Parceiro',
'Public Relations'=>'Relações Públicas',
'Direct Mail'=>'Mala Direta',
'Conference'=>'Conferência',
'Trade Show'=>'Feira Negócios',
'Web Site'=>'Web Site',
'Word of mouth'=>'Boca-boca',

'Attempted to Contact'=>'Tentativa Contato',
'Cold'=>'Frio',
'Contact in Future'=>'Contactar no Futuro',
'Contacted'=>'Contactado',
'Hot'=>'Quente',
'Junk Lead'=>'Descartado',
'Lost Lead'=>'Perdido',
'Not Contacted'=>'Não Contactado',
'Pre Qualified'=>'Pré-Qualificado',
'Qualified'=>'Qualificado',
'Warm'=>'Morno',

'Designation'=>'Título',

'Lead No'=>'Cod. Lead',

'LBL_TRANSFER_RELATED_RECORDS_TO' => 'Transferir registros relacionados para',

'LBL_FOLLOWING_ARE_POSSIBLE_REASONS' => 'O seguinte pode ser uma das possíveis razões',
'LBL_LEADS_FIELD_MAPPING_INCOMPLETE' => 'Todos os campos obrigatórios não são mapeados',
'LBL_MANDATORY_FIELDS_ARE_EMPTY' => 'Alguns dos valores dos campos obrigatórios estão vazios',
'LBL_LEADS_FIELD_MAPPING' => 'Mapeamento Campos Customizados do Lead',
'LBL_FIELD_SETTINGS' => 'Configurações do Campo',
'Leads ID' => 'ID Leads',

//Missing label in vtiger CRM
'Secondary Email'=>'Email Alternativo', 
'LeadAlreadyConverted' => 'Lead cannot be converted. Either it has already been converted or you lack permission on one or more of the destination modules.',
'Is Converted From Lead' => 'Convertido a partir do Lead',
'Converted From Lead' => 'Convertido do Lead',
);

?>
