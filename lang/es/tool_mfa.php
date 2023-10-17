<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_mfa', language 'es', version '4.2'.
 *
 * @package     tool_mfa
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['achievedweight'] = 'Peso alcanzado';
$string['areyousure'] = '¿Está seguro de que quiere revocar el factor?';
$string['combination'] = 'Combinación';
$string['connector'] = 'Y';
$string['created'] = 'Creada';
$string['createdfromip'] = 'Creada desde la IP';
$string['debugmode:currentweight'] = 'Peso actual: {$a}';
$string['debugmode:heading'] = 'Modo depuración';
$string['devicename'] = 'Dispositivo';
$string['email:subject'] = 'No es posible iniciar sesión en {$a}';
$string['enablefactor'] = 'Habilitar factor';
$string['error:actionnotfound'] = 'Acción \'{$a}\' no soportada';
$string['error:directaccess'] = 'Esta página no debería ser accedida directamente';
$string['error:factornotenabled'] = 'El factor MFA \'{$a}\' no está habilitado';
$string['error:factornotfound'] = 'No se encuentra el factor MFA \'{$a}\'';
$string['error:home'] = 'Haga clic para volver a la página de inicio.';
$string['error:notenoughfactors'] = 'No es posible autenticar';
$string['error:reauth'] = 'No pudimos confirmar su identidad para cumplir la política de autenticación de este sitio. Si se ha saltado algún factor, puede intentarlo de nuevo, o por favor póngase en contacto con el administrador del sitio.';
$string['error:revoke'] = 'No se puede revocar el factor';
$string['error:setupfactor'] = 'No se puede configurar el factor';
$string['error:support'] = 'Si aún no puede iniciar sesión o cree que está ante un error, por favor envíe un correo electrónico a la siguiente dirección para obtener soporte:';
$string['error:wrongfactorid'] = 'El id de factor \'{$a}\' no es correcto';
$string['event:userpassedmfa'] = 'Verificación exitosa';
$string['event:userrevokedfactor'] = 'Revocación del factor';
$string['event:usersetupfactor'] = 'Configuración del factor';
$string['factor'] = 'Factor';
$string['factorreport'] = 'Informe de todos los factores';
$string['factorrevoked'] = 'El factor \'{$a}\' fue revocado con éxito.';
$string['factorsetup'] = 'El factor \'{$a}\' fue configurado con éxito.';
$string['fallback'] = 'Factor alternativo';
$string['fallback_info'] = 'Este factor es una alternativa, en caso de no configurarse otros factores. Este factor siempre fallará.';
$string['gotourl'] = 'Ir a su URL original:';
$string['inputrequired'] = 'Entrada de usuario';
$string['lastverified'] = 'Último verificado';
$string['lockoutnotification'] = 'Tiene {$a} intentos de verificación más.';
$string['mfa'] = 'MFA';
$string['mfa:mfaaccess'] = 'Interactuar con MFA';
$string['mfareports'] = 'Informes MFA';
$string['mfasettings'] = 'Gestionar MFA';
$string['na'] = 'n/d';
$string['overall'] = 'En general';
$string['pending'] = 'Pendiente';
$string['pluginname'] = 'Autenticación multifactor';
$string['preferences:activefactors'] = 'Factores activos';
$string['preferences:availablefactors'] = 'Factores disponibles';
$string['preferences:header'] = 'Preferencias de multifactor de autenticación';
$string['privacy:metadata:tool_mfa'] = 'Datos con configuración de factores MFA';
$string['privacy:metadata:tool_mfa:createdfromip'] = 'IP desde donde se configuró el factor';
$string['privacy:metadata:tool_mfa:factor'] = 'Tipo de factor';
$string['privacy:metadata:tool_mfa:id'] = 'ID de registro';
$string['privacy:metadata:tool_mfa:label'] = 'etiqueta para la instancia del factor, ej: dispositivo o correo';
$string['privacy:metadata:tool_mfa:lastverified'] = 'Hora en que el usuario fue verificado con este factor';
$string['privacy:metadata:tool_mfa:secret'] = 'Cualquier dato secreto para el factor';
$string['privacy:metadata:tool_mfa:timecreated'] = 'Hora en que el factor fue configurado';
$string['privacy:metadata:tool_mfa:timemodified'] = 'Hora en que el factor fue modificado';
$string['privacy:metadata:tool_mfa:userid'] = 'ID del usuario al que pertenece ese factor';
$string['redirecterrordetected'] = 'Se detectó una redirección no soportada, la ejecución del script terminó. El error de redirección fue entre MFA y {$a}.';
$string['revoke'] = 'Revocar';
$string['revokefactor'] = 'Revocar factor';
$string['settings:combinations'] = 'Resumen de condiciones buenas para el inicio de sesión';
$string['settings:debugmode'] = 'Habilitar modo depuración';
$string['settings:debugmode_help'] = 'El modo depuración mostrará un pequeño aviso con información de los factores habilitados actualmente, tanto en las páginas de administración de MFA como en la página de preferencias del usuario.';
$string['settings:enabled'] = 'Plugin MFA habilitado';
$string['settings:enablefactor'] = 'Habilitar factor';
$string['settings:enablefactor_help'] = 'Habilite esta opción para permitir que el factor se utilice en la autenticación MFA';
$string['settings:general'] = 'Configuración general de MFA';
$string['settings:lockout'] = 'Umbral de bloqueo';
$string['settings:lockout_help'] = 'Cantidad de intentos que un usuario tiene para responder un factor antes de que se le prohíba el ingreso.';
$string['settings:weight'] = 'Peso del factor';
$string['settings:weight_help'] = 'El peso de este factor si es superado. Un usuario necesita al menos 100 puntos para realizar el inicio de sesión.';
$string['setup'] = 'Configuración';
$string['setupfactor'] = 'Configuración de factor';
$string['setuprequired'] = 'Configuración de usuario';
$string['state:fail'] = 'Fallido';
$string['state:neutral'] = 'Neutral';
$string['state:pass'] = 'Superado';
$string['state:unknown'] = 'Desconocido';
$string['totalweight'] = 'Peso total';
$string['weight'] = 'Peso';
