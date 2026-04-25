<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
  /**
   * Boot function para generar el UUID automáticamente al crear un registro.
   */
  protected static function bootHasUuid()
  {
      static::creating(function ($model) {
          if (empty($model->{$model->getKeyName()})) {
              $model->{$model->getKeyName()} = (string) Str::uuid();
          }
      });
  }

  /**
   * Desactivamos el incremento automático para usar UUID.
   */
  public function getIncrementing()
  {
      return false;
  }

  /**
   * Definimos que el tipo de llave primaria es string.
   */
  public function getKeyType()
  {
      return 'string';
  }
}