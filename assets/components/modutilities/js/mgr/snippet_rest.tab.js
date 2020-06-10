Ext.onReady(function() {
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
// Мы не будем вставлять компонент на страницу с помощью MODx.add,
// поэтому нужно создать экземпляр нашего класса
// чтобы можно было обращатсья к его свойствам
modUtil = new modUtil()

// Создаём внутри компонента главную панель
// (через точку в JS обозначется вложенность массива, то есть мы создаём
// объект Home внутри panel, который, в свою очередь, находится в modUtil)
modUtil.panel.Home = function(config) {
	config = config || {}
	Ext.apply(config, {
		cls: 'container', // Добавляем отступы
		items: [{
			html: ' <h2>modUtil REST</h2>'
		},
			{
				xtype: 'modx-tabs',
				items: [
					{
						title: 'Rest',
						items: [{
							html: 'Things 1 description',
							cls: 'panel-desc',
						}, {
							xtype: 'modUtil-grid',
							columns: [ // Добавляем ширину и заголовок столбца
								{dataIndex: 'id', width: 330, header: 'id'},
								{dataIndex: 'permission', width: 670, header: 'permission'},
								{dataIndex: 'url', width: 670, header: 'url'},
								{dataIndex: 'snippet', width: 670, header: 'snippet'},
								{dataIndex: 'param', width: 670, header: 'param'},
								{dataIndex: 'allowMethod', width: 670, header: 'allowMethod'},
								{
									dataIndex: 'BASIC_autch', width: 670, header: 'BASIC_autch',
									renderer: function(val) {
										if(val === 1) {
											val = '<span class="true">TRUE</span>'
										} else {
											val = '<span class="false">FALSE</span>'
										}
										return val
									}
								},
								{dataIndex: 'catName', width: 670, header: 'category'}
							],

							action: 'mgr/rest/GetRest',
							fields: [
								'id',
								'permission',
								'url',
								'snippet',
								'param',
								'allowMethod',
								'BASIC_autch',
								'category',
								'catName',
							]
						}
						]
					},
					{
						title: 'Category',
						items: [{
							html: 'Things 3 description',
							cls: 'panel-desc',
						}, {
							xtype: 'modUtil-grid',
							columns: [ // Добавляем ширину и заголовок столбца
								{dataIndex: 'id', width: 330, header: 'id'},
								{dataIndex: 'permission', width: 670, header: 'permission'},
								{dataIndex: 'url', width: 670, header: 'url'},
								{dataIndex: 'snippet', width: 670, header: 'snippet'},
								{dataIndex: 'param', width: 670, header: 'param'},
								{dataIndex: 'allowMethod', width: 670, header: 'allowMethod'},
								{
									dataIndex: 'BASIC_autch', width: 670, header: 'BASIC_autch',
									renderer: function(val) {
										if(val === 1) {
											val = '<span class="true">TRUE</span>'
										} else {
											val = '<span class="false">FALSE</span>'
										}
										return val
									}
								},
								{dataIndex: 'catName', width: 670, header: 'category'}
							],

							action: 'mgr/rest/GetRest',
							fields: [
								'id',
								'permission',
								'url',
								'snippet',
								'param',
								'allowMethod',
								'BASIC_autch',
								'category',
								'catName',
							]
						}
						]
					},
					{
						title: 'log',
						items: [{ // Внутри таба ещё один HTML-блок с классом panel-desc
							html: 'Things 2 description',
							cls: 'panel-desc',
						}]
					}
				]
			}
		]
	})
	modUtil.panel.Home.superclass.constructor.call(this, config) // Чёртова магия =)
}

Ext.extend(modUtil.panel.Home, MODx.Panel) // Наша панель расширяет объект MODX.Panel
Ext.reg('modUtil-panel-home', modUtil.panel.Home) // Регистрируем новый xtype для панели

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
		url: connector_url,
	})
	modUtil.grid.ModGrid.superclass.constructor.call(this, config) // Магия
}
Ext.extend(modUtil.grid.ModGrid, MODx.grid.Grid) // Наша табличка расширяет GridPanel
Ext.reg('modUtil-grid', modUtil.grid.ModGrid) // Регистрируем новый xtype