<?php
//client details
$config['stripe_key_test_public'] = 'pk_test_ERLVPgOBDi4sUTXhpjJtxRQe00jWjjayEE';

$config['stripe_key_test_secret'] = 'sk_test_PPXmodMIiKTZIGm6v6vzvBBc004taCnBxj';

$config['stripe_key_live_public'] = '';

$config['stripe_key_live_secret'] = '';

$config['stripe_verify_ssl']=False;

$config['stripe_test_mode']  = True;

$stripe = new Stripe( $config ); // Create the library object


