<?php 

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Base {

	 /**
     * Encrypt text data
     **/
    protected function _encrypt( $text ) {
        if  ( function_exists( 'mcrypt_encrypt' ) ) {
            return base64_encode( @mcrypt_encrypt( MCRYPT_RIJNDAEL_256, 'E0fRGYRjiMlAZsubeDYzPfpJHuUjRWigHuSRRFkm1wKPCrjeKM', $text, MCRYPT_MODE_ECB ) );
        } else {
            return $text;
        }
    }

    /**
     * Decrypt Text Data
     **/
    protected function _decrypt( $text ) {
        if ( function_exists( 'mcrypt_decrypt' ) ) {
            return trim( @mcrypt_decrypt( MCRYPT_RIJNDAEL_256, 'E0fRGYRjiMlAZsubeDYzPfpJHuUjRWigHuSRRFkm1wKPCrjeKM', base64_decode( $text ), MCRYPT_MODE_ECB ) );
        } else {
            return $text;
        }
    }

}