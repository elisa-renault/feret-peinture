<?php
if ( ! defined('WP_CLI') || ! WP_CLI || ! fp_is_preview() ) { exit(1); }
$check = function($ok, $message) { if (!$ok) { throw new RuntimeException($message); } WP_CLI::log('PASS: '.$message); };
$check(!fp_captcha_verify(''), 'Missing proof rejected');
$check(!fp_captcha_verify('junk'), 'Malformed proof rejected');
$challenge = fp_captcha_challenge();
$solution = fp_captcha_service()->solveChallenge(new \AltchaOrg\Altcha\SolveChallengeOptions($challenge, new \AltchaOrg\Altcha\Algorithm\Pbkdf2()));
$check((bool)$solution, 'Real challenge solved');
$payload = new \AltchaOrg\Altcha\Payload($challenge,$solution);
$raw=$payload->toBase64();
$signature=fp_captcha_verify($raw);
$check((bool)$signature, 'Valid proof accepted');
$bad=$payload->toArray(); $bad['challenge']['parameters']['expiresAt']=time()+99999;
$check(!fp_captcha_verify(base64_encode(json_encode($bad))), 'Changed expiry rejected');
$bad=$payload->toArray(); $bad['solution']['derivedKey']=str_repeat('0',64);
$check(!fp_captcha_verify(base64_encode(json_encode($bad))), 'Forged solution rejected');
$expired=fp_captcha_service()->createChallenge(new \AltchaOrg\Altcha\CreateChallengeOptions(algorithm:new \AltchaOrg\Altcha\Algorithm\Pbkdf2(),cost:1000,expiresAt:time()-1));
$check(!fp_captcha_verify((new \AltchaOrg\Altcha\Payload($expired,$solution))->toBase64()),'Expired challenge rejected');
$check(fp_captcha_consume($signature), 'First use accepted');
$check(!fp_captcha_consume($signature), 'Replay rejected');
WP_CLI::success('CAPTCHA verified without sending email.');
