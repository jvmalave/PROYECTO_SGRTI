<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| El archivo Pest.php sirve para configurar tus grupos de pruebas.
| Aquí vinculamos la clase TestCase de Laravel con tus pruebas de Feature.
|
*/

uses(Tests\TestCase::class)->in('Feature');



/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| Aquí puedes definir "macros" de expectativas personalizadas si lo deseas.
|
*/

// Ejemplo: expect($id)->toBeUuid();