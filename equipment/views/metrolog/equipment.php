<?php
	use yii\helpers\Url;
	$this->registerJsFile('@web/assets/vendor/vue/vue.min.js'); 
	$this->registerJsFile('@web/assets/vendor/vue/axios.min.js');
	$this->registerJsFile('@web/assets/js/equipment/metrolog_equipment.js');
?>
<div class="row" id="demo1">
	<div class="sixteen wide column">
		<equipment-grid
			:rows="gridData"
			:columns="gridColumns.tableColumn"
			:filters="filters"
			:count-post="countPost"
			@request="selectedMaterials"
			@equipment="check.id_equipment">
		</equipment-grid>
	</div>
	<div id="modalPrint" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Регистрационная карта | Этикетка
			</div>
		</div>
		<div class="content">
		</div>
		<div class="actions">
			<button class="ui approve green button">Сформировать</button>
			<button class="ui deny orange button">Отмена</button>
		</div>
	</div>
	<div id="modalFilter" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Поиск
			</div>
		</div>
		<div class="content">
			<div class="ui form">
				<div class="field" v-for="key in gridColumns.filterColumn" v-show="filters.hasOwnProperty(Object.keys(key))">
					<label>{{ Object.values(key)[0] }}</label>
					<select multiple class="ui search dropdown" v-model="filters[Object.keys(key)]">
						<option v-for="col in returnUniq(Object.keys(key))" v-bind:value="col">{{ col }}</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div id="modalAppendEq" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Оборудование
			</div>
		</div>
		<div class="content">
			<div class="ui form">
				<div class="field">
					<label>Отдел</label>
					<select class="ui search dropdown" v-model="equipment.id_department">
						<option v-for="department in listDepartment" v-bind:value="department.id">{{ department.title }}</option>
					</select>
				</div>
				<div class="field">
					<label>Вид</label>
					<select class="ui search dropdown" v-model="equipment.id_equipment_type">
						<option v-for="type in listType" v-bind:value="type.id">{{ type.title }}</option>
					</select>
				</div>
				<div class="field">
					<label>Номер</label>
					<input type="text" v-model="equipment.number">
				</div>
				<div class="field">
					<label>Оборудование</label>
					<input type="text" v-model="equipment.title">
				</div>
				<div class="field">
					<label>Модель</label>
					<input type="text" v-model="equipment.model">
				</div>
				<div class="field">
					<label>Серийный номер</label>
					<input type="text" v-model="equipment.serial_number">
				</div>
				<div class="field">
					<label>Производитель</label>
					<input type="text" v-model="equipment.manufacturer">
				</div>
				<div class="field">
					<label>Дата изготовления</label>
					<input type="date" v-model="equipment.date_create">
				</div>
				<div class="field">
					<label>Инвентарный номер</label>
					<input type="text" v-model="equipment.inventory_number">
				</div>
				<div class="field">
					<label>Местоположение</label>
					<select class="ui search dropdown" v-model="equipment.id_location">
						<option v-for="location in listLocations" v-bind:value="location.id">{{ location.cabinet_number }}</option>
					</select>
				</div>
			</div>
		</div>
		<div class="actions">
			<button class="ui approve green button" v-on:click="appendEq()">Добавить</button>
			<button class="ui deny orange button">Отмена</button>
		</div>
	</div>
	<div id="modalArchive" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Архив | Консервация
			</div>
		</div>
		<div class="content">
		</div>
		<div class="actions">
			<button class="ui approve green button">Отправить</button>
			<button class="ui deny orange button">Отмена</button>
		</div>
	</div>
	<div id="modalHandoff" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Перемещение
			</div>
		</div>
		<div class="content">
		</div>
		<div class="actions">
			<button class="ui approve green button">Отправить</button>
			<button class="ui deny orange button">Отмена</button>
		</div>
	</div>
	<div id="modalCheck" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Поверка | Проверка
			</div>
		</div>
		<div class="content">
			<div class="ui form">
				<div class="field">
					<label>Дата текущей поверки | проверки</label>
					<input type="date" v-model="check.current">
				</div>
				<div class="field">
					<label>Дата следующей поверки | проверки</label>
					<input type="date" v-model="check.next">
				</div>
			</div>
		</div>
		<div class="actions">
			<button class="ui approve green button" v-on:click="changeCheck()">Изменить</button>
			<button class="ui deny orange button">Отмена</button>
		</div>
	</div>
	<div id="modalEdit" class="ui tiny card modal">
		<div class="content">
			<div class="content header">
			Редактирование
			</div>
		</div>
		<div class="content">
		</div>
		<div class="actions">
			<button class="ui approve green button">Отправить</button>
			<button class="ui deny orange button">Отмена</button>
		</div>
	</div>
</div>
<template id="equipment-grid">
	<table class="ui compact selectable table">
		<thead>
			<tr>
				<th v-bind:colspan="columns.length + 1">
					Оборудование
					<button class="ui green right floated mini icon button" v-on:click="clearFilter()"><i class="icon undo"></i></button>
					<button class="ui teal right floated mini icon button" v-on:click="showModal('Filter')"><i class="icon filter"></i></button>
					<div class="ui blue right floated icon top left mini pointing dropdown button">
						<i class="icon print"></i>
						<i class="icon dropdown"></i>
						<div class="menu">
							<a class="item">Этикетка</a>
							<a class="item">Регистрационная карта</a>
						</div>
					</div>
<!-- 					<button class="ui blue right floated mini icon button" v-on:click="showModal('Print')"><i class="icon print"></i>v-on:click="showModal('AppendEq')"
					</button> -->
					<a href="<?php echo Url::toRoute(['append/']) ?>" class="ui yellow right floated mini icon button" ><i class="icon plus"></i></a>
				</th>
			</tr>
			<tr>
				<th v-for="key in columns" @click="sortBy(Object.keys(key)[0])">
					{{ Object.values(key)[0] }}
					<i :class="{'icon caret up': (sortColumns[Object.keys(key)[0]] > 0) && Object.keys(key)[0] === sortKey, 'icon caret down': (sortColumns[Object.keys(key)[0]] < 0) && Object.keys(key)[0] === sortKey}"></i>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="equipment in paginateRows">
				<td class="collapsing">
					<div class="ui checkbox">
					<input type="checkbox"
					v-bind:value="{id_equipment: equipment.id}" 
					v-model="selectedMaterials">
					<label></label>
					</div>
				</td>
				<td class="collapsing right aligned">{{ equipment.number }} / {{ equipment.id_department }} / {{ equipment.type }}</td>
				<td>{{ equipment.equipment }}</td>
				<td class="collapsing right aligned">{{ equipment.serial_number }}</td>
				<td class="collapsing">{{ equipment.date_current_check }}</td>
				<td class="collapsing">{{ equipment.date_next_check }}</td>
				<td class="collapsing">
					<div class="ui icon left pointing dropdown mini button">
						<!-- <div class="text">Действие</div> -->
						<i class="settings icon"></i>
						<div class="menu">
							<a v-bind:href="'details/' + equipment.id" target="_blank" class="item">Подробнее</a>
							<div class="item" v-on:click="showModal('Archive')">Архив - Консервация</div>
							<div class="item" v-on:click="showModal('Handoff')">Перемещение</div>
							<div class="item" v-on:click="showModal('Check', equipment.id)">Поверка - Проверка</div>
							<div class="item" v-on:click="showModal('Edit')">Редактирование</div>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th v-bind:colspan="columns.length + 1">
					<div class="ui left floated label">
						Страница {{ currentPage }} из {{ listPages.length }}
					</div>
					<div class="ui icon basic right floated small buttons">
						<button class="ui button" v-on:click="currentPage = listPages[0]"><i class="icon angle double left"></i></button>
						<button class="ui button" v-on:click="currentPage--" v-if="currentPage != 1"><i class="icon angle left"></i></button>
						<div class="ui form">
							<div class="field">
								<input type="text" v-model="currentPage" v-bind:value="currentPage">
							</div>
						</div>
						<button class="ui button" v-on:click="currentPage++" v-if="currentPage < listPages.length"><i class="icon angle right"></i></button>
						<button class="ui button" v-on:click="currentPage = listPages.length"><i class="icon angle double right"></i></button>
					</div>
				</th>
			</tr>
		</tfoot>
	</table>
</template>