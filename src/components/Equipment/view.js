import React from 'react';
import { connect } from 'react-redux';
import { Table, Nav, Toast, Button } from '@douyinfe/semi-ui'
import { IconRefresh, IconPlus, IconMapPin, IconVerify, IconIdCard } from '@douyinfe/semi-icons';

import agent from '../../agent';
import {
  EQUIPMENT_VIEW_PAGE_LOADED
} from '../../constants/actionTypes';
import ModalCreate from './modalCreate';
import { history } from '../../store';

const mapStateToProps = state => ({
  ...state.EquipmentView,
  currentUser: state.common.currentUser,
});

const mapDispatchToProps = dispatch => ({
    onLoad: payload =>
      dispatch({ type: EQUIPMENT_VIEW_PAGE_LOADED, payload }),
    // onUnload: () =>
    //   dispatch({ type: ARTICLE_PAGE_UNLOADED })
  });

class EquipmentView extends React.PureComponent {
    
    constructor(){
        super()

        this.state = {
            loading: true,
            currentPage: 1,
            pageSize: 20,
            sorter: {},
            dataSource: [],
            selectedRow: [],
            showModalCreate: false
        }
    }

    componentDidUpdate(){
        if (this.state.dataSource)
            this.setState({loading: false})
    }

    async getData(page, size, sorter){
        this.setState({...this.state, loading: true})

        if (typeof(page) == 'undefined' && typeof(size) == 'undefined')
        {
            const data = await agent.EquipmentService.view(this.state.currentPage, this.state.pageSize, this.state.sorter)
            this.setState({...this.state, dataSource: data, loading: false, sorter: sorter})
        }
        else
        {
            const data = await agent.EquipmentService.view(page, size, sorter)
            this.setState({dataSource: data, currentPage: page, pageSize: size, loading: false, sorter: sorter})
        }
        
    }

    async componentDidMount(){
        this.getData()
    }

    handlePageChange(changes){
        this.getData(changes.pagination.currentPage, changes.pagination.pageSize, changes.sorter)
    }

    rowSelection = {
        onSelect: (record, selected) => {
            if (selected)
                this.setState(prevState => ({
                        selectedRow: [...prevState.selectedRow, record]
                    })
                )
            else
                this.setState(prevState => ({
                        selectedRow: [...prevState.selectedRow.filter(el => el.id != record.id)]
                    })
                )
        },
        onSelectAll: (selected, records) => {
            this.setState({...this.state, selectedRow: records})
        }
    }

    async sentToCheck(){
        if (this.state.selectedRow == null || this.state.selectedRow.length <= 0)
        {
            Toast.warning("Не выбрано оборудование для отправки на поверку.");
            return;
        }

        let obj = {
            equipments: []
        }

        this.state.selectedRow.forEach(el => {
            obj.equipments.push({ equipmentId: el.id })
        })

        let result = await agent.VerificationService.add(obj);

        if (result)
            Toast.info('Оборудование добавлено на поверку')
    }

    handleModalCreate = value => {
        this.setState({...this.state, showModalCreate: value});
    }

    openCard(record){
        history.push(`/equipment/view/${record.id}`)
    }

    render() {
        const columns = [
            { title: 'Номер', dataIndex: 'number', width: 200, sorter: (a, b) => a.number - b.number > 0 ? 1 : -1},
            { title: 'Отдел', dataIndex: 'department.name', width: 200, sorter: (a, b) => a.department.name - b.department.name > 0 ? 1 : -1},
            { title: 'Тип', dataIndex: 'type.name', width: 200, sorter: (a, b) => a.type.name - b.type.name > 0 ? 1 : -1},
            { title: 'Наименование', dataIndex: 'name' , width: 200, sorter: (a, b) => a.name - b.name > 0 ? 1 : -1},
            { title: 'Модель', dataIndex: 'model', width: 200, sorter: (a, b) => a.model - b.model > 0 ? 1 : -1},
            { title: 'С/Н', dataIndex: 'serialNumber', width: 200, sorter: (a, b) => a.serialNumber - b.serialNumber > 0 ? 1 : -1},
            { title: 'Статус', dataIndex: 'tag.name', width: 200, sorter: (a, b) => a.tag.name - b.tag.name > 0 ? 1 : -1},
            { title: '', dataIndex: 'actions', width: 100, render: (text, record, index) => {
                return (
                    <>
                        <Button icon={<IconIdCard />} aria-label={'Карточка'} theme={'borderless'} type={'tertiary'} onClick={(e) => this.openCard(record)}/>
                    </>
                );
            }}
        ];

        return (
            <>
                <Table
                columns={columns}
                dataSource={this.state.dataSource.data}
                loading={this.state.loading}
                resizable
                bordered
                showHeader={true}
                rowKey={'id'}
                title={<Nav
                    header={{text: 'Оборудование'}}
                    style={{padding: 0}}
                    mode={'horizontal'}
                    items={
                        [
                            { itemKey: 'update', text: 'Обновить', icon: <IconRefresh />, onClick: (e) => this.getData() },
                            { itemKey: 'create', text: 'Создать', icon: <IconPlus />, onClick: (e) => this.handleModalCreate(true)},
                            { itemKey: 'toVerification', text: 'Отправить на поверку', icon: <IconVerify />, onClick: (e) => this.sentToCheck() },
                            { itemKey: 'changeLocation', text: 'Сменить местоположение', icon: <IconMapPin /> },
                        ]
                    } 
                    />}
                rowSelection={this.rowSelection}
                onChange={(changes) => this.handlePageChange(changes)}
                pagination={{
                    currentPage: this.state.currentPage,
                    pageSize: this.state.pageSize,
                    total: this.state.dataSource.totalRecords,
                    showSizeChanger: true
                }}/>
                <ModalCreate onClose={this.handleModalCreate} show={this.state.showModalCreate} />
            </>
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(EquipmentView);
