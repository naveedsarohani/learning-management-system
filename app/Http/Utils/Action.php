<?php

namespace App\Http\Utils;

enum Action: string
{
    case CREATE_USER = 'create_user';
    case UPDATE_USER = 'update_user';
    case DELETE_INSTRUCTOR = 'delete_instructor';
    case DELETE_STUDENT = 'delete_student';
}
