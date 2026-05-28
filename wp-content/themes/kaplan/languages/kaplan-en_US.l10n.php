<?php
/**
 * WP_Textdomain_Registry bu dosyanın VARLIĞINI arar ($domain-$locale.l10n.php),
 * ama JIT loader tema kendi languages/ dizininde çeviriyi {locale}.l10n.php
 * (en_US.l10n.php) adından yükler. Tek kaynak: en_US.l10n.php.
 *
 * @package Kaplan
 */
return require __DIR__ . '/en_US.l10n.php';
