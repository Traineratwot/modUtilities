var ModUtilCategoryArr
Ext.onReady(function() {
	MODx.add({
		xtype: 'modUtil-panel-home'
	})
	ModUtilCategoryArr = new Ext.data.JsonStore({
		root: 'results'
		, autoLoad: true
		, totalProperty: 'total'
		, fields: ['id', 'name']
		, url: modUtilConnector_url
		, baseParams: {
			action: 'mgr/rest/getlistcategory',
			combo: 1,
		},
		listeners: {
			load: function() {
				Ext.getCmp('Rest-main-table').refresh()
				Ext.getCmp('Category-main-table').refresh()
				// Ext.getCmp('Log-main-table').refresh()
			}
		}
	})
	hljs.initHighlightingOnLoad()
})
var modUtil = function(config) {
	config = config || {}
	modUtil.superclass.constructor.call(this, config)
}
Ext.extend(modUtil, MODx.Component, { // Перечисляем группы, внутрь которых будем "складывать" объекты
	panel: {},
	page: {},
	window: {},
	grid: {},
	tree: {},
	combo: {},
	config: {},
	view: {},
	utils: {}
})
Ext.reg('modUtil', modUtil)
modUtil = new modUtil()

var defaultRenderer = function(val) {
	return val || _('ext_emptygroup')
}
var JSONRenderer = function(val) {
	if(val) {
		return cope.Highlighter.highlight(JSON.parse(val), {indent: 2, useTabs: true});
		return `<pre><code class="language-json">${val}</code></pre>`
	}
	return defaultRenderer(val)
}

//основной блок
modUtil.panel.Home = function(config) {
	config = config || {}
	Ext.apply(config, {
		cls: 'container', // Добавляем отступы
		items: [{
			html: ' <h2>modUtil REST<small style="font-size: 10px"><a href="https://forms.gle/FJbfBSutMJwQCgmS8" target="_blank">Bug report</a></small></h2>',
		},
			{
				xtype: 'modx-tabs',
				id:"main-modx-tabs",
				deferredRender: false,
				border: true,
				items: [
					{
						title: 'ERROR',
						id:'modx-tabs-Rest',
						items: [{
							html: '<H1>Error '+_('modutilities.warning')+'</H1>',
							cls: 'panel-desc',
						}
						]
					},
				]
			}
		]
	})
	modUtil.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}
Ext.extend(modUtil.panel.Home, MODx.Panel)
Ext.reg('modUtil-panel-home', modUtil.panel.Home)




