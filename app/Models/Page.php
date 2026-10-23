<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages'; // table name

    protected $fillable = [
        'page_name',
        'page_type',
        'description',
        'status'
    ];

    /**
     * Clean and strip excessive spacing/newlines from the description HTML
     */
    public function getCleanDescriptionAttribute(): string
    {
        $html = $this->description ?? '';
        
        // 1. Remove excessive spaces/newlines between HTML tags
        $html = preg_replace('/>\s{2,}</', '><', $html);
        
        // 2. Remove empty paragraphs or paragraphs containing only whitespace/&nbsp;
        $html = preg_replace('/<p>\s*(&nbsp;)?\s*<\/p>/i', '', $html);
        
        // 3. Replace multiple consecutive <br> tags with a single <br>
        $html = preg_replace('/(<br\s*\/?>\s*){2,}/i', '<br>', $html);
        
        // 4. Remove consecutive newlines (3 or more) to collapse spacing
        $html = preg_replace('/(\r?\n){3,}/', "\n\n", $html);
        
        return trim($html);
    }
}