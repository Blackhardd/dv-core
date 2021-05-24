<?php

/**
 *  Helpers
 */
function dv_get_post_data(){
    $data = array();

    foreach( $_POST as $key => $value ){
        if( $key != 'action' ):
            $data[$key] = $value;
        endif;
    }

    return $data;
}

function dv_prepare_data_for_sheet( $data ){
    $prepared = array();

    $prepared[] = "{$data['name']} {$data['surname']}";
    $prepared[] = $data['email'];
    $prepared[] = $data['phone'];
    $prepared[] = $data['birthdate'];
    $prepared[] = $data['communication_method'];

    if( $data['donator'] == 'new' ){
        $prepared[] = $data['height'];
        $prepared[] = $data['weight'];
        $prepared[] = ( $data['eggs_donated'] == 'on' ) ? __( 'Ano', 'dv' ) : __( 'Ne', 'dv' );
    }
    else if( $data['donator'] == 'existing' ){
        $prepared[] = $data['last_donation'];
        $prepared[] = $data['last_menstruation'];
        $prepared[] = ( $data['hormonal_contraception'] == 'on' ) ? __( 'Ano', 'dv' ) : __( 'Ne', 'dv' );
    }

    $prepared[] = $data['comment'];

    return $prepared;
}

function dv_extract_sheet_ids( $sheets = false ){
    if( $sheets ){
        $processed = array();

        foreach( $sheets as $sheet ){
            $props = $sheet->getProperties();
            $processed[] = $props->sheetId;
        }

        return $processed;
    }

    return null;
}


/**
 *  Google API.
 */
function dv_get_google_client(){
    $creds = DV_PLUGIN_PATH . 'google-creds.json';

    $client = new Google_Client();
    $client->setAuthConfig( $creds );
    $client->addScope( Google_Service_Sheets::SPREADSHEETS );

    return $client;
}

function dv_append_row_to_sheet( $client = false, $spreadsheet_id = false, $data = false, $sheet = 0 ){
    if( $client ){
        $service = new Google_Service_Sheets( $client );

        $sheets = dv_extract_sheet_ids( $service->spreadsheets->get( $spreadsheet_id )->getSheets() );

        $values = array();
        foreach( $data as $d ){
            $cell_data = new Google_Service_Sheets_CellData();
            $value = new Google_Service_Sheets_ExtendedValue();

            $value->setStringValue( $d );
            $cell_data->setUserEnteredValue( $value );

            $values[] = $cell_data;
        }

        $row_data = new Google_Service_Sheets_RowData();
        $row_data->setValues( $values );

        $append_request = new Google_Service_Sheets_AppendCellsRequest();
        $append_request->setSheetId( $sheets[$sheet] );
        $append_request->setRows( $row_data );
        $append_request->setFields( 'userEnteredValue' );

        $request = new Google_Service_Sheets_Request();
        $request->setAppendCells( $append_request );

        $requests = array();
        $requests[] = $request;

        $batch_update_request = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(
            array(
                'requests'  => $requests
            )
        );

        $response = $service->spreadsheets->batchUpdate( $spreadsheet_id, $batch_update_request );

        if( $response->valid() ){
            return true;
        }

        return false;
    }

    return false;
}

function dv_add_user_data_to_sheet( $data = false, $sheet = 0 ){
    if( $data ){
        $client = dv_get_google_client();
        $spreadsheet_id = get_option( 'google_sheet_id' );

        if( $spreadsheet_id ){
            return dv_append_row_to_sheet( $client, $spreadsheet_id, $data, $sheet );
        }
    }

    return null;
}