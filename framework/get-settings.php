<?php
function seed_csp4_get_settings(){
    $settings = get_option('seed_csp4_settings');
    return apply_filters( 'seed_csp4_get_settings', $settings );;
}
