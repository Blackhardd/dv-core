<?php

/**
 *  Admin AJAX actions.
 */
add_action( 'wp_ajax_dv_save_plugin_settings', 'dv_save_plugin_settings' );
function dv_save_plugin_settings(){
    $data = dv_get_post_data();

    foreach( $data as $key => $value ):
        update_option( $key, $value );
    endforeach;

    wp_die();
}


/**
 *  Front-end AJAX actions.
 */
add_action( 'wp_ajax_donation_apply', 'dv_donation_apply' );
add_action( 'wp_ajax_nopriv_donation_apply', 'dv_donation_apply' );
function dv_donation_apply(){
    $data = dv_get_post_data();

    $admin_recipients = get_option( 'recipients' );
    $admin_message = "
        <h1>Darování vajíček</h1>
    ";

    $admin_message .= "
        <p>
            <b>Jméno:</b> {$data['name']}<br>
            <b>Příjmení:</b> {$data['surname']}<br>
            <b>Email:</b> {$data['email']}<br>
            <b>Telefon:</b> {$data['phone']}<br>
            <b>Rok narození:</b> {$data['birthdate']}<br>  
            <b>Preferovaný způsob komunikace:</b> {$data['communication_method']}
        </p>
    ";

    if( $data['donator'] == 'new' ){
        $admin_message .= "
            <p><b>Daruje se poprvé.</b></p>
            <p>
                <b>Výška:</b> {$data['height']} cm<br>
                <b>Váha:</b> {$data['weight']} kg<br>
                <b>Léčila jste se někdy na neplodnost:</b> 
        ";

        $admin_message .= ( isset( $data['infertility_threatment'] ) ) ? "Ano<br>" : "Ne<br>";
        $admin_message .= "<b>Darovala jste někdy vajíčka:</b> ";
        $admin_message .= ( isset( $data['eggs_donated'] ) ) ? "Ano<br>" : "Ne<br>";
        $admin_message .= "<b>Jste přihlášena k veřejnému zdravotnímu pojištění v České republice:</b> ";
        $admin_message .= ( isset( $data['insurance_registration'] ) ) ? "Ano<br>" : "Ne<br>";
    }
    else{
        $admin_message .= "
            <p><b>Již jste darovali.</b></p>
            <p>
                <b>Poslední darování:</b> {$data['last_donation']}<br>
                <b>Datum poslední menstruace - 1. den:</b> {$data['last_menstruation']}<br>
                <b>Hormonální antikoncepce:</b> 
        ";

        $admin_message .= ( isset( $data['hormonal_contraception'] ) ) ? "Ano<br>" : "Ne<br>";
    }

    $admin_message .= "</p>";

    $is_admin_message_sent = wp_mail( $admin_recipients, 'Darování vajíček', $admin_message, ['content-type: text/html'] );

    $user_message = "
        <h1>Dobrý den, {$data['name']}!</h1>
        <p>Děkujeme, že jste se rozhodla pro darování vajíček a chcete pomoci změnit svět bezdětnému páru.
        Zařadili jsme Vás do databáze dárkyň vajíček v Prague Fertility Centre s.r.o.</p>
        
        <h3>Objednejte se prosím do našeho centra:</h3>
        <p>- telefonicky na čísle <a href='tel:+420255707027'>255 707 027</a> případně na <a href='tel:+420778528182'>778 528 182</a> Volat můžete PO - PÁ 8.00 – 16.00<br>
        - emailem na adrese <a href='mailto:darkyne@pragueivf.cz'>darkyne@pragueivf.cz</a><br>
        - pošlete sms na 778 528 182</p>
        
         
        <h3>Ambulanci pro dárkyně najdete na adrese:</h3>
        <p><a href='https://goo.gl/maps/XU9nXNm9XTt' target='_blank'>Sokolovská 304, Praha 9 - Vysočany (budova Vysočanské polikliniky)</a><br>
        tel.: <a href='tel:+420255707027'>255 707 027</a>, <a href='tel:+420778528182'>778 528 182</a><br>
        <a href='https://g.page/pragueivf?share'>Mapa</a><br>
        Dostanete se k nám metrem - stanice Českomoravská, trasa B, nebo tramvají č. 16 stanice Poliklinika Vysočany<br>
        <a href='https://www.darcovstvivajicek.cz'>www.darcovstvivajicek.cz</a>
    ";

    $is_user_message_sent = wp_mail( $data['email'], 'Darování vajíček', $user_message, ['content-type: text/html'] );
    $is_added_to_sheet = dv_add_user_data_to_sheet( dv_prepare_data_for_sheet( $data ), ( $data['donator'] == 'new' ) ? 0 : 1 );

    if( $is_admin_message_sent && $is_user_message_sent ){
        wp_die( get_permalink( 1581 ) );
    }

    wp_die();
}