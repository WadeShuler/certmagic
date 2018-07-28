#!/usr/bin/env php
<?php
define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/config.php';

function terminate($message) {
    die($message . PHP_EOL);
}

function say($message) {
    echo $message . PHP_EOL;
}

if ( ! file_exists(ROOT_DIR . '/bin/_template.conf') ) {
    terminate('Template Config Missing!');
}

$template = file_get_contents(ROOT_DIR . '/bin/_template.conf');

foreach ($config as $key => $val)
{
    if ( ! is_array($val) )
    {

        if ( $key === 'common_name' ) {
            $template = str_replace('{{REPLACE_' . strtoupper($key) . '}}', 'CertMagic: ' . $val, $template);
        } else {
            $template = str_replace('{{REPLACE_' . strtoupper($key) . '}}', $val, $template);
        }

    } else {

        if ( $key === 'domains' )
        {
            $i = 1;
            $domainText = '';

            foreach ( $val as $domain )
            {
                if ( $domain === 'localhost' ) {
                    $domainText .= "DNS.$i = $domain" . PHP_EOL;
                } else {
                    $domainText .= "DNS.$i = $domain" . PHP_EOL;
                    $i++;
                    $domainText .= "DNS.$i = *.$domain" . PHP_EOL;
                }

                $i++;
            }

            $template = str_replace('{{REPLACE_' . strtoupper($key) . '}}', $domainText, $template);
        }
    }
}

file_put_contents(ROOT_DIR . '/tmp/generated.conf', $template, LOCK_EX);

say('Config Generated!');
