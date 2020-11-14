Ext.onReady(function() {
	Ext.util.Cookies.set('search_log','')
	MODx.add({
		xtype: 'modUtil-panel-home'
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
var escapeHTML = function (string) {
	var htmlEscapes = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#x27;',
		'/': '&#x2F;'
	};
	var htmlEscaper = /[&<>"'\/]/g;
	return ('' + string).replace(htmlEscaper, function(match) {
		return htmlEscapes[match];
	});
}
var defaultRenderer = function(val) {
	return val || _('ext_emptygroup')
}
var JSONRenderer = function(val) {
	if(val) {
		return cope.Highlighter.highlight(JSON.parse(val), {indent: 2, useTabs: true})
		return `<pre><code class="language-json">${val}</code></pre>`
	}
	return defaultRenderer(val)
}
var HTMLRenderer = function(val) {
	if(val) {
		val = escapeHTML(val);
		return `<pre><code class="language-html">${val}</code></pre>`
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
				id: 'main-modx-tabs',
				deferredRender: false,
				border: true,
				items: [
					{
						title: 'Rest',
						id: 'modx-tabs-Rest',
						items: [{
							html: 'Methods',
							cls: 'panel-desc',
						}, {
							id: 'Rest-main-table',
							xtype: 'modUtil-grid',
							columns: [ // Добавляем ширину и заголовок столбца
								{dataIndex: 'id', width: 330, header: 'id', sortable: true, renderer: defaultRenderer},
								{
									dataIndex: 'url',
									width: 670,
									header: 'url',
									sortable: true,
									editor: {xtype: 'textfield'},
									renderer: function(val) {
										if(val) {
											return `<a href="/${val}" target="_blank">${val}</a>`
										} else {
											return defaultRenderer(val)
										}
									}
								},
								{
									dataIndex: 'snippet',
									width: 670,
									header: _('snippet'),
									sortable: true,
									editor: {
										xtype: 'modUtil-combo-modCombo',
										fieldLabel: _('snippet'),
										forceSelection: false,
										fields: ['id', 'name','category_name'],
										url: MODx.config.connector_url,
										baseParams: {
											action: 'element/snippet/getlist',
											sort:'id',
											dir:'DESK',
											combo: 1,
										},
										valueField: 'name',
										displayField: 'name',
										tpl: new Ext.XTemplate(
											'<tpl for=".">\
												<div class="x-combo-list-item">\
													<tpl if="id">({id})</tpl>\
													<strong>{name}</strong><small>({category_name})</small>\
												</div>\
											</tpl>'
											, {compiled: true}
										)
									},
									renderer: defaultRenderer
								},
								{
									dataIndex: 'parameters',
									width: 670,
									editor: {xtype: 'textarea'},
									header: _('parameters'),
									renderer: JSONRenderer,
									editor: {xtype: 'textarea'}
								},
								{
									dataIndex: 'permission',
									width: 670,
									header: _('permissions'),
									renderer: JSONRenderer
									,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'allowMethod',
									width: 670,
									sortable: true,
									header: _('modutilities.allowMethod'),
									renderer: defaultRenderer,
									editor: {xtype: 'textfield'}
								},
								{
									dataIndex: 'BASIC_auth',
									width: 670,
									header: _('modutilities.BASIC_auth'),
									sortable: true,
									editor: {xtype: 'combo-boolean'},
									renderer: function(val) {
										if(val == 1) {
											val = '<span class="true">' + _('yes') + '</span>'
										} else {
											val = '<span class="false">' + _('no') + '</span>'
										}
										return val
									}
								},
								{
									dataIndex: 'category', width: 670, header: _('category'),
									editor: {
										xtype: 'modUtil-combo-modCombo',
										name: 'category',
										hiddenName: 'category',
										fieldLabel: _('category'),
										forceSelection: false,
										fields: ['id','name','allowMethod'],
										baseParams: {
											action: 'mgr/rest/getlistcategory',
											sort:'name',
											dir:'DESK',
											combo: 1,
										},
										valueField: 'name',
										displayField: 'name',
										tpl: new Ext.XTemplate(
											'<tpl for=".">\
												<div class="x-combo-list-item">\
													<tpl if="id">({id})</tpl>\
													<strong>{name}</strong><small>({allowMethod})</small>\
												</div>\
											</tpl>'
											, {compiled: true}
										)
									},
									renderer: defaultRenderer
								}
							],
							fields: [
								'id',
								'permission',
								'url',
								'snippet',
								'param',
								'allowMethod',
								'BASIC_auth',
								'category',
								'catName',
							],
							tbar: [{
								xtype: 'button', // Перемещаем сюда нашу кнопку
								text: _('create_new') + ' Rest',
								cls: 'primary-button',
								handler: function() {
									MODx.load({
										xtype: 'modUtil-window-addRest',
									}).show()
								},
							}],
							action: 'mgr/rest/getrest',
							save_action: 'mgr/rest/updaterest',
							autosave: true,
							getMenu: function(grid, rowIndex) {
								var m = []
								m.push({
									text: _('delete'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.del
								})
								return m
							},
							del: function() {
								var cs = this.getSelectedAsList()
								var self = this
								MODx.msg.confirm({
									title: _('delete'),
									text: _('confirm_remove'),
									url: modUtilConnector_url,
									params: {
										action: 'mgr/rest/delrest',
										id: cs,
									},
									listeners: {
										'success': {
											fn: function(r) {
												if(!r.success) {
													MODx.msg.status({
														title: _('undeleted'),
														message: 'Ошибка',
														delay: 3
													})
												} else {
													MODx.msg.status({
														title: _('delete'),
														message: 'Готово',
														delay: 3
													})
												}
												self.refresh()
											}, scope: true
										}
									}
								})
							}
						}
						]
					},
					{
						title: 'Category',
						id: 'modx-tabs-Category',
						items: [{
							html: 'Categories',
							cls: 'panel-desc',
						}, {
							id: 'Category-main-table',
							xtype: 'modUtil-grid',
							columns: [ // Добавляем ширину и заголовок столбца
								{
									dataIndex: 'id',
									width: 330,
									sortable: true,
									header: 'id',
									renderer: defaultRenderer,
								},
								{
									dataIndex: 'name',
									width: 330,
									sortable: true,
									header: _('name'),
									renderer: defaultRenderer,
									editor: {xtype: 'textfield'}
								},
								{
									dataIndex: 'permission',
									width: 670,
									header: _('permissions'),
									renderer: JSONRenderer,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'param', width: 670, header: _('parameters'),
									renderer: JSONRenderer,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'allowMethod',
									sortable: true,
									width: 670,
									header: _('modutilities.allowMethod'),
									editor: {xtype: 'textfield'},
									renderer: defaultRenderer
								},
								{
									dataIndex: 'BASIC_auth',
									sortable: true,
									width: 670,
									header: _('modutilities.BASIC_auth'),
									renderer: function(val) {
										if(val === 1) {
											val = '<span class="true">' + _('yes') + '</span>'
										} else {
											val = '<span class="false">' + _('no') + '</span>'
										}
										return val
									},
									editor: {xtype: 'combo-boolean'},
								},
							],
							fields: [
								'id',
								'name',
								'permission',
								'param',
								'allowMethod',
								'BASIC_auth',
							],
							tbar: [{
								xtype: 'button', // Перемещаем сюда нашу кнопку
								text: _('category_create'),
								cls: 'primary-button',
								handler: function() {
									MODx.load({
										xtype: 'modUtil-window-addCat',
									}).show()
								},
							}],
							action: 'mgr/rest/getlistcategory',
							save_action: 'mgr/rest/updaterestcategory',
							autosave: true,
							getMenu: function(grid, rowIndex) {
								var m = []
								m.push({
									text: _('delete'),
									grid: grid,
									rowIndex: rowIndex,
									handler: this.del
								})
								return m
							},
							del: function() {
								var cs = this.getSelectedAsList()
								var self = this
								MODx.msg.confirm({
									title: _('delete'),
									text: _('confirm_remove'),
									url: modUtilConnector_url,
									params: {
										action: 'mgr/rest/delrestcategory',
										id: cs,
									},
									listeners: {
										'success': {
											fn: function(r) {
												if(!r.success) {
													MODx.msg.status({
														title: _('undeleted'),
														message: 'Ошибка',
														delay: 3
													})
												} else {
													MODx.msg.status({
														title: _('delete'),
														message: 'Готово',
														delay: 3
													})
												}
												self.refresh()
											}, scope: true
										}
									}
								})
							}
						}
						]
					},
					{
						title: 'log',
						id: 'modx-tabs-log',
						items: [{ // Внутри таба ещё один HTML-блок с классом panel-desc
							html: 'connection log',
							cls: 'panel-desc',
						}, {
							id: 'log-main-table',
							xtype: 'modUtil-grid',
							columns: [ // Добавляем ширину и заголовок столбца
								{
									dataIndex: 'id',
									width: 330,
									sortable: true,
									header: 'id',
									renderer: defaultRenderer,
									editor: {xtype: 'textarea'},

								},
								{
									dataIndex: 'rest_id',
									width: 330,
									sortable: true,
									header: _('modutilities.rest_id'),
									renderer: defaultRenderer,
									editor: {xtype: 'textfield'}
								},
								{
									dataIndex: 'input',
									width: 670,
									header: _('modutilities.input'),
									renderer: JSONRenderer,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'output', width: 670, header: _('modutilities.output'),
									renderer: HTMLRenderer,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'user',
									sortable: true,
									width: 670,
									header: _('modutilities.user'),
									renderer: JSONRenderer,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'time', sortable: true, width: 670, header: _('modutilities.time'),
									renderer: defaultRenderer,
									editor: {xtype: 'textarea'},
								},
								{
									dataIndex: 'datetime',
									sortable: true,
									width: 670,
									header: _('modutilities.datetime'),
									renderer: defaultRenderer,
									editor: {xtype: 'textarea'},
								},
							],
							fields: [
								'id',
								'rest_id',
								'input',
								'output',
								'user',
								'time',
								'datetime',
							],
							tbar: [
								{
									emptyText:_('search'),
									id: 'search-field-log',
									xtype: 'textfield', // Перемещаем сюда нашу кнопку
								},
								{
									id: 'search-button-log',
									xtype: 'button', // Перемещаем сюда нашу кнопку
									text: '🔎',
									cls: 'primary-button',
									handler: function() {
										var v =Ext.getCmp('search-field-log').getValue()
										if(v && v != _('search')) {
											Ext.util.Cookies.set('search_log',v)
										}else{
											Ext.util.Cookies.set('search_log','')
											Ext.util.Cookies.clear('search_log')
										}
										Ext.getCmp('log-main-table').refresh();
									},
								}],
							action: 'mgr/rest/getlistlog',
						}]
					}
				]
			}
		]
	})
	modUtil.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}

Ext.extend(modUtil.panel.Home, MODx.Panel)
Ext.reg('modUtil-panel-home', modUtil.panel.Home)

modUtil.grid.ModGrid = function(config) { // Придумываем название, например, «Names»
	config = config || {}
	Ext.apply(config, {
		// Сюда перемещаем все свойства нашей таблички
		paging: true,
		autoHeight: true,
		viewConfig: {
			forceFit: true,
			scrollOffset: 0
		},
		remoteSort: true,
		url: modUtilConnector_url,
		keyField: 'id',
		getSelectedAsList: function() {
			var selects = this.getSelectionModel().getSelections()
			if(selects.length <= 0) return false
			var cs = ''
			for(var i = 0; i < selects.length; i++) {
				cs += ',' + selects[i].data[this.keyField]
			}
			cs = cs.substr(1)
			return cs
		}
	})
	modUtil.grid.ModGrid.superclass.constructor.call(this, config) // Магия
}
Ext.extend(modUtil.grid.ModGrid, MODx.grid.Grid) // Наша табличка расширяет GridPanel
Ext.reg('modUtil-grid', modUtil.grid.ModGrid) // Регистрируем новый xtype

modUtil.window.modWindow = function(config) {
	config = config || {}
	Ext.applyIf(config, {
		title: 'Create thing',
		width: window.innerWidth / 100 * 50,
		saveBtnText: 'Сохранить 💾',
		url: modUtilConnector_url,

	})
	modUtil.window.modWindow.superclass.constructor.call(this, config) // Магия
}
Ext.extend(modUtil.window.modWindow, MODx.Window) // Расширяем MODX.Window
Ext.reg('modUtil-window-modWindow', modUtil.window.modWindow) // Регистрируем новый xtype
//добавление rest

modUtil.window.addRest = function(config) {
	config = config || {}
	this.ident = config.ident || 'mecnewsletter' + Ext.id()
	Ext.applyIf(config, {
		title: _('create_new') + ' Rest',
		fields: [
			{
				xtype: 'textfield',
				allowBlank: false,
				fieldLabel: _('service_url'),
				name: 'url',
				id: 'addRest-' + this.ident + '-url',
				anchor: '99%',
				value: null
			},
			{
				xtype: 'modUtil-combo-modCombo',
				fieldLabel: _('snippet'),
				name: 'snippet',
				id: 'addRest-' + this.ident + '-snippet',
				anchor: '99%',
				value: null,
				forceSelection: false,
				fields: ['id', 'name'],
				url: MODx.config.connector_url,
				baseParams: {
					action: 'element/snippet/getlist',
					sort:'id',
					dir:'DESK',
					combo: 1,
				},
				allowBlank: false,
				hiddenName: 'snippet',
				valueField: 'name',
				displayField: 'name',
				tpl: new Ext.XTemplate(
					'<tpl for=".">\
						<div class="x-combo-list-item">\
							<tpl if="id">({id})</tpl>\
							<strong>{name}</strong><small>({category_name})</small>\
						</div>\
					</tpl>'
					, {compiled: true}
				)
			},
			{
				xtype: 'textarea',
				fieldLabel: _('parameters'),
				name: 'param',
				id: 'addRest-' + this.ident + '-param',
				anchor: '99%',
				value: null
			},
			{
				xtype: 'modUtil-combo-modComboSuper',
				fieldLabel: 'разрешенные методы',
				name: 'allowMethod',
				id: 'addRest-' + this.ident + '-allowMethod',
				anchor: '99%',
				value: null,
				fields: ['name'],
				valueField: 'name',
				displayField: 'name',
				hiddenName: 'allowMethod',
				multiple: true,
				action: 'mgr/rest/getlistallowmethod',
			},
			{
				xtype: 'textarea',
				fieldLabel: _('permissions'),
				name: 'permission',
				id: 'addRest-' + this.ident + '-permission',
				anchor: '99%',
			},
			{
				xtype: 'xcheckbox',
				fieldLabel: 'Basic auth',
				boxLabel: _('yes'),
				name: 'BASIC_auth',
				id: 'addRest-' + this.ident + '-BASIC_auth',
				anchor: '99%',
				value: false
			},
			{
				xtype: 'modUtil-combo-modCombo',
				fieldLabel: _('category'),
				name: 'category',
				id: 'addRest-' + this.ident + '-category',
				fields: ['id', 'name', 'allowMethod'],
				defaultValue: '1',
				allowBlank: false,
				baseParams: {
					action: 'mgr/rest/getlistcategory',
					combo: 1,
					sort:'id',
					dir:'DESK',
				},
				hiddenName: 'category',
				valueField: 'id',
				displayField: 'name',
				autoSelect: true,
				tpl: new Ext.XTemplate(
					'<tpl for=".">\
						<div class="x-combo-list-item">\
							<tpl if="id">({id})</tpl>\
							<strong>{name}</strong><small>({allowMethod})</small>\
						</div>\
					</tpl>'
					, {compiled: true}
				)
			}
		],
		action: 'mgr/rest/create_utilrest',
		listeners: {
			beforeSubmit: function(a) {
				if(typeof a.allowMethod !== 'string' && a.allowMethod) {
					var allowMethod = a.allowMethod.join()
					if(typeof allowMethod == 'string') {
						$(`input[name="allowMethod"]`).each(function() {
							$(this).val(allowMethod)
							$(this).attr('value', allowMethod)
							this.value = allowMethod
						})
					}
				}
				return true
			},
			success: function() {
				MODx.msg.status({
					title: _('created'),
					message: 'Готово',
					delay: 3
				})
				Ext.getCmp('Rest-main-table').refresh()
				this.remove()
			},
			failure: function() {
				this.remove()
			}
		}
	})
	modUtil.window.addRest.superclass.constructor.call(this, config) // Магия
}
Ext.extend(modUtil.window.addRest, modUtil.window.modWindow) // Расширяем MODX.Window
Ext.reg('modUtil-window-addRest', modUtil.window.addRest) // Регистрируем новый xtype
//добавление категории

modUtil.window.addCat = function(config) {
	config = config || {}
	this.ident = config.ident || 'mecnewsletter' + Ext.id()
	Ext.applyIf(config, {
		title: _('create_new') + ' Rest',
		fields: [
			{
				xtype: 'textfield',
				fieldLabel: _('name'),
				name: 'name',
				id: 'addRest-' + this.ident + '-url',
				anchor: '99%',
				value: null
			},
			{
				xtype: 'textarea',
				fieldLabel: _('parameters'),
				name: 'param',
				id: 'addRest-' + this.ident + '-param',
				anchor: '99%',
				value: null
			},
			{
				xtype: 'modUtil-combo-modComboSuper',
				fieldLabel: 'разрешенные методы',
				name: 'allowMethod',
				id: 'addCat-' + this.ident + '-allowMethod',
				anchor: '99%',
				value: null,
				fields: ['name'],
				valueField: 'name',
				displayField: 'name',
				hiddenName: 'allowMethod',
				multiple: true,
				action: 'mgr/rest/getlistallowmethod',
			},
			{
				xtype: 'textarea',
				fieldLabel: _('permissions'),
				name: 'permission',
				id: 'addRest-' + this.ident + '-permission',
				anchor: '99%',
			},
			{
				xtype: 'xcheckbox',
				fieldLabel: 'Basic auth',
				boxLabel: _('yes'),
				name: 'BASIC_auth',
				id: 'addRest-' + this.ident + '-BASIC_auth',
				anchor: '99%',
				value: false
			},
		],
		action: 'mgr/rest/create_utilrestcategory',
		listeners: {
			beforeSubmit: function(a) {
				if(typeof a.allowMethod !== 'string' && a.allowMethod) {
					var allowMethod = a.allowMethod.join()
					if(typeof allowMethod == 'string') {
						$(`input[name="allowMethod"]`).each(function() {
							$(this).val(allowMethod)
							$(this).attr('value', allowMethod)
							this.value = allowMethod
						})
					}
				}
				return true
			},
			success: function() {
				MODx.msg.status({
					title: _('created'),
					message: 'Готово',
					delay: 3
				})
				Ext.getCmp('Category-main-table').refresh()
				this.remove()
			},
			failure: function() {
				this.remove()
			}
		}
	})
	modUtil.window.addRest.superclass.constructor.call(this, config) // Магия
}
Ext.extend(modUtil.window.addCat, modUtil.window.modWindow) // Расширяем MODX.Window
Ext.reg('modUtil-window-addCat', modUtil.window.addCat) // Регистрируем новый xtype

modUtil.combo.modCombo = function(config) {
	config = config || {}
	this.ident = config.ident || 'mecnewsletter' + Ext.id()
	Ext.applyIf(config, {
		url: modUtilConnector_url,
		valueField: 'id',
		width: '100%',
		anchor: '99%',
		editable: true,
		pageSize: 20,
		preventRender: true,
		forceSelection: true,
		enableKeyEvents: true,
	})
	modUtil.combo.modCombo.superclass.constructor.call(this, config) // Магия
}
Ext.extend(modUtil.combo.modCombo, MODx.combo.ComboBox) // Расширяем MODX.ComboBox
Ext.reg('modUtil-combo-modCombo', modUtil.combo.modCombo) // Регистрируем новый xtype

modUtil.combo.modComboSuper = function(config) {
	config = config || {}
	Ext.applyIf(config, {
		xtype: 'superboxselect'
		, allowBlank: true
		, msgTarget: 'under'
		, allowAddNewData: true
		, addNewDataOnBlur: true
		, resizable: true
		, name: config.name || 'tags'
		, anchor: '99%'
		, minChars: 2
		, store: new Ext.data.JsonStore({
			id: (config.name || 'tags') + '-store'
			, root: 'results'
			, autoLoad: true
			, autoSave: false
			, totalProperty: 'total'
			, fields: config.fields
			, url: modUtilConnector_url
			, baseParams: {
				action: config.action,
				combo: 1,
				key: config.name
			}
		})
		, mode: 'remote'
		, displayField: 'value'
		, valueField: 'value'
		, triggerAction: 'all'
		, extraItemCls: 'x-tag'
		, expandBtnCls: 'x-form-trigger'
		, clearBtnCls: 'x-form-trigger'
		, listeners: {
			newitem: function(bs, v, f) {bs.addItem({tag: v})}
			/*,select: {fn:MODx.fireResourceFormChange, scope:this}
			,beforeadditem: {fn:MODx.fireResourceFormChange, scope:this}
			,beforeremoveitem: {fn:MODx.fireResourceFormChange, scope:this}
			,clear: {fn:MODx.fireResourceFormChange, scope:this}*/
		}
		, renderTo: Ext.getBody()
	})
	config.name += '[]'
	modUtil.combo.modComboSuper.superclass.constructor.call(this, config)
}
Ext.extend(modUtil.combo.modComboSuper, Ext.ux.form.SuperBoxSelect)
Ext.reg('modUtil-combo-modComboSuper', modUtil.combo.modComboSuper)