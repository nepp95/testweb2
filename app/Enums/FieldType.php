<?php

namespace App\Enums;

enum FieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Markdown = 'markdown';
    case Email = 'email';
    case Select = 'select';
    case Checkbox = 'checkbox';
    case SectionLink = 'section_link';
    case ContactSubject = 'contact_subject';
}
