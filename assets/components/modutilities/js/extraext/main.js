Ext.onReady(function() {
	Ext.util.Cookies.set('search_log', '')
	MODx.add({
		xtype: 'modUtil-panel-home'
	})
})
var modUtil = function(config) {
	config = config || {}
	modUtil.superclass.constructor.call(this, config)
}
Ext.extend(modUtil, MODx.Component, { // Перечисляем группы, внутрь которых будем "складывать" объекты
	panel: {},
})
Ext.reg('modUtil', modUtil)
modUtil = new modUtil()

modUtil.panel.Home = function(config) {
	config = config || {}
	Ext.apply(config, {
		cls: 'container', // Добавляем отступы
		items: [{
			html: ' <h2>' + _('modutilitiesRest') + '<small style="font-size: 10px"><a href="https://forms.gle/FJbfBSutMJwQCgmS8" target="_blank">Bug report</a></small></h2>',
		},
			{
				xtype: extraExt.tabs.xtype,
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
						},
							{
								id: 'Rest-main-table',
								name: 'REST',
								createBtnText: _('create') + ' rest',
								xtype: extraExt.grid.xtype,
								columns: [ // Добавляем ширину и заголовок столбца
									{
										dataIndex: 'id', width: 330, header: 'id', sortable: true, extraExtRenderer: {
											popup: false,
										},
										extraExtEditor: {
											visible: false
										}
										, renderer: extraExt.grid.renderers.default
									},
									{
										dataIndex: 'url', width: 330, header: 'url', sortable: true,
										editor: {xtype: 'textfield'},
										extraExtRenderer: {
											popup: false,
											preRenderer: function(val) {
												if(val) {
													return `<a href="/${val}" target="_blank">${val}</a>`
												} else {
													return extraExt.grid.renderers.default(val)
												}
											},
										}, renderer: extraExt.grid.renderers.default
									},
									{
										dataIndex: 'snippet',
										width: 330,
										header: 'snippet',
										editor: {xtype: 'textfield'},
										sortable: true,
										extraExtRenderer: {
											popup: true,
										},
										extraExtEditor: {
											xtype: extraExt.inputs.modCombo.xtype,
											fieldLabel: _('snippet'),
											forceSelection: false,
											fields: ['id', 'name', 'category_name'],
											url: MODx.config.connector_url,
											action: 'element/snippet/getlist',
											baseParams: {
												sort: 'id',
												dir: 'DESK',
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
										editor: {
											xtype: extraExt.inputs.modCombo.xtype,
											fieldLabel: _('snippet'),
											forceSelection: false,
											fields: ['id', 'name', 'category_name'],
											url: MODx.config.connector_url,
											action: 'element/snippet/getlist',
											baseParams: {
												sort: 'id',
												dir: 'DESK',
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
										renderer: extraExt.grid.renderers.default
									},
									{
										dataIndex: 'param', width: 330, header: 'param', sortable: true,
										editor: {
											xtype: extraExt.inputs.popup.xtype,
											defaultValue: JSON.stringify({
												"scriptProperties": [],
												"headers": [],
												"httpResponseCode": 200
											}),
											fields: [
												{
													xtype: 'hidden' ,
													name: 'scriptProperties',
												},
												{
													xtype: 'hidden' ,
													name: 'headers',
												},
												{
													xtype: 'numberfield',
													name: 'httpResponseCode',
													id: 'add-' + this.ident + '-prefix',
													anchor: '99%',
													allowBlank: true,
												},
											]
										},
										extraExtEditor: {
											xtype: extraExt.inputs.popup.xtype,
											defaultValue: JSON.stringify({
												"scriptProperties": [],
												"headers": [],
												"httpResponseCode": 200
											}),
											fields: [
												{
													xtype: 'hidden' ,
													name: 'scriptProperties',
												},
												{
													xtype: 'hidden' ,
													name: 'headers',
												},
												{
													xtype: 'numberfield',
													name: 'httpResponseCode',
													id: 'add-' + this.ident + '-prefix',
													anchor: '99%',
													allowBlank: true,
												},
											]
										},
										extraExtRenderer: {
											popup: true,
										}, renderer: extraExt.grid.renderers.JSON
									},
									{
										dataIndex: 'allowMethod',
										width: 330,
										header: 'allowMethod',
										sortable: true,
										editor: {xtype: 'textarea'},
										extraExtRenderer: {
											popup: true,
										},
										extraExtEditor: {
											xtype: extraExt.inputs.modComboSuper.xtype,
											id: 'add-' + this.ident + '-allowmethod',
											anchor: '99%',
											forceSelection: true,
											multiple: true,
											fields: ['name'],
											url: modutilitiesConnectorUrl,
											baseParams: {
												action: 'mgr/rest/getlistallowmethod', combo: 1, sort: 'id',
												dir: 'DESK',
											},
											allowBlank: true,
											valueField: 'name',
											displayField: 'name',
										},
										editor: {xtype: 'textfield'},
										renderer: extraExt.grid.renderers.JSON
									},
									{
										dataIndex: 'permission',
										width: 330,
										header: 'permission',
										sortable: true,
										editor: {xtype: 'textarea'},
										extraExtRenderer: {
											popup: true,
										},
										renderer: extraExt.grid.renderers.JSON
									},
									{
										dataIndex: 'BASIC_auth',
										width: 330,
										header: 'BASIC_auth',
										sortable: true,
										extraExtRenderer: {
											popup: false,
										},
										extraExtEditor: {
											xtype: 'xcheckbox',
											fieldLabel: 'Basic auth',
											boxLabel: _('yes'),
											name: 'BASIC_auth',
											id: 'addRest-' + this.ident + '-BASIC_auth',
											anchor: '99%',
											value: false
										},
										editor: {xtype: 'combo-boolean'},
										renderer: extraExt.grid.renderers.BOOL
									},
									{
										dataIndex: 'category',
										width: 330,
										header: 'category',
										sortable: true,
										editor: {
											xtype: extraExt.inputs.modCombo.xtype,
											fieldLabel: _('category'),
											forceSelection: false,
											fields: ['id', 'name', 'allowMethod'],
											action: 'mgr/rest/category/get',
											url: modutilitiesConnectorUrl,
											baseParams: {
												sort: 'name',
												dir: 'DESK',
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
										extraExtRenderer: {
											popup: false,
										},
										extraExtEditor: {
											xtype: extraExt.inputs.modCombo.xtype,
											fieldLabel: _('category'),
											forceSelection: true,
											fields: ['id', 'name', 'allowMethod'],
											action: 'mgr/rest/category/get',
											url: modutilitiesConnectorUrl,
											baseParams: {
												sort: 'name',
												dir: 'DESK',
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
											),
											defaultValue: 'default'
										},
										renderer: extraExt.grid.renderers.default
									},
								],
								autosave: true,
								fields: [
									'id',
									'permission',
									'url',
									'snippet',
									'param',
									'allowMethod',
									'BASIC_auth',
									'category',
								],
								nameField: 'url',
								url: modutilitiesConnectorUrl,
								extraExtSearch: true,
								extraExtUpdate: true,
								extraExtCreate: true,
								extraExtDelete: true,
								requestDataType: 'form',
								action: 'mgr/rest/rest/get',
								save_action: 'mgr/rest/rest/update',
								create_action: 'mgr/rest/rest/create',
								delete_action: 'mgr/rest/rest/delete',
							}]
					},
					{
						title: 'Category',
						id: 'modx-tabs-Category',
						items: [{
							html: _('categories'),
							cls: 'panel-desc',
						}, {
							id: 'Category-main-table',
							name: _('category'),
							createBtnText: _('category_create'),
							xtype: extraExt.grid.xtype,
							columns: [ // Добавляем ширину и заголовок столбца
								{
									dataIndex: 'id', width: 330, header: 'id', sortable: true, extraExtRenderer: {
										popup: false,
									},
									extraExtEditor: {
										visible: false
									}
									, renderer: extraExt.grid.renderers.default
								},
								{
									dataIndex: 'name', width: 330, header: 'name', sortable: true,
									editor: {xtype: 'textfield'},
									extraExtRenderer: {
										popup: false,
									}, renderer: extraExt.grid.renderers.default
								},
								{
									dataIndex: 'param', width: 330, header: 'param', sortable: true,
									editor: {
										xtype: extraExt.inputs.popup.xtype,
										defaultValue: JSON.stringify({
											"scriptProperties": [],
											"headers": [],
											"httpResponseCode": 200
										}),
										fields: [
											{
												xtype: 'hidden' ,
												name: 'scriptProperties',
											},
											{
												xtype: 'hidden' ,
												name: 'headers',
											},
											{
												xtype: 'numberfield',
												name: 'httpResponseCode',
												id: 'add-' + this.ident + '-prefix',
												anchor: '99%',
												allowBlank: true,
											},
										]
									},
									extraExtEditor: {
										xtype: extraExt.inputs.popup.xtype,
										defaultValue: JSON.stringify({
											"scriptProperties": [],
											"headers": [],
											"httpResponseCode": 200
										}),
										fields: [
											{
												xtype: 'hidden' ,
												name: 'scriptProperties',
											},
											{
												xtype: 'hidden' ,
												name: 'headers',
											},
											{
												xtype: 'numberfield',
												name: 'httpResponseCode',
												id: 'add-' + this.ident + '-prefix',
												anchor: '99%',
												allowBlank: true,
											},
										]
									},
									extraExtRenderer: {
										popup: true,
									}, renderer: extraExt.grid.renderers.JSON
								},
								{
									dataIndex: 'allowMethod',
									width: 330,
									header: 'allowMethod',
									sortable: true,
									editor: {xtype: 'textarea'},
									extraExtRenderer: {
										popup: true,
									},
									extraExtEditor: {
										xtype: extraExt.inputs.modComboSuper.xtype,
										id: 'add-' + this.ident + '-allowmethod',
										anchor: '99%',
										forceSelection: true,
										multiple: true,
										fields: ['name'],
										url: modutilitiesConnectorUrl,
										baseParams: {
											action: 'mgr/rest/getlistallowmethod', combo: 1, sort: 'id',
											dir: 'DESK',
										},
										allowBlank: true,
										valueField: 'name',
										displayField: 'name',
									},
									editor: {xtype: 'textfield'},
									renderer: extraExt.grid.renderers.JSON
								},
								{
									dataIndex: 'permission',
									width: 330,
									header: 'permission',
									sortable: true,
									editor: {xtype: 'textarea'},
									extraExtRenderer: {
										popup: true,
									},
									renderer: extraExt.grid.renderers.JSON
								},
								{
									dataIndex: 'BASIC_auth',
									width: 330,
									header: 'BASIC_auth',
									sortable: true,
									extraExtRenderer: {
										popup: false,
									},
									extraExtEditor: {xtype: 'combo-boolean'},
									editor: {xtype: 'combo-boolean'},
									renderer: extraExt.grid.renderers.BOOL
								},
							],
							autosave: true,
							fields: [
								'id',
								'name',
								'permission',
								'param',
								'allowMethod',
								'BASIC_auth',
							],
							nameField: 'name',
							url: modutilitiesConnectorUrl,
							extraExtSearch: true,
							extraExtUpdate: true,
							extraExtCreate: true,
							extraExtDelete: true,
							requestDataType: 'form',
							action: 'mgr/rest/category/get',
							save_action: 'mgr/rest/category/update',
							create_action: 'mgr/rest/category/create',
							delete_action: 'mgr/rest/category/delete',
						}]
					},
					{
						title: 'log',
						id: 'modx-tabs-log',
						items: [{
							html: 'connection log',
							cls: 'panel-desc',
						}, {
							id: 'log-main-table',
							name: 'Log',
							xtype: extraExt.grid.xtype,
							columns: [ // Добавляем ширину и заголовок столбца
								{
									dataIndex: 'id', width: 330, header: 'id', sortable: true, extraExtRenderer: {
										popup: false,
									},
									renderer: extraExt.grid.renderers.default
								},
								{
									dataIndex: 'rest_id',
									width: 330,
									sortable: true,
									header: _('modutilities.rest_id'),
									renderer: extraExt.grid.renderers.default,
								},
								{
									dataIndex: 'input',
									width: 670,
									header: _('modutilities.input'),
									extraExtRenderer: {
										popup: true,
									},
									renderer: extraExt.grid.renderers.JSON,
								},
								{
									dataIndex: 'output', width: 670, header: _('modutilities.output'),
									extraExtRenderer: {
										popup: true,
									},
									renderer: extraExt.grid.renderers.HTML,
								},
								{
									dataIndex: 'user',
									sortable: true,
									width: 670,
									header: _('modutilities.user'),
									extraExtRenderer: {
										popup: true,
									},
									renderer: extraExt.grid.renderers.JSON,
								},
								{
									dataIndex: 'time', sortable: true, width: 670, header: _('modutilities.time'),
									renderer: extraExt.grid.renderers.default,
								},
								{
									dataIndex: 'datetime',
									sortable: true,
									width: 670,
									header: _('modutilities.datetime'),
									renderer: extraExt.grid.renderers.default,
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
							nameField: 'rest_id',
							url: modutilitiesConnectorUrl,
							extraExtSearch: true,
							requestDataType: 'form',
							action: 'mgr/rest/log/get',
						}]
					}
				]
			}]
	})
	modUtil.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}

Ext.extend(modUtil.panel.Home, MODx.Panel)
Ext.reg('modUtil-panel-home', modUtil.panel.Home)



