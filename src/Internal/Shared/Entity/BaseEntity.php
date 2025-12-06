<?php

namespace Internal\Shared\Entity;

use Illuminate\Database\Eloquent\Model;

abstract class BaseEntity extends Model
{
    protected $hidden = ['deleted_at', 'user_who_deleted_id'];
    
    // Agregar métodos comunes para todas las entidades
}