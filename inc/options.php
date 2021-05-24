<?php

add_action( 'admin_menu', function(){
    add_menu_page(
        __( 'DV Nastavení', 'dv' ),
        __( 'DV Nastavení', 'dv' ),
        'manage_options',
        'dv-settings',
        'dv_admin_page_settings',
        "data:image/svg+xml,%3Csvg width='17' height='20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M16.71 2.55a2.55 2.55 0 01-4 2.1 13.58 13.58 0 012 6.16c.57 7.2-4.54 9.3-7.41 9.19C3.72 20-.35 16.96.02 10.8.47 3.37 4.82.9 6.93.9c1.76 0 3.45.92 4.8 2.45a2.55 2.55 0 114.98-.79zM11.9 3.73a4.27 4.27 0 00-2.35-1.1c-4.19-.56-5.8 5.2-4.65 8.14a2.56 2.56 0 012.48-3.13 2.55 2.55 0 11-1.54 4.58c1.65 1.52 4.23.3 5.56-1.4 1.63-2.08 1.92-4.03 1.49-5.46-.1-.31-.23-.6-.39-.87a2.56 2.56 0 01-.6-.76z' fill='%23EF95AB'/%3E%3C/svg%3E",
        2
    );
} );

function dv_admin_page_settings(){ ?>
    <div class="dv-wrapper">
        <div class="dv-section">
            <header class="dv-section-header">
                <h1><?=__( 'DV Nastavení', 'dv' ); ?></h1>
            </header>
            <main class="dv-section-body">
                <div style="margin-bottom: 2em;">
                    <b><?=__( 'E-mail klienta ke sdílení listu:', 'dv' ); ?></b><br>
                    <span>sheets@egg-donation.iam.gserviceaccount.com</span>
                </div>
                <form class="dv-form">
                    <div class="dv-field">
                        <label for="recipients"><?=__( 'Příjemci oznámení (oddělené čárkou)', 'dv' ); ?></label>
                        <input type="text" name="recipients" id="recipients" value="<?=get_option( 'recipients' ); ?>">
                    </div>

                    <div class="dv-field">
                        <label for="google-sheet"><?=__( 'Google Sheet ID', 'dv' ); ?></label>
                        <input type="text" name="google_sheet_id" id="google-sheet" value="<?=get_option( 'google_sheet_id' ); ?>">
                    </div>

                    <input type="hidden" name="action" value="dv_save_plugin_settings">

                    <button type="submit" class="dv-button"><?=__( 'Uložit', 'dv' ); ?></button>
                </form>
            </main>
        </div>
    </div>
    <style>
    .dv-wrapper {
        max-width: 900px;
        padding: 20px 20px 20px 0;
        color: #000000;
        font-size: 16px;
        line-height: 1.4;
    }

    .dv-wrapper *,
    .dv-wrapper *:before,
    .dv-wrapper *:after {
        box-sizing: border-box;
    }


    .dv-section {
        border-radius: 0 32px;
        overflow: hidden;
    }


    .dv-section-header {
        padding: 16px 24px;
        background: #EF95AB;
    }

    .dv-section-header h1 {
        margin: 0;
        font-size: 1.5em;
    }


    .dv-section-body {
        padding: 24px;
        background: #FFFFFF;
    }


    .dv-button {
        position: relative;
        display: inline-block;
        flex-shrink: 0;
        padding: 0 2em;
        color: #000000;
        font-weight: 600;
        font-size: 1rem;
        line-height: 48px;
        background: #EF95AB;
        border: 0;
        border-radius: 0 16px;
        box-shadow: 0 8px 8px -4px #ef95ab88;
        transition: all 300ms ease !important;
        z-index: 2;
        cursor: pointer;
    }

    .dv-button:not(:disabled):hover {
        opacity: 0.75;
    }

    .dv-button:disabled {
        cursor: not-allowed;
    }


    .dv-form {
        position: relative;
    }

    .dv-form.loading:before {
        content: "";
        position: absolute;
        display: block;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(255,255,255,.5);
        z-index: 3;
    }

    .dv-form.loading:after {
        content: "";
        position: absolute;
        display: block;
        top: calc(50% - 1em);
        left: calc(50% - 1em);
        width: 2em;
        height: 2em;
        border: 4px solid #7D1128;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 4;
    }


    .dv-field {
        margin-bottom: 16px;
    }

    .dv-field > label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .dv-field > input,
    .dv-field > textarea {
        display: block;
        width: 100%;
        font-size: 1em;
        border-radius: 0;
        border-color: #c1c1c1;
        resize: none;
        transition: border-color 300ms ease;
    }

    .dv-field > input:focus,
    .dv-field > textarea:focus {
        border-color: #EF95AB;
        box-shadow: none;
        outline: none;
    }

    .dv-field > input {
        padding: 0 1em;
        line-height: 48px;
    }

    .dv-field > textarea {
        padding: 1em;
    }


    @keyframes spin {
        from {
            transform: rotateZ(0deg);
        }

        to {
            transform: rotateZ(360deg);
        }
    }
    </style>
    <script>
        jQuery(document).ready(function($){
            $('form').on('submit', function(){
                let $form = $(this);
                let form_data = new FormData($form[0]);

                $.ajax({
                    url: '<?=admin_url( 'admin-ajax.php' ); ?>',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    beforeSend: function(){
                        $form.addClass('loading');
                        $form.find('button[type="submit"]').addClass('loading').attr('disabled', true);
                    },
                    success: function(response){
                        $form.removeClass('loading');
                        $form.find('button[type="submit"]').removeClass('loading').attr('disabled', false);
                    }
                });

                return false;
            });
        });
    </script>
    <?php
}