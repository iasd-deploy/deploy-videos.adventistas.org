import {__} from '@wordpress/i18n';
import {useCallback, useEffect, useState} from '@wordpress/element';
import DataTable, {createTheme} from "react-data-table-component";
import UserDataTableStore from "./UserDataTableStore";
import FilterData from "../FilterData";

import {button} from "@wordpress/components";
import {produce} from "immer";
import AddIpAddressModal from "./AddIpAddressModal";
import AddUserModal from "./AddUserModal";
import EventLogDataTableStore from "../EventLog/EventLogDataTableStore";
import useFields from "../FieldsData";
import FieldsData from "../FieldsData";
import SearchBar from "../DynamicDataTable/SearchBar";
import AddButton from "../DynamicDataTable/AddButton";

const UserDatatable = (props) => {
    let {
        UserDataTable,
        dataLoaded,
        fetchData,
        processing,
        handleUserTableFilter,
        handleUserTablePageChange,
        pagination,
        resetRow,
        resetMultiRow,
        dataActions,
        handleUserTableRowsChange,
        handleUserTableSort,
        handleUserTableSearch,
        updateMultiRow,
        updateRow,
        rowCleared
    } = UserDataTableStore()
    const {showSavedSettingsNotice} = FieldsData();
    const {
        fetchDynamicData,
    } = EventLogDataTableStore();
    //here we set the selectedFilter from the Settings group
    const {
        selectedFilter,
        setSelectedFilter,
        activeGroupId,
        getCurrentFilter,
        setProcessingFilter,
    } = FilterData();

    const [rowsSelected, setRowsSelected] = useState([]);
    const [addingUser, setAddingUser] = useState(false);
    const [user, setUser] = useState('');
    const [tableHeight, setTableHeight] = useState(600);  // Starting height
    const rowHeight = 50; // Height of each row.

    const moduleName = 'rsssl-group-filter-limit_login_attempts_users';
    const {fields, fieldAlreadyEnabled, getFieldValue, saveFields} = useFields();

    const buildColumn = useCallback((column) => ({
        name: column.name,
        sortable: column.sortable,
        searchable: column.searchable,
        width: column.width,
        visible: column.visible,
        column: column.column,
        selector: row => row[column.column],
    }), []);
    //getting the fields from the props
    let field = props.field;
    //we loop through the fields
    const columns = field.columns.map(buildColumn);

    const searchableColumns = columns
        .filter(column => column.searchable)
        .map(column => column.column);

    useEffect(() => {
        const currentFilter = getCurrentFilter(moduleName);
        if (!currentFilter) {
            setSelectedFilter('locked', moduleName);
        }
        setProcessingFilter(processing);
        handleUserTableFilter('status', currentFilter);
    }, [moduleName, handleUserTableFilter, getCurrentFilter(moduleName), setSelectedFilter, UserDatatable, processing]);

    useEffect(() => {
        setRowsSelected([]);
    }, [UserDataTable]);

    //if the dataActions are changed, we fetch the data
    useEffect(() => {
        //we make sure the dataActions are changed in the store before we fetch the data
        if (dataActions) {
            fetchData(field.action, dataActions)
        }
    }, [dataActions.sortDirection, dataActions.filterValue, dataActions.search, dataActions.page, dataActions.currentRowsPerPage, fieldAlreadyEnabled('enable_limited_login_attempts')]);

    let enabled = getFieldValue('enable_limited_login_attempts');


    const customStyles = {
        headCells: {
            style: {
                paddingLeft: '0', // override the cell padding for head cells
                paddingRight: '0',
            },
        },
        cells: {
            style: {
                paddingLeft: '0', // override the cell padding for data cells
                paddingRight: '0',
            },
        },
    };
    createTheme('really-simple-plugins', {
        divider: {
            default: 'transparent',
        },
    }, 'light');

    const handleOpen = () => {
        setAddingUser(true);
    };

    const handleClose = () => {
        setAddingUser(false);
    };

    //now we get the options for the select control
    let options = props.field.options;
    //we divide the key into label and the value into value
    options = Object.entries(options).map((item) => {
        return {label: item[1], value: item[0]};
    });

    const resetUsers = useCallback(async (data) => {
        if (Array.isArray(data)) {
            const ids = data.map((item) => item.id);
            await resetMultiRow(ids, dataActions).then((response) => {
                if (response && response.success) {
                    showSavedSettingsNotice(response.message);
                } else {
                    showSavedSettingsNotice(response.message, 'error');
                }
            });
            setRowsSelected([]);
        } else {
            await resetRow(data, dataActions).then((response) => {
                if (response && response.success) {
                    showSavedSettingsNotice(response.message);
                } else {
                    showSavedSettingsNotice(response.message);
                }
            });
        }
        await fetchDynamicData('event_log');
    }, [resetMultiRow, resetRow, fetchDynamicData, dataActions]);

    const handleSelection = useCallback((state) => {
        setRowsSelected(state.selectedRows);
    }, []);

    const ActionButton = ({onClick, children, className}) => (
        <div className={`rsssl-action-buttons__inner`}>
            <button
                className={`button ${className} rsssl-action-buttons__button`}
                onClick={onClick}
                disabled={processing}
            >
                {children}
            </button>
        </div>
    );

    const generateActionButtons = useCallback((id, status, region_name) => (
        <div className="rsssl-action-buttons">
            <ActionButton onClick={() => {
                resetUsers(id);
            }}
                          className="button-red">
                {__("Delete", "really-simple-ssl")}
            </ActionButton>
        </div>
    ), [getCurrentFilter(moduleName), moduleName, resetUsers]);


//we convert the data to an array
let data = {...UserDataTable.data};

for (const key in data) {
    let dataItem = {...data[key]}
    //we log the dataItem
    //we add the action buttons
    dataItem.action = generateActionButtons(dataItem.id);
    dataItem.status = __(dataItem.status = dataItem.status.charAt(0).toUpperCase() + dataItem.status.slice(1), 'really-simple-ssl');
    data[key] = dataItem;
}

    let paginationSet;
    paginationSet = typeof pagination !== 'undefined';

    useEffect(() => {
        if (Object.keys(data).length === 0 ) {
            setTableHeight(100); // Adjust depending on your UI measurements
        } else {
            setTableHeight(rowHeight * (paginationSet ? pagination.perPage + 2 : 12)); // Adjust depending on your UI measurements
        }

    }, [paginationSet, pagination?.perPage, data]);

return (
    <>
        <AddUserModal
            isOpen={addingUser}
            onRequestClose={handleClose}
            options={options}
            value={user}
            status={getCurrentFilter(moduleName)}
            dataActions={dataActions}
        >
        </AddUserModal>
        <div className="rsssl-container">
            {/*display the add button on left side*/}
            <AddButton
                getCurrentFilter={getCurrentFilter}
                moduleName={moduleName}
                handleOpen={handleOpen}
                processing={processing}
                blockedText={__("Block Username", "really-simple-ssl")}
                allowedText={__("Trust Username", "really-simple-ssl")}
            />
            {/*Display the search bar*/}
            <SearchBar handleSearch={handleUserTableSearch} searchableColumns={searchableColumns}/>
        </div>
        { /*Display the action form what to do with the selected*/}
        {rowsSelected.length > 0 && (
            <div style={{
                marginTop: '1em',
                marginBottom: '1em',
            }}>
                <div className={"rsssl-multiselect-datatable-form rsssl-primary"}
                >
                    <div>
                        {__("You have selected %s rows", "really-simple-ssl").replace('%s', rowsSelected.length)}
                    </div>

                    <div className="rsssl-action-buttons">
                        {/* if the id is new we show the Delete button */}
                        <ActionButton
                            className="button button-red rsssl-action-buttons__button"
                            onClick={() => {resetUsers(rowsSelected)}}>
                            {__("Delete", "really-simple-ssl")}
                        </ActionButton>
                    </div>
                </div>
            </div>
        )}
        {/*Display the datatable*/}
            <DataTable
                columns={columns}
                data={processing && !dataLoaded? [] : Object.values(data)}
                dense
                pagination={!processing}
                paginationServer
                paginationTotalRows={paginationSet? pagination.totalRows: 10}
                onChangeRowsPerPage={handleUserTableRowsChange}
                onChangePage={handleUserTablePageChange}
                sortServer={!processing}
                onSort={handleUserTableSort}
                paginationRowsPerPageOptions={[10, 25, 50, 100]}
                selectableRows={!processing}
                onSelectedRowsChange={handleSelection}
                clearSelectedRows={rowCleared}
                noDataComponent={__("No results", "really-simple-ssl")}
                persistTableHead
                theme="really-simple-plugins"
                customStyles={customStyles}
            ></DataTable>
        {!enabled && (
            <div className="rsssl-locked">
                <div className="rsssl-locked-overlay"><span
                    className="rsssl-task-status rsssl-open">{__('Disabled', 'really-simple-ssl')}</span><span>{__('Activate Limit login attempts to enable this block.', 'really-simple-ssl')}</span>
                </div>
            </div>
        )}
    </>
);

}
export default UserDatatable;


