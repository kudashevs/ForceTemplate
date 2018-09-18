<?php
/**
 * MODx Revolution plugin which force template set for given parents
 *
 * @package forcetemplate
 *
 * @var modX $modx MODX instance
 *
 */
if ($modx->event->name === 'OnManagerPageBeforeRender' && $modx->getoption('forcetemplate.quick_create')) {
    $modx->regClientStartupScript('<script>Ext.override(MODx.tree.Resource, {
    quickCreate:function(itm,e,cls,ctx,p) {
        cls = cls || \'modDocument\';
        var r = {
            class_key: cls
            ,context_key: ctx || \'web\'
            ,\'parent\': p || 0
            ,\'template\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'default_template\',MODx.config.default_template))
            ,\'richtext\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'richtext_default\',MODx.config.richtext_default))
            ,\'hidemenu\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'hidemenu_default\',MODx.config.hidemenu_default))
            ,\'searchable\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'search_default\',MODx.config.search_default))
            ,\'cacheable\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'cache_default\',MODx.config.cache_default))
            ,\'published\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'publish_default\',MODx.config.publish_default))
            ,\'content_type\': parseInt(this.getContextSettingForNode(this.cm.activeNode,ctx,\'default_content_type\',MODx.config.default_content_type))
        };

        if (this.cm.activeNode.attributes.type != \'modContext\') {
            var t = this.cm.activeNode.getOwnerTree();
            var rn = t.getRootNode();
            var cn = rn.findChild(\'ctx\',ctx,false);
            if (cn) {
                r[\'template\'] = cn.attributes.settings.default_template;
            }
        } else {
            r[\'template\'] = this.cm.activeNode.attributes.settings.default_template;
        }

        MODx.Ajax.request({
            url: \'/assets/components/forcetemplate/connector.php\',
            params: {action: \'mgr/get\', data: JSON.stringify(r)},
            listeners: {
                success: {
                    fn: function ( response ) {
                        if (response.count > 0) {
                            r[\'template\'] = response.object.template;
                        }
                        var w = MODx.load({
                            xtype: \'modx-window-quick-create-modResource\'
                            ,record: r
                            ,listeners: {
                                \'success\':{
                                    fn: function() {
                                        this.refreshNode(this.cm.activeNode.id, this.cm.activeNode.childNodes.length > 0);
                                    }
                                    ,scope: this}
                                ,\'hide\':{fn:function() {this.destroy();}}
                                ,\'show\':{fn:function() {this.center();}}
                            }
                        });
                        w.setValues(r);
                        w.show(e.target,function() {
                            Ext.isSafari ? w.setPosition(null,30) : w.center();
                        },this);
                    }, scope: this
                }
            }
        });
    }
});</script>');
}

if ($modx->event->name === 'OnDocFormRender') {

    $startId = $resource->get('parent');
    $rules = $modx->getOption('forcetemplate.rules');

    if ($startId < 1 || empty($rules) || empty($scriptProperties['mode']) || $scriptProperties['mode'] !== 'new') { return ''; }

    require_once MODX_CORE_PATH . 'components/forcetemplate/forcetemplate.class.php';

    $forceTemplate = new ForceTemplate($modx, $startId, $rules);
    $templateId = $forceTemplate->calculateParentTemplateId();

    if (empty($templateId)) { return ''; }

    $modx->controller->setProperty('template', $templateId);
    return '';
}