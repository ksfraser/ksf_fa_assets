<?php
// Assets Module
$module_id = 'Assets'; $module_version = '1.0.0'; $module_name = 'Assets Management'; $module_description = 'Equipment/asset tracking with serial numbers and depreciation';
$module_tables = ['fa_asset_categories', 'fa_assets', 'fa_asset_depreciation', 'fa_asset_maintenance', 'fa_asset_transfers'];
$module_capabilities = ['SA_ASSETSVIEW'=>'View Assets','SA_ASSETSCREATE'=>'Create Assets','SA_ASSETSEDIT'=>'Edit Assets','SA_ASSETSMAINTENANCE'=>'Manage Maintenance'];

function assets_install():bool{return install_module_sql('Assets');}function assets_enable():bool{return enable_module('Assets');}function assets_disable():bool{return disable_module('Assets');}function assets_remove():bool{return remove_module_sql('Assets');}
add_module($module_name,$module_version,$module_description);